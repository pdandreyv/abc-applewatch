<?php

/*
v1.1.17 - скрипт для обновления картинок в каталоге
v1.1.18 - скрипт для обновления картинок в каталоге - пофиксил баги + backup
v1.2.8 - исправление ошибки при удалении картинок
*/

//путь к папке с картинками
$temp = '_images';
//основная картинка - копируем строчку из shop_products
$img = array('file td6','img','Основная картинка',array(''=>'resize 1000x1000','m-'=>'resize 400x400','p-'=>'resize 150x150'));
//дополнительные
$imgs = array('file_multi','imgs','Дополнительные картинки',array(''=>'resize 1000x1000','p-'=>'resize 150x150'));
//таблица
$table2 = 'shop_products';
//поле для идентификации
$field = 'article';
//путь к картинкам в каталоге
$files_dir = 'files/'.$table2;
//удалять папку после успешного обновления
$delete = 1;
//добавлять _ к папке с картинками если такого товара не нашли
$rename = 1;

//обновление картинок
if (@$_GET['dir']) {
	$dir = preg_replace('~[^-a-z0-9_.]+~u', '', $_GET['dir']);	//удаление лишних символов
	$error = $success = array();
	if (is_dir(ROOT_DIR . $temp . '/' . $dir)) {
		$product = mysql_select("SELECT * FROM `".$table2."` WHERE `".$field."`='".mysql_res($dir)."' LIMIT 1",'row');
		if ($product) {
			if ($handle = opendir(ROOT_DIR . $temp . '/' . $dir)) {
				$files = array();
				while (false !== ($file = readdir($handle))) {
					if ($file == '.' || $file == '..') continue;
					if (is_file(ROOT_DIR . $temp . '/' . $dir . '/' . $file)) {
						$files[] = $file;
					}
				}
				//если есть файлы
				if ($files) {
					include_once (ROOT_DIR.'functions/image_func.php');
					//удаляем основную и доп картинки
					delete_all(ROOT_DIR.$files_dir.'/'.$product['id'].'/'.$img[1].'/',true);
					delete_all(ROOT_DIR.$files_dir.'/'.$product['id'].'/'.$imgs[1].'/',true);
					//массив данных товара
					$data = array(
						'id'        =>$product['id'],
						$img[1]     => '', //основная картинка
						$imgs[1]    => '' //дополнительная
					);
					$photos = array();
					sort($files);
					$n = 0;
					foreach ($files as $key=>$val) {
						//загрузка основной картинки
						if ($n==0) {
							$file = strtolower(trunslit($val)); //название файла
							$param = $img[3];
							$path = $files_dir.'/'.$product['id'].'/'.$img[1]; //папка от корня основной папки
							$root = ROOT_DIR.$path.'/'; //папка от корня сервера
							$temp_file = ROOT_DIR.$temp.'/'.$product[$field].'/'.$val;
							if (copy2 ($temp_file,$root,$file,$param)) {
								$data[$img[1]] = $file;
							}
							else $error[] = 'ошибка '.$file;
						}
						//загрузка доп картинок
						else {
							$file = strtolower(trunslit($val)); //название файла
							$param = $imgs[3];
							$path = $files_dir.'/'.$product['id'].'/'.$imgs[1].'/'.$n; //папка от корня основной папки
							$root = ROOT_DIR.$path.'/'; //папка от корня сервера
							$temp_file = ROOT_DIR.$temp.'/'.$product[$field].'/'.$val;
							if (copy2 ($temp_file,$root,$file,$param)) {
								$photos[$n] = array(
									'file' => $file,
									'name' => current(explode('.',$val,2)),
									'display' => 1,
								);
							}
							else $error[] = 'ошибка '.$file;
						}
						$n++;
					}
					if ($photos) {
						$data[$imgs[1]] = serialize($photos);
					}
					//print_r($data);
					mysql_fn('update',$table2,$data);
					$success[] = 'товар обновлен';
					//удаляем папку если без ошибок прошло обновление
					if ($error==false) {
						if ($delete==true) {
							$success[] = 'папка удалена';
							delete_all($temp.'/'.$product[$field].'/',true);
						}
					}
				}
				else $error[] = 'нет картинок';
			}
			else $error[] = 'нет папки';
			closedir($handle);
		}
		else {
			$error[] = 'нет товара';
			//добавляем _ если не нашли такого товара
			if ($rename==true) {
				$error[] = 'папка переименована';
				rename (ROOT_DIR.$temp.'/'.$dir,ROOT_DIR.$temp.'/_'.$dir);
			}
		}
	}
	else $error[] = 'нет папки';
	if ($success) echo ' <strong style="color:green">'.implode(', ',$success).'</strong>';
	if ($error) echo ' <strong style="color:darkred">'.implode(', ',$error).'</strong>';
	die();
}
//главная страница
else {
	$content = '
		<br>Для автоматической привязки картинок к товароам загрузите их в корневой каталог сайта в папку <a target="_blank" href="http://'.$_SERVER['SERVER_NAME'].'/_images/">/_images/</a>
		<br>Сами картинки одного товара должны находиться внутри одной папки, которая названа точно также как и артикул товара, например:
		<br>/_images/12345/1.jpg
		<br>/_images/12345/2.jpg
		<br>где "12345" является артикулом товара
		<br>
		<br>Текущее содержание папки /_images/
		<a href="#" class="image_start">запустить скрипт</a>
		<a href="#" style="display:none" class="image_stop">остановить скрипт</a>
		<div class="image_list">
	';
	$folders = array();
	if (is_dir(ROOT_DIR . $temp) AND $handle = opendir(ROOT_DIR . $temp)) {
		while (false !== ($dir = readdir($handle))) {
			if ($dir == '.' || $dir == '..') continue;
			if (is_dir(ROOT_DIR . $temp . '/' . $dir)) {
				$folders[$dir]['name'] = $dir;
				$folders[$dir]['count'] = 0;
				if ($handle2 = opendir(ROOT_DIR . $temp . '/' . $dir)) {
					while (false !== ($file = readdir($handle2))) {
						if ($file == '.' || $file == '..') continue;
						if (is_file(ROOT_DIR . $temp . '/' . $dir . '/' . $file)) {
							$folders[$dir]['count']++;
						}
					}
				}
			}
		}
		closedir($handle);
	}

	foreach ($folders as $k => $v) {
		$content .= '<div>:../' . $v['name'] . '/ (' . $v['count'] . ') ';
		if (substr($v['name'], 0, 1)!='_') {
			$content .= '<a href="?m=shop_upload_images&dir=' . $v['name'] . '">загрузить</a>';
		}
		$content.= '</div>';
	}
	$content.= '</div>';

	$content .= '
	<script>
	document.addEventListener("DOMContentLoaded", function () {
		//обновление одной картинки
		$(document).on("click",".image_list a",function(){
			var box = $(this).parent("div"),
				url = $(this).attr("href");
			$(this).remove();
			$("body").css("cursor","wait");
			$.get(
				url,
				function(data){
					//alert(data);
					box.append(data);
					$("body").css("cursor","default");
					//если включена автоматическая работа
					if ($(".image_list").hasClass("auto")) {
						if ($(".image_list div a").length>0) {
							$(".image_list div a").each(function(){
								$(this).trigger("click");
								return false;
							});
						}
						else {
							alert("Обновлены все картинки!");
							$(".image_stop").hide();
						}
					}
				}
			);
			return false;
		});
		//запустить автоматическую работу
		$(document).on("click",".image_start",function(){
			$(this).hide();
			$(".image_stop").show();
			$(".image_list").addClass("auto");
			$(".image_list div a").each(function(){
				$(this).trigger("click");
				return false;
			});
			return false;
		});
		//остановить автоматическую работу
		$(document).on("click",".image_stop",function(){
			$(this).hide();
			$(".image_start").show();
			$(".image_list").removeClass("auto");
			return false;
		});
	});
	</script>';
}



