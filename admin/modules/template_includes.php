<?php

$path = $config['style'].'/includes/';
$path = 'templates/includes/';

$array = array();
if ($handle = opendir(ROOT_DIR.$path)) {
	while (false !== ($f = readdir($handle))) {
		if (substr($f, -3)=='php') {
			$array[$f] = $f;
		}
		elseif (strlen($f)>2 && is_dir(ROOT_DIR.$path.$f.'/')) {
			if ($handle2 = opendir(ROOT_DIR.$path.$f.'/')) {
				while (false !== ($file2 = readdir($handle2))) {
					if (substr($file2, -3)=='php') {
						$array[$f.'/'.$file2] = $f.'/'.$file2;
					}
				}
			}
		}
	}
	closedir($handle);
}
sort($array, SORT_LOCALE_STRING);
foreach ($array as $k=>$v) $array2[$v] = $v;
$array = $array2;

$file = isset($_GET['file']) ? $_GET['file'] : '';
if (!in_array($file,$array)) $file=key($array);

if (isset($_POST['text'])) {
	$text = stripslashes_smart($_POST['text']);
	$fp = fopen(ROOT_DIR.$path.$file,'w');
	$message = fwrite($fp,$text)>=0 ? 'файл обновлен' : 'ошибка записи';
	fclose($fp);
	die($message);
}

$handle = fopen(ROOT_DIR.$path.$file, "r");
$text = '';
if ($handle) {
	while (($buffer = fgets($handle, 4096)) !== false) $text.= $buffer;
	fclose($handle);
}

$content.= '<div class="style_menu">';
$content.= '<select onchange="if (this.value) top.location=\'?m='.$_GET['m'].'&file=\'+this.value">';
$content.= select($file,$array);
$content.= '</select>';
$content.= '</div>';

$content.= '<link rel="stylesheet" href="/plugins/CodeMirror/lib/codemirror.css">';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/lib/codemirror.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/addon/edit/matchbrackets.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/mode/htmlmixed/htmlmixed.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/mode/xml/xml.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/mode/javascript/javascript.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/mode/css/css.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/mode/clike/clike.js"></script>';
$content.= '<script type="text/javascript" src="/plugins/CodeMirror/mode/php/php.js"></script>';

$content.= '<form method="post" class="style_form" action="?'.$_SERVER['QUERY_STRING'].'">';
$content.= '<h1>'.$file.'</h1>';
$content.= form('CodeMirror','text',$text);
$content.= '<a href="#" class="button red js_submit_style"><span>Сохранить</span></a>';
$content.= '<div class="message"></div>';
$content.= '</form>';
$content.= '<div class="clear"></div>';
$content.= <<<HTML
<script type="text/javascript">
	var editor = CodeMirror.fromTextArea($('#codeMirror').get(0), {
			lineNumbers: true,
			matchBrackets: true,
			mode: "application/x-httpd-php",
			indentUnit: 4,
			indentWithTabs: true,
			enterMode: "keep",
			tabMode: "shift",
		});
	editor.setSize(null,$(window).height()-200);
</script>
HTML;
