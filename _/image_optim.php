<?php

/*
 * оптимизация всех картинок на сервере
 */
error_reporting(E_ALL);
define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'functions/file_func.php');

//адрес скрипта для обработки изображений
$server = 'https://5legko.com/jpegoptim.php';
//протокол и домен данного сайта сайта
$domain = 'http://'.$_SERVER['SERVER_NAME'].'/';
//папка для оптимизации
$path = ROOT_DIR.'files/';
//$path = ROOT_DIR.'templates/';

if (@$_GET['go']) {
	$file = $_GET['go'];
	$base64 = base64_encode($domain.$file);
	$content = file_get_contents($server.'?file='.$base64);
	if ($content AND $content!='error') {
		//echo ROOT_DIR.$file;
		$fp = fopen(ROOT_DIR.$file, 'w');
		fwrite($fp,$content);
		fclose($fp);
		echo intval(filesize(ROOT_DIR.$file)/1024).'kb';
	}
	else 'error';
	die();
}

$files = scandir2($path,true);
//dd($files);
?>
<style>
	* {font:12px Arial}
</style>
<a href="#" class="start">старт</a>
<a href="#" class="stop" style="display:none">стоп</a>
<table>
<?php
$len = mb_strlen(ROOT_DIR);
if ($files) foreach ($files as $k=>$v) {
	//echo substr($v,-3);
	//echo '<br>';
	if (substr($v,-3)=='jpg') {
		$file = substr($v,$len);
		?>
	<tr data-id="<?=$k?>">
		<td class="name"><?=$file?></td>
		<td class="size"><?=intval(filesize($v)/1024)?>kb</td>
		<td><a href="#" class="go">сгенерировать <?=$k?></a></td>
	</tr>
		<?php
	}
}
echo '<table>';
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		go = false;
		$('.start').click(function () {
			$(this).hide();
			$('.stop').show();
			go = true;
			go_first();
			return false;
		});
		$('.stop').click(function () {
			$(this).hide();
			$('.start').show();
			go = false;
			return false;
		});
		$('.go').click(function () {
			var id = $(this).closest('tr').data('id');
			go_id (id);
			return false;
		});

	});
	function go_first () {
		$('.go').each(function(){
			var id = $(this).closest('tr').data('id');
			go_id (id);
			breack;
		});
	}
	function go_id (id) {
		var a = $('tr[data-id='+id+'] .go');
		var file = $('tr[data-id='+id+'] .name').text();
		//alert(id);
		$.get(
			'/_/image_optim.php',
			{'go':file},
			function(data){ //alert(data);
				$(a).replaceWith(data);
				if (go) {
					go_first();
				}
			}
		);
	}
</script>
<?php

