<?php

/*
 * v1.4.17 - сокращение параметров form
 */

if (isset($_POST['text'])) {
	$text = stripslashes_smart($_POST['text']);
	$fp = fopen(ROOT_DIR.'robots.txt','w');
	$content.= fwrite($fp,$text)>=0 ? '<br />файл обновлен!' : '<br />ошибка записи!';
	fclose($fp);
	$data = array();
	$data['error']	= '';
	echo '<textarea>'.json_encode($data, JSON_HEX_AMP).'</textarea>';
	die();
}

$handle = fopen(ROOT_DIR.'robots.txt', "r");
$text = '';
if ($handle) {
	while (($buffer = fgets($handle, 4096)) !== false) $text.= $buffer;
	fclose($handle);
}

$module['one_form'] = true;

$form[] = array('textarea td12','text',array(
	'value'=>$text,
	'name'=>' ',
	'attr'=>'style="height:300px"'
));