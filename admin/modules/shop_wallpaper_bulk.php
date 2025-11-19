<?php

// Массовая загрузка обоев (каталог)
/*
 * v1.0.0
 * Загружает сразу несколько изображений и создает записи в `shop_wallpaper`
 */

$module['one_form'] = true;

global $config;

// Справочники
$categories = mysql_select("SELECT id,name,0 as level FROM `shop_wallpaper_categores` ORDER BY sort, id",'rows_id');

// При сохранении формы
if ($get['u']=='edit') {
	// Ожидаем структуру от поля file_multi: $post['images'] = [ n => ['temp'=>TEMP_ID, 'name'=>..., 'display'=>1], ... ]
	$uploads = isset($_POST['images']) ? stripslashes_smart($_POST['images']) : array();
	$total = 0;
	$ok = 0;
	$errors = array();

	if ($uploads && is_array($uploads)) {
		foreach ($uploads as $n=>$val) {
			$total++;
			$tempId = isset($val['temp']) ? preg_replace('~[^-a-z0-9_]+~u', '', $val['temp']) : '';
			if ($tempId==='') { $errors[] = 'empty temp for item '.$n; continue; }

			// находим файл во временной папке
			$tempDir = ROOT_DIR . 'files/temp/' . $tempId . '/';
			if (!is_dir($tempDir)) { $errors[] = 'temp dir not found: '.$tempId; continue; }
			$sourceFilePath = '';
			$sourceFileName = '';
			if ($handle = opendir($tempDir)) {
				while (false !== ($f = readdir($handle))) {
					if (strlen($f)>2 && is_file($tempDir.$f)) { $sourceFileName = $f; $sourceFilePath = $tempDir.$f; break; }
				}
				closedir($handle);
			}
			if ($sourceFilePath==='') { $errors[] = 'no file in temp: '.$tempId; continue; }

			// создаем запись
			$row = array(
				'category'   => isset($post['category']) ? intval($post['category']) : 0,
				'sort'       => isset($post['sort']) ? intval($post['sort']) : 0,
				'img'        => '',
				'price'      => isset($post['price']) ? 1 : 0,
				'display'    => isset($post['display']) ? 1 : 0,
				'created_at' => $config['datetime'],
			);
			$row['id'] = mysql_fn('insert','shop_wallpaper',$row);

			// копируем картинку в целевую папку записи
			include_once(ROOT_DIR.'functions/file_func.php');
			$root = ROOT_DIR.'files/shop_wallpaper/'.$row['id'].'/img/';
			$file = strtolower(trunslit($sourceFileName));
			$copied = copy2($sourceFilePath, $root, $file, array(''=>'resize 1000x1000'));
			if ($copied) {
				mysql_fn('update','shop_wallpaper', array(
					'id'  => $row['id'],
					'img' => $file,
				));
				$ok++;
			} else {
				$errors[] = 'copy failed for item '.$n;
				// откат записи
				mysql_fn('delete','shop_wallpaper', array('id'=>$row['id']));
			}

			// очищаем временную папку
			delete_all($tempDir, true);
		}
	}

	$data = array(
		'error' => '',
		'message' => 'Загружено: '.$ok.' из '.$total.($errors ? ' (ошибки: '.count($errors).')' : ''),
	);
	echo '<textarea>'.json_encode($data, JSON_HEX_AMP).'</textarea>';
	die();
}

// Форма
$form[] = '<h2>Массовая загрузка обоев</h2>';
$form[] = array('select td4','category',array(
	'value'=>array(true,$categories,'- категория -')
));
$form[] = array('input td2 right','sort', array('name'=>'Сортировка'));
$form[] = array('checkbox td2','price', array('name'=>'Платный'));
$form[] = array('checkbox td2','display', array('name'=>'Показывать'));
$form[] = 'clear';
$form[] = array('file_multi td12','images', array(
	'name'=>'Картинки (множественная загрузка)',
	'sizes'=>array(''=>'resize 1000x1000'),
	'fields'=>array()
));



