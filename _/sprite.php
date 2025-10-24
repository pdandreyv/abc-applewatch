<?php

/*
 * генерация спрайтов из картинок
 * складываем картинки в папку templates/images/sprite
 * и называем их так как они будут в стилях, например
 * .sprite.arrow.png
 * .sprite.dot.png
 * на сайте делаем просто код <span class="sprite arrow"></span>
 * если нам нужно сделать псевдоклас, например :hover то файл называем так
 * .sprite.arrow..hover.png
 * если нужно чтобы в классе был проблел, например
 * a:hover .sprite.arrow {}
 * то называем файл так
 * a..hover+.sprite.arrow.png
 *
 */


//путь файла спрайта - такой файл будет сгенерирован
$sprite_file = 'admin/templates/sprite.png';
//$sprite_file = 'templates/images/sprite.png';
//путь где лежат картинки - сюда нужно поскладывать картинки
$sprite_img = 'admin/templates/sprite';
//$sprite_img = 'templates/images/sprite';
//префикс
$prefix = '';//'.sprite .';

//***************************************************************************

error_reporting(E_ALL);
//error_reporting(0);

// загрузка настроек *********************************************************
define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
//require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
//require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
//require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
//require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками


$area = $width = $height = 0;
if ($handle = opendir(ROOT_DIR.$sprite_img.'/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file=='.' OR $file=='..') continue;
		$size = getimagesize(ROOT_DIR.$sprite_img.'/'.$file);
		//dd($size);
		//echo $file.' ['.$size[0].'x'.$size[1].']<br />';
		$coef = $size[0]>$size[1] ? $size[0]/$size[1] : $size[1]/$size[0];
		$coef2 = sqrt($coef)*$size[0]*$size[1];
		$sprite[] = array(
			'name'	=> $file,
			'width'	=> $size[0],
			'height'=> $size[1],
			'area'	=> $size[0]*$size[1],
			'coef'	=> $coef,
			'coef2'	=>$coef2
		);
		$area+=$size[0]*$size[1];
		$width = $size[0]>$width ? $size[0] : $width;
		$height = $size[1]>$height ? $size[1] : $height;
	}
	closedir($handle);
}
//dd($sprite);
$sqrt[1] = $sqrt[0] = ceil(sqrt($area*1.5));
if ($sqrt[0]<$width) {
	$sqrt[0] = $width;
	$sqrt[1] = ceil($area*1.5/$width);
}
elseif ($sqrt[1]<$height) {
	$sqrt[1] = $height;
	$sqrt[0] = ceil($area*1.5/$height);
}

//сортировка по площади
foreach ($sprite as $k=>$v) {
	$coef = 0;
	$key = $k;
	foreach ($sprite as $k1=>$v1) {
		if ($v1['coef2']>$coef) {
			$coef = $v1['coef2'];
			$key = $k1;
		}
	}
	$sprite_new[] = $sprite[$key];
	unset($sprite[$key]);
}
$sprite = $sprite_new;

?>
<style>
	* {margin:0; padding:0}
	ul {list-style:none; background:#00FF00}
	li {display:inline-block; float:left; padding:1px 1px 0 0; background: #000}
	.sprite {display:inline-block; font-size:0px; background-image: url('/<?=$sprite_file?>'); }
</style>
<?=html_sources('return','jquery.js jquery_ui.js')?>
<?php
echo '<ul style="width:'.$sqrt[0].'px;">';
foreach ($sprite as $k=>$v) {
	echo '<li><img src="/'.$sprite_img.'/'.$v['name'].'" alt="'.$v['name'].'" width="'.$v['width'].'" height="'.$v['height'].'"/></li>';
} ?>
<div style="clear:both; width:0; height:0; font-size:0; line-height:0;"></div>
</ul>
<form method="post">
	<input type="submit" value="Сгенерировать картинку">
</form>

<?php
$sprite = $style = $css = '';
if (count($_POST)>0) { //echo 1;
	$css.= ".sprite {display:inline-block; font-size:0px; background-image: url('/".$sprite_file."?".time()."'); }<br />";
	$size_box = explode(',',$_POST['box']); //print_r($size_box);
	//print_r($_POST['sprite']);
	if (is_file(ROOT_DIR.$sprite_file)) unlink(ROOT_DIR.$sprite_file);
	foreach ($_POST['sprite'] as $k=>$v) { //echo 1;
		if (file_exists(ROOT_DIR.$sprite_img.'/'.$k)) {
			if (!is_file(ROOT_DIR.$sprite_file)) {
				$file = ROOT_DIR.$sprite_file;
				$img = imageCreatetruecolor($size_box[0],$size_box[1]);
				imageInterlace($img,1); // Добавляем постепенную загрузку
				$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127); // Добавляем прозрачность
				imagefill($img, 0, 0, $transparent);
				imagesavealpha($img,true); // Включаем обработку альфа канала
				imagepng($img,$file);
				imageDestroy($img);
			}
			$file1 = ROOT_DIR.$sprite_file;
			$file2 = ROOT_DIR.$sprite_img.'/'.$k;
			$file3 = ROOT_DIR.$sprite_file;
			$size1 = getimagesize($file1);
			$size2 = getimagesize($file2);
			$img1 = imagecreatefrompng($file1);
			$img2 = imagecreatefrompng($file2);
			//imagealphablending($img1, true);
			//imagealphablending($img2, true);
			$pos = explode(',',$v);
			imagecopy($img1,$img2,$pos[1],$pos[0],0,0,$size2[0],$size2[1]);
			//imagecopyresampled($img1, $img2, 0, 0, 0, 0, $size2[0], $size2[1], $size2[0], $size2[1]);
			imagesavealpha($img1, true);
			imagepng ($img1,$file3);
			imagedestroy($img1);
			imagedestroy($img2);
			$name = substr($k, 0, -4);
			$name = str_replace(
				array('..','+'),
				array(':',' '),
				$name);
			$style.= ''.$name.' {width:'.$size2[0].'px; height:'.$size2[1].'px; background-position:-'.$pos[1].'px -'.$pos[0].'px}';
			$css.= $prefix.$name.' {width:'.$size2[0].'px; height:'.$size2[1].'px; background-position:-'.$pos[1].'px -'.$pos[0].'px}<br />';
			//$sprite.='<span class="sprite '.$name.' stlye=""></span>';
		}
		else echo 'нет файла'.$k;
		//break;
	}
	echo $css;
	echo '<style>'.$style.'</style>';
	echo '<div style="background:#00FF00">'.$sprite.'</div>';

}
?><div style="background:#00FF00"><img src="/<?=$sprite_file?>?<?=time()?>" /></div>
<script type="text/javascript">
	$(document).ready(function(){
		$('ul').sortable();
		$('ul li img').each(function(){
			name	= $(this).attr('alt');
			offset	= $(this).offset();
			$('form').append('<input name="sprite['+name+']" type="hidden" value="'+parseInt(offset.top)+','+parseInt(offset.left)+'" >');
		});
		$('form').append('<input name="box" type="hidden" value="'+$('ul').width()+','+$('ul').height()+'" >');
	});
</script>
<?php
/*
echo '<table>';
foreach ($sprite as $k=>$v) {
	echo '<tr>';
	echo '<td style="background:gray"><img src="sprite/'.$v['name'].'" /></td>';
	echo '<td>'.$v['name'].'</td>';
	echo '<td>'.$v['width'].'x'.$v['height'].'</td>';
	echo '<td>'.$v['area'].'</td>';
	echo '<td>'.$v['coef'].'</td>';
	echo '<td>'.$v['coef2'].'</td>';
	echo '</tr>';
}/**/


?>
