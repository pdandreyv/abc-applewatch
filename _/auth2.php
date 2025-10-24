<?php

error_reporting(E_ALL);

$social = array('vk','facebook','google','yandex','mailru');

$config['http'] = 'http';
$config['domain'] = $_SERVER['HTTP_HOST'];
$config['http_domain'] = $config['http'].'://'.$config['domain'];

foreach ($social as $k=>$v) {
	//урл для редиректа
	$redirect = urlencode($config['http_domain'].'/_/auth2.php');
	echo '<br><a href="http://auth.abc-cms.com/' . $v . '/?redirect='.$redirect.'">'.$v.'</a>';
}
echo '<br>';

if (@$_GET['code'] AND @$_GET['type']) {
	//print_r($_GET);
	//echo 'http://auth.abc-cms.com/'.$_GET['type'].'/?go=1&code='.$_GET['code'];
	$data =  file_get_contents('http://auth.abc-cms.com/'.$_GET['type'].'/?go=1&code='.$_GET['code']);
	//данные пользователя
	if ($data) {
		//echo $data;
		$data = json_decode($data,true);
		echo '<pre>';
		print_r($data);
	}
}