<?php

$path = $config['style'].'/images/';
$path = 'templates/images/';

$array = $array2 = array();
if ($handle = opendir(ROOT_DIR.$path)) {
	while (false !== ($f = readdir($handle)))
	if (strlen($f)>2) $array[$f] = $f;
	closedir($handle);
}
sort($array, SORT_LOCALE_STRING);
foreach ($array as $k=>$v) $array2[$v] = $v;
$array = $array2;

$file = isset($_GET['file']) ? $_GET['file'] : '';
if (!in_array($file,$array)) $file=key($array);


$message = '';
if (isset($_FILES['upload']['tmp_name'])) {
	$temp = $_FILES['upload']['tmp_name'];
	if (is_uploaded_file($temp)) {//проверка записался ли файл на сервер во временную папку
		move_uploaded_file($temp,ROOT_DIR.$path.$_FILES['upload']['name']);
		$file = $_FILES['upload']['name'];
	}
}

if (isset($_GET['delete'])) {
	if (is_file(ROOT_DIR.$path.$_GET['delete']))
		unlink(ROOT_DIR.$path.$_GET['delete']);
}

$array = array();
if ($handle = opendir(ROOT_DIR.$path)) {
	while (false !== ($f = readdir($handle))) if (strlen($f)>2) $array[$f] = $f;
	closedir($handle);
}

$content.= '<form method="post" style="margin:0 0 0 200px" enctype="multipart/form-data" action="?m=template_images">';
$content.= '<input name="upload" type="file" />';
$content.= '<input type="submit" value="Загрузить" />';
$content.= '</form>';

$content.= '<div class="style_menu">';
$content.= '<select size="34" style="width:180px" onchange="if (this.value) top.location=\'?m='.$_GET['m'].'&file=\'+this.value">';
$content.= select($file,$array);
$content.= '</select></div>';

if (is_file(ROOT_DIR.$path.$file)) {
	$text = '<br /><img src="/'.$path.$file.'" />';
	$img = getimagesize(ROOT_DIR.'/'.$path.$file);
	$content.= '<div style="width:800px; float:right;">';
	$content.= '<h1>'.$file.'</h1> размеры: '.$img[0].'x'.$img[1].' '.$message;
	$content.= '<a class="button red" href="?m=template_images&delete='.$file.'"><span>удалить</span></a>';
	$content.= $text;
	$content.= '</div>';
}

$content.= '<div class="clear"></div>';