<?php
define('ROOT_DIR', __DIR__.'/');
require_once ROOT_DIR.'_config.php';
require_once ROOT_DIR.'_config2.php';
header('Content-Type: text/html; charset='.$config['charset']);

$resultJson = '';
$imageData  = '';
$httpCode   = null;
$respHeaders= '';
$debugLog   = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $prompt = trim(@$_POST['prompt']);
    if ($prompt!=='') {
        $engine = isset($config['stability_engine']) ? $config['stability_engine'] : 'stable-diffusion-xl-1024-v1-0';
        $apiUrl = 'https://api.stability.ai/v1/generation/' . $engine . '/text-to-image';
        $payload = json_encode(array(
            'cfg_scale' => 7,
            'height' => 1024,
            'width'  => 1024,
            'samples'=> 1,
            'steps'  => 30,
            'text_prompts' => array(array('text'=>$prompt,'weight'=>1))
        ));
        // Заголовки запроса
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.$config['stability_api_key']
        );

        // Используем cURL, чтобы получить точный статус/заголовки/ошибки
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true); // включаем заголовки в ответ
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $info  = curl_getinfo($ch);
        $httpCode = isset($info['http_code']) ? (int)$info['http_code'] : null;
        $headerSize = isset($info['header_size']) ? (int)$info['header_size'] : 0;
        if ($response !== false) {
            $respHeaders = substr($response, 0, $headerSize);
            $resBody = substr($response, $headerSize);
            $resultJson = $resBody;
            $data = json_decode($resBody,true);
            if (isset($data['artifacts'][0]['base64'])) {
                $imageData = $data['artifacts'][0]['base64'];
            }
        }
        curl_close($ch);

        // Логи
        $debugLog = json_encode(array(
            'request' => array(
                'url' => $apiUrl,
                'headers' => $headers,
                'payload' => json_decode($payload,true),
            ),
            'curl' => array(
                'errno' => $errno,
                'error' => $error,
                'info'  => $info,
            ),
            'http' => array(
                'code' => $httpCode,
                'response_headers' => $respHeaders,
                'body_sample' => substr(isset($resultJson)?$resultJson:'',0,2000)
            )
        ), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Test: Stability.ai</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 24px; }
        .row { margin: 12px 0; }
        label { display:block; font-weight:600; margin-bottom:6px; }
        textarea { width:100%; min-height:120px; padding:10px; border:1px solid #ccc; border-radius:6px; }
        button { padding:10px 16px; border:0; background:#007bff; color:#fff; border-radius:6px; cursor:pointer; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
        img { max-width:100%; height:auto; border:1px solid #eee; border-radius:6px; }
        pre { background:#f7f7f7; padding:12px; border-radius:6px; overflow:auto; }
    </style>
</head>
<body>
    <h1>Тест Stability.ai (прямой запрос)</h1>
    <div class="grid">
        <div>
            <form method="post">
                <div class="row">
                    <label for="prompt">Промпт</label>
                    <textarea id="prompt" name="prompt" placeholder="Опишите желаемое изображение"><?php echo isset($_POST['prompt'])?htmlspecialchars($_POST['prompt']):''; ?></textarea>
                </div>
                <div class="row">
                    <button type="submit">Сгенерировать</button>
                </div>
            </form>
        </div>
        <div>
            <div class="row">
                <label>HTTP код</label>
                <pre><?php echo htmlspecialchars((string)$httpCode); ?></pre>
            </div>
            <div class="row">
                <label>Заголовки ответа</label>
                <pre><?php echo htmlspecialchars($respHeaders); ?></pre>
            </div>
            <div class="row">
                <label>Ответ API (raw)</label>
                <pre><?php echo htmlspecialchars($resultJson); ?></pre>
            </div>
            <div class="row">
                <label>Логи запроса</label>
                <pre><?php echo htmlspecialchars($debugLog); ?></pre>
            </div>
            <div class="row">
                <label>Сгенерированное изображение</label>
                <?php if ($imageData): ?>
                    <img src="data:image/png;base64,<?php echo $imageData; ?>" alt="result" />
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

