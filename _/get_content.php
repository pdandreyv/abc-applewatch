<?php

/*файл для прокси запросов
//урл прокси скрипта
$proxy = 'http://demo.abc-cms.com/_/get_content.php';
//урл для запросов
$request = 'https://api.telegram.org/';
$result = file_get_contents ($proxy.'?url='.base64_encode($request));
*/

error_reporting(0);
$url = base64_decode(@$_GET['url']);
echo file_get_contents($url);