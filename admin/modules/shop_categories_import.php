<?php

/*
 * добавление категорий из файла в базу сайта
 * категории идут рядами, вложенность отображена столбиками
 * история обновления
 * v1.12.16
 */

//папка куда будут складываться результаты
$dir = 'files/temp/shop_сategories_import';

$table = array(
	'l1',
	'l2',
	'l3',
	'l4',
	'l5',
	'l6',
	'l7',
	'l8',
);
//уровень вложенности категорий
$table_count = count($table);

//текущий ряд с категориями
$categories = array();
//текущие ряд активных категорий
$categories_active = array();
//текущий уровень вложенности
$current_level = 1;
$left_key = 1;


for ($i=1; $i<=$table_count; $i++) {
	$a18n['l' . $i] = $i . ' уровень';
	$categories_active[$i] = '';
}



$content = '<br /><h2>Загрузка файла excel (csv, xls, xlsx)</h2>';
$content.= '<form method="post" enctype="multipart/form-data" action="">';
$content.= '<br /><input type="file" name="i">';
$content.= '<input type="submit" name="upload" value="Загрузить файл">';
$content.= '</form>';

if (count($_POST)>0) {
	$file = $exc = $data = false;
	//загрузка файла
	if (isset($_POST['upload'])) {
		if (is_file($_FILES["i"]["tmp_name"])) {
			$file = strtolower(trunslit($_FILES['i']['name']));
			$arr = explode('.',$file);
			$exc = end($arr);
			$file = ROOT_DIR.$dir.'/'.$file; //die($file);
			if (is_dir(ROOT_DIR.$dir) || mkdir(ROOT_DIR.$dir,0755,true)) {
				copy($_FILES["i"]["tmp_name"],$file);
			}
		}
		else $content.= '<br /><b>ошибка загрузки файла</b>';
	}
	//импорт файла
	elseif (isset($_POST['import'])) {
		if (is_file($_POST['file'])) {
			mysql_fn('query',"TRUNCATE `shop_categories`");
			$file = $_POST['file'];
			$arr = explode('.',$file);
			$exc = end($arr);
		}
		else {
			$content.= '<br /><b>ошибка загрузки файла</b>';
		}
	}
	//обработка файла
	if ($file AND is_file($file)) {
		//загрузка csv
		$i = 0;
		if ($exc=='csv') {
			$handle = fopen($file, 'r');
			while (($value = fgetcsv($handle, 8000, ';')) !== FALSE) {
				$i++;
				foreach ($table as $k=>$v) {
					if ($k==0) {
						$data[$i][$k] = iconv("cp1251", "UTF-8",current($value));
						//next($value);
					}
					else $data[$i][$k] = iconv("cp1251", "UTF-8",next($value));
				}
			}
			fclose($handle);
		}
		//загрузка excel
		elseif ($exc=='xls' OR $exc=='xlsx') {
			include ROOT_DIR.'plugins/phpexcel/PHPExcel/IOFactory.php';
			$inputFileName = $file;
			//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			$data = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		}
	}
	else {
		$content.= '<br /><b>ошибка типа файла</b>';
	}
	//вывод данных на экран
	if (is_array($data)) { //print_r($data);
		if (isset($_POST['upload'])) {
			$content = '<br /><h2>Подтверждение загрузки</h2>';
			$content.= '<form method="post" action=""><input name="file" type="hidden" value="'.$file.'"><input type="submit" name="import" value="Загрузить данные на сайт?"> &nbsp; <a href="">назад</a></form><br />';
			$content.= '<br /><h2>Содержание загруженого файла</h2>';
			$content.= 'Товары на зеленом фоне будут обновлены, товары на красном фоне будут добавлены<br />';
			$content.= 'На оранжевом фоне отображены несуществующие значение динамических параметров, которые будут добавлены<br /><br />';
			$content.= '<table class="table"><tr>';
			foreach ($table as $k=>$v) {
				$content.= '<th>'.$a18n[$v].'</th>';
			}
		}
		else {
			$insert = $update = 0;
			$content = '<h3>Результаты загрузки</h3>';
		}
		//алфавит для экселя
		$a = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		foreach ($data as $key=>$value) {
			if ($exc=='csv') $val = $value[0];
			//для экселя используем алфавит
			else $val = $value['A'];
			if ($val) {
				if (isset($_POST['upload'])) $content.= '<tr>';
				$post = array();
				foreach ($table as $k=>$v) {
					if ($exc=='csv') $str = $value[$k];
					else $str = $value[$a[$k]];
					if (isset($_POST['upload'])) {
						$content.= '<td>'.$str.'</td>';
					}
					//формирование массива для обновления БД
					else {
						$post[$v] = $str;
					}
				}

				if (isset($_POST['import'])) {
					//добавляем категории с вложенностью но без проставленных left_key
					$level = 0;
					foreach ($post as $k=>$v) {
						if ($v=='') break;
						$name = $v;
						$level++;
						//$level.$name
						$categories[$level] = $name;
					}



					//вставка категорий
					for ($i=1; $i<=$table_count; $i++) {
						if (@$categories[$i]) {
							//если текущая активная не ровняется
							if (@$categories_active[$i]['name']!=$categories[$i]) {
								$prev = $i-1;
								$parent = $prev>0 ? $categories_active[$prev]['id'] : 0;
								//вставка текущей категория
								$data = array(
									'name' => $categories[$i],
									'level' => $i,
									'parent' => $parent,
									'display' => 1,
									'left_key' => $left_key++,
									'right_key' => $left_key++,
									'url' => trunslit($categories[$i]),
									'title' => $categories[$i],
									'description' => $categories[$i],
								);
								$id = mysql_fn('insert', 'shop_categories', $data);
								$categories_active[$i] = array(
									'id'=>$id,
									'name'=>$categories[$i]
								);
								$insert++;
								/* *
								for ($l=1; $l<=$i; $l++) echo '&nbsp;&nbsp;&nbsp;&nbsp;';
								echo ':..';
								echo $data['name'];
								echo '<br>';
								/* */
							}
						}
						//очищаем текущую активную
						else $categories_active[$i] = array('id'=>0,'name'=>'');
					}
				}
				if (isset($_POST['upload']))  $content.= '</tr>';
			}
			//else echo '-'.$value[$a[0]].'-'.$value[0].'-';
		}
		if (isset($_POST['upload'])) $content.= '</table>';
		else {
			$content.= '<br />Количество добавленных категорий:'.$insert;
			//проставляем правильно left_key и right_key
			$shop_categories = mysql_select("SELECT id,parent FROM `shop_categories` ORDER BY left_key",'rows');
			foreach ($shop_categories as $k=>$v) {
				if ($v['parent']) {
					//dd($v);
					//вставляем в нужного родителя
					nested_sets('shop_categories',$v['parent'], $v['id'],'parent');
				}
			}
		}
	}
	else {
		$content.= '<br /><b>ошибка обработки файла</b>';
	}
}
else {
	$content.= '<br /><h2>Файл должен быть с такой структоруй</h2>';
	$content.= '<br /><table class="table"><tr>';
	foreach ($table as $k=>$v) {
		//$str.= '"'.str_replace('"',"&quot;",$fieldset[$v]).'";';
		$content.= '<th>'.$a18n[$v].'</th>';
	}
	$content.= '</tr></table>';
}
unset($table);
