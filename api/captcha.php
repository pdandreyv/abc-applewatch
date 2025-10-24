<?php

//not_visible
// Эндпоинт для скрытой капчи (используется формой входа в админку)
// Не требует API-ключа

// Сессия уже запускается в _config2.php через api/index.php

$len = isset($_GET['len']) ? max(4, min(8, (int)$_GET['len'])) : 6;
$min = (int)pow(10, $len - 1);
$max = (int)pow(10, $len) - 1;
$code = random_int($min, $max);

$_SESSION['captcha'] = $code;

header('Content-Type: text/plain; charset=UTF-8');
echo $code;
exit;


