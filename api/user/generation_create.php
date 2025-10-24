<?php

// Создание записи генерации для пользователя
/*
 * v1.0.0
 */

include_once __DIR__.'/../_guard.php';

// входные параметры
$appleId = isset($_REQUEST['apple_id']) ? trim((string)$_REQUEST['apple_id']) : '';
$styleId = isset($_REQUEST['style_id']) ? intval($_REQUEST['style_id']) : 0;
$colorId = isset($_REQUEST['color_id']) ? intval($_REQUEST['color_id']) : 0;
$prompt  = isset($_REQUEST['prompt']) ? trim((string)$_REQUEST['prompt']) : '';

// логируем вход (без чувствительных данных)
log_add('generation_create.log', [
    'stage' => 'input',
    'apple_id' => substr($appleId, 0, 6) . (strlen($appleId) > 6 ? '***' : ''),
    'style_id' => $styleId,
    'color_id' => $colorId,
    'prompt_len' => strlen($prompt),
]);

if ($appleId === '' || $styleId<=0) {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
    $api['message'] = 'apple_id and style_id are required';
    return;
}

// ищем пользователя по apple_id
$user = mysql_select("SELECT id, count_generation FROM users WHERE apple_id='".mysql_res($appleId)."' LIMIT 1", 'row');
if (!$user) {
    $api['success'] = 0;
    $api['error'] = 'not_found';
    $api['message'] = 'user not found';
    return;
}

// проверка доступного количества генераций
if (intval($user['count_generation']) <= 0) {
    $api['success'] = 0;
    $api['error'] = 'no_credits';
    $api['message'] = 'нет проплаченных генераций — оплатите один из пакетов';
    return;
}

// создаём запись
global $config;
$row = array(
    'user_id'   => intval($user['id']),
    'style_id'  => $styleId,
    'color_id'  => $colorId,
    'prompt'    => $prompt,
    'img'       => '',
    'created_at'=> $config['datetime'],
);

// соберём общий промпт: prompt + prompts из стиля и цвета
$style = mysql_select("SELECT id, name, prompt FROM styles WHERE id='".intval($styleId)."'", 'row');
$color = $colorId ? mysql_select("SELECT id, name, code, prompt FROM colors WHERE id='".intval($colorId)."'", 'row') : null;

$parts = array();
if ($style && trim($style['prompt'])!='') $parts[] = trim($style['prompt']);
if ($color && trim($color['prompt'])!='') $parts[] = trim($color['prompt']);
if ($prompt!=='') $parts[] = $prompt;
$finalPrompt = trim(implode('. ', $parts));
if ($finalPrompt==='') $finalPrompt = 'Apple Watch wallpaper';

log_add('generation_create.log', [
    'stage' => 'final_prompt',
    'final_prompt' => mb_substr($finalPrompt, 0, 200),
]);

// создаём запись, чтобы получить id для пути файла
$row['id'] = mysql_fn('insert','user_generation',$row);
// сразу списываем 1 доступную генерацию
mysql_fn('query', "UPDATE users SET count_generation = GREATEST(count_generation - 1, 0), updated_at = NOW() WHERE id = '".intval($user['id'])."'");

// вызов Stability.ai (text-to-image)
$imageFile = '';
$imgPublicUrl = '';
// для возврата причины ошибки ИИ
$aiHttpCode = 0;
$aiError = '';
if (!empty($config['stability_api_key'])) {
    $engine = isset($config['stability_engine']) ? $config['stability_engine'] : 'stable-diffusion-xl-1024-v1-0';
    $apiUrl = 'https://api.stability.ai/v1/generation/' . $engine . '/text-to-image';
    $payload = json_encode(array(
        'cfg_scale' => 7,
        'height' => 1024,
        'width' => 1024,
        'samples' => 1,
        'steps' => 30,
        'text_prompts' => array(array('text' => $finalPrompt, 'weight' => 1)),
    ));

    // cURL с подробным логированием
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.$config['stability_api_key'],
        ),
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 60,
    ));
    $resp = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $respHeaders = substr($resp, 0, $headerSize);
    $respBody = substr($resp, $headerSize);

    $aiHttpCode = $httpCode;

    log_add('generation_create.log', [
        'stage' => 'stability_request',
        'url' => $apiUrl,
        'http_code' => $httpCode,
        'curl_error' => $curlErr,
        'resp_body_head' => substr($respBody, 0, 400),
    ]);

    if ($curlErr==='' && $httpCode>=200 && $httpCode<300 && $respBody) {
        $data = json_decode($respBody, true);
        if (is_array($data) && isset($data['artifacts'][0]['base64'])) {
            $b64 = $data['artifacts'][0]['base64'];
            $decoded = base64_decode($b64);
            log_add('generation_create.log', [
                'stage' => 'base64_decode',
                'len' => strlen($b64),
                'decoded_len' => ($decoded!==false ? strlen($decoded) : 0),
            ]);
            if ($decoded!==false && strlen($decoded)>100) {
                $tmp = tempnam(sys_get_temp_dir(), 'gen');
                file_put_contents($tmp, $decoded);
                // сохраняем в файловую систему проекта
                $appleFolder = preg_replace('/[^0-9A-Za-z_\-]/','', (string)$appleId);
                $root = ROOT_DIR.'files/user_generation/'.$appleFolder.'/';
                $file = intval($row['id']).'.png';
                include_once(ROOT_DIR.'functions/file_func.php');
                $copied = copy2($tmp, $root, $file, array(''=>'resize 1000x1000'));
                log_add('generation_create.log', [
                    'stage' => 'save_image',
                    'root' => $root,
                    'file' => $file,
                    'copied' => $copied ? 1 : 0,
                ]);
                if ($copied) {
                    // сохраняем относительный путь с папкой apple_id
                    $imageFile = $appleFolder.'/'.$file;
                }
                @unlink($tmp);
            }
        } else {
            log_add('generation_create.log', [
                'stage' => 'parse_response_error',
                'body_head' => substr($respBody, 0, 400),
            ]);
            // попытаемся достать текст ошибки из ответа
            $errJson = json_decode($respBody, true);
            if (is_array($errJson) && isset($errJson['message'])) {
                $aiError = (string)$errJson['message'];
            }
        }
    }
} else {
    log_add('generation_create.log', [
        'stage' => 'no_api_key',
        'message' => 'config[stability_api_key] is empty',
    ]);
}

// обновляем запись именем файла, если получилось
if ($imageFile!=='') {
    mysql_fn('update','user_generation', array(
        'id' => $row['id'],
        'img'=> $imageFile,
    ));
    // абсолютный публичный URL
    $domain = (isset($config['https']) && intval($config['https'])===1 ? 'https' : 'http').'://'.$config['domain'];
    $imgPublicUrl = $domain.'/files/user_generation/'.$imageFile;
}
else {
    log_add('generation_create.log', [
        'stage' => 'no_image_saved',
        'id' => $row['id'],
    ]);
}

$api['success'] = 1;
$api['id'] = intval($row['id']);
$api['message'] = $imageFile!=='' ? 'created_with_image' : 'created';
$api['img_url'] = $imgPublicUrl;
$api['final_prompt'] = $finalPrompt;
if ($imageFile==='') {
    $api['ai_http_code'] = $aiHttpCode;
    if ($aiError!=='') $api['ai_error'] = $aiError;
}



