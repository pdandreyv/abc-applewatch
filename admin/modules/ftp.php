<?php

if ($get['u']=='ajax') {
	echo get_file($get['dir']);
	die();
}

elseif ($get['u']=='file') {
	$path = $_GET['path'];
	$text = '';
	$exc = substr($path, -3);
	$message = $path;
	if (is_file(ROOT_DIR.$path)) {
		if (isset($_POST['text'])) {
			$text = stripslashes_smart($_POST['text']);
			$text = str_replace('  ','	',$text);
			$fp = fopen(ROOT_DIR.$path,'w');
			$message.= fwrite($fp,$text)>=0 ? ' файл обновлен' : ' ошибка записи';
			fclose($fp);
		}
		else {
			$handle = fopen(ROOT_DIR.$path, "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) $text.= $buffer;
				fclose($handle);
			}
		}
	}
	else $message = ' нет файла';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Панель управления сайтом</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="/plugins/CodeMirror/js/codemirror.js"></script>
<style>
body {font:11px Arial; margin:0; padding:30px; background:#F9F9F9}
.box {background:#fff; border: 1px solid silver;}
.box .CodeMirror-line-numbers {background:#F4F4F4;}
</style>
</head>
<body>
<form method="post" action="">
	<?=$message?><br />
	<div class="box"><textarea id="code" name="text"><?=htmlspecialchars($text);?></textarea></div>
	<br />
	<input type="submit"  value="Сохранить">
</form>
<script type="text/javascript">
var editor = CodeMirror.fromTextArea('code', {
	height: "600px",
<?php if ($exc=='css') { ?>
	parserfile: "parsecss.js",
	stylesheet: "/plugins/CodeMirror/css/csscolors.css",
<?php } elseif ($exc=='.js') { ?>
	parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
	stylesheet: "/plugins/CodeMirror/css/jscolors.css",
<?php } else { ?>
	parserfile: [
		"parsexml.js",
		"parsecss.js",
		"tokenizejavascript.js",
		"parsejavascript.js",
		"../contrib/php/js/tokenizephp.js",
		"../contrib/php/js/parsephp.js",
		"../contrib/php/js/parsephphtmlmixed.js"
	],
	stylesheet: "/plugins/CodeMirror/contrib/php/css/phpcolors.css",
<?php }?>
	path: "/plugins/CodeMirror/js/",
	continuousScanning: 600,
	lineNumbers: true,
	textWrapping: false
});
</script>
</body>
</html><?php
	die();
}

$content = '
<style>
.ftp {}
.ftp div {padding:0 0 0 20px}
.ftp .open_folder > a {color:#000}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
	$(document).on("click",".dir",function(){
		var div = $(this).parent("div");
		if (div.hasClass("open_folder")) {
			div.removeClass("open_folder");
			div.find("div").remove();
		}
		else {
			div.addClass("open_folder");
			var dir = $(this).attr("href");
			$.get(
				"/admin.php", {"m":"ftp","u":"ajax","dir":dir},
				function(data){
					div.append(data);
				}
			);
		}
		return false;
	});
});
</script>';
$content.= '<div class="ftp">'.get_file ().'</div>';

function get_file ($dir = '') {
	$content = '';
	if ($handle = opendir(ROOT_DIR.$dir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file=='.'||$file=='..') continue;
			if (is_dir(ROOT_DIR.$dir.'/'.$file)) $folders[] = $file;
			else $files[] = $file;
		}
		closedir($handle);
	}
	if (isset($folders)) {
		sort($folders, SORT_LOCALE_STRING);
		foreach ($folders as $k=>$v) $content.= '<div>:.. <a class="dir" href="'.$dir.'/'.$v.'">'.$v.'/</a></div>';
	}
	if (isset($files)) {
		sort($files, SORT_LOCALE_STRING);
		$array = array('php','ess','.js','css','tml');
		foreach ($files as $k=>$v) {
			$path = in_array(substr($v, -3),$array) ? '/admin.php?m=ftp&u=file&path=' : '';
			$content.= '<div><a target="_blank" href="'.$path.$dir.'/'.$v.'">'.$v.'</a></div>';
		}
	}
	return $content;
}