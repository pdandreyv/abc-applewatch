<?php

// Создание записи генерации для пользователя
/*
 * v1.0.0
 * example: /api/user/generation_create?key=secret_key&apple_id={apple_id}&style_id={style_id}&color_id={color_id}&prompt={text}
 * example_response:
 * {
 *   "success": 1,
 *   "id": 123,
 *   "message": "created",
 *   "img_url": "http://example.com/files/user_generation/abc/123.png",
 *   "final_prompt": "..."
 * }
 */

include_once __DIR__.'/../_guard.php';

// входные параметры
$appleId = isset($_REQUEST['apple_id']) ? trim((string)$_REQUEST['apple_id']) : '';
//$styleId = isset($_REQUEST['style_id']) ? intval($_REQUEST['style_id']) : 0;
//$colorId = isset($_REQUEST['color_id']) ? intval($_REQUEST['color_id']) : 0;
$prompt  = isset($_REQUEST['prompt']) ? trim((string)$_REQUEST['prompt']) : '';

// логируем вход (без чувствительных данных)
log_add('generation_create.log', [
    'stage' => 'input',
    'apple_id' => substr($appleId, 0, 6) . (strlen($appleId) > 6 ? '***' : ''),
    //'style_id' => $styleId,
    //'color_id' => $colorId,
    'prompt_len' => strlen($prompt),
]);

if ($appleId === '') {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
    $api['message'] = 'apple_id are required';
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
    'style_id'  => 1,//$styleId,
    'color_id'  => 1,//$colorId,
    'prompt'    => $prompt,
    'img'       => '',
    'created_at'=> $config['datetime'],
);

// соберём общий промпт: prompt + prompts из стиля и цвета
//$style = mysql_select("SELECT id, name, prompt FROM styles WHERE id='".intval($styleId)."'", 'row');
//$color = $colorId ? mysql_select("SELECT id, name, code, prompt FROM colors WHERE id='".intval($colorId)."'", 'row') : null;

$parts = array();

// дополнительный промпт из админки
$extraPrompt = isset($config['user_generation_prompt']) ? trim((string)$config['user_generation_prompt']) : '';
if ($extraPrompt!=='') $parts[] = $extraPrompt;

if ($prompt!=='') $parts[] = $prompt;
//if ($style && trim($style['prompt'])!='') $parts[] = trim($style['prompt']);
//if ($color && trim($color['prompt'])!='') $parts[] = trim($color['prompt']);

$finalPrompt = trim(implode('. ', $parts));

log_add('generation_create.log', [
    'stage' => 'final_prompt',
    'final_prompt' => mb_substr($finalPrompt, 0, 200),
]);

// создаём запись, чтобы получить id для пути файла
// запись создадим только после успешной генерации изображения

// вызов Stability.ai (text-to-image)
$imageFile = '';
$imgPublicUrl = '';
// для возврата причины ошибки ИИ
$aiHttpCode = 0;
$aiError = '';
if (!empty($config['stability_api_key'])) {
	// Возвращаемся к v1 text-to-image (даёт более детальные результаты)
	$engine = isset($config['stability_engine']) ? $config['stability_engine'] : 'stable-diffusion-xl-1024-v1-0';
	$apiUrl = 'https://api.stability.ai/v1/generation/'.$engine.'/text-to-image';
	$negativePrompt = 'lowres, blurry, bad anatomy, bad proportions, deformed, disfigured, extra fingers, extra limbs, text, watermark, signature, jpeg artifacts, cropped, out of frame, ugly, duplicate, morbid, mutilated, poorly drawn';
	$payload = json_encode(array(
		'cfg_scale' => 8,
		'height' => 1024,
		'width' => 1024,
		'samples' => 1,
		'steps' => 40,
		'text_prompts' => array(
			array('text' => $finalPrompt, 'weight' => 1),
			array('text' => $negativePrompt, 'weight' => -1),
		),
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
		$decoded = null;
		$data = json_decode($respBody, true);
		if (is_array($data)) {
			// JSON ответ
			if (isset($data['artifacts'][0]['base64'])) {
				$decoded = base64_decode($data['artifacts'][0]['base64']);
			} elseif (isset($data['image'])) {
				$decoded = base64_decode($data['image']);
			} elseif (isset($data['images'][0])) {
				$decoded = base64_decode($data['images'][0]);
			}
			log_add('generation_create.log', [
				'stage' => 'json_response',
				'has_image' => $decoded!==false && $decoded!==null ? 1 : 0,
			]);
		} else {
			// бинарный ответ (image/png, image/jpeg)
			$decoded = $respBody;
			log_add('generation_create.log', [
				'stage' => 'binary_response',
				'len' => strlen($respBody),
			]);
		}
		if ($decoded!==false && $decoded!==null && strlen($decoded)>100) {
			$tmp = tempnam(sys_get_temp_dir(), 'gen');
			file_put_contents($tmp, $decoded);
			// создаём запись только сейчас, чтобы получить id для имени файла
			$row['id'] = mysql_fn('insert','user_generation',$row);
			// сохраняем в файловую систему проекта (отдельная папка на каждую генерацию, чтобы не удалять предыдущие)
			$appleFolder = preg_replace('/[^0-9A-Za-z_\-]/','', (string)$appleId);
			$genFolder = intval($row['id']);
			$root = ROOT_DIR.'files/user_generation/'.$appleFolder.'/'.$genFolder.'/';
			$file = 'image.png';
			include_once(ROOT_DIR.'functions/file_func.php');
			$copied = copy2($tmp, $root, $file, array(''=>'resize 512x512'));
			log_add('generation_create.log', [
				'stage' => 'save_image',
				'root' => $root,
				'file' => $file,
				'copied' => $copied ? 1 : 0,
			]);
			if ($copied) {
				// сохраняем относительный путь с папкой apple_id/ID генерации
				$imageFile = $appleFolder.'/'.$genFolder.'/'.$file;
				// дописываем имя файла в запись
				mysql_fn('update','user_generation', array(
					'id' => $row['id'],
					'img'=> $imageFile,
				));
				// сразу списываем 1 доступную генерацию
				mysql_fn('query', "UPDATE users SET count_generation = GREATEST(count_generation - 1, 0), updated_at = NOW() WHERE id = '".intval($user['id'])."'");
				// абсолютный публичный URL
				$domain = (isset($config['https']) && intval($config['https'])===1 ? 'https' : 'http').'://'.$config['domain'];
				$imgPublicUrl = $domain.'/files/user_generation/'.$imageFile;
			} else {
				// если не удалось сохранить — откатываем созданную запись
				mysql_fn('delete','user_generation', $row['id']);
				unset($row['id']);
			}
			@unlink($tmp);
		} else {
			log_add('generation_create.log', [
				'stage' => 'parse_response_error',
				'body_head' => substr($respBody, 0, 400),
			]);
			// попытаемся достать текст ошибки из ответа
			$errJson = json_decode($respBody, true);
			if (is_array($errJson) && isset($errJson['message'])) {
				$aiError = (string)$errJson['message'];
			} elseif (is_array($errJson) && isset($errJson['errors'][0])) {
				$aiError = (string)$errJson['errors'][0];
			}
		}
	} else {
		// неуспешный HTTP ответ или ошибка curl
		$errJson = json_decode($respBody ?? '', true);
		if (is_array($errJson) && isset($errJson['message'])) {
			$aiError = (string)$errJson['message'];
		} elseif (is_array($errJson) && isset($errJson['errors'][0])) {
			$aiError = (string)$errJson['errors'][0];
		} elseif ($curlErr!=='') {
			$aiError = $curlErr;
		}
	}
} else {
    log_add('generation_create.log', [
        'stage' => 'no_api_key',
        'message' => 'config[stability_api_key] is empty',
    ]);
}

// формируем ответ
if ($imageFile!=='') {
	$api['success'] = 1;
	$api['id'] = intval($row['id']);
	$api['message'] = 'created_with_image';
	$api['img_url'] = $imgPublicUrl;
	$api['final_prompt'] = $finalPrompt;
} else {
	$api['success'] = 0;
	$api['error'] = 'ai_generation_failed';
	$api['message'] = ($aiError!=='') ? $aiError : 'image not generated';
	$api['ai_http_code'] = $aiHttpCode;
	$api['final_prompt'] = $finalPrompt;
}



