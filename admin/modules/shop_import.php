<?php

/*
 * добавление товаров из файла в базу сайта
 * идентификация товара идет по полю article которое должно быть самым первым
 * история обновления
 * v1.1.19
 * v1.4.31 - заменил PHPExcel на spout
 */


/*
 * https://opensource.box.com/spout/getting-started/
 * https://github.com/box/spout
 */
require ROOT_DIR . 'vendor/autoload.php';
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

//папка куда будут складываться результаты
$dir = 'files/temp/shop_import';

$table = array(
	'article',
	'price',
	'price2',
	'brand',
	'category',
	'name',
	'special',
	'text',
);
//количество значений без параметров
$table_count = count($table);

//добавляем динамические параметры у которых стоит галочка синхронизация
if ($shop_parameters = mysql_select("SELECT * FROM shop_parameters WHERE import=1 ORDER BY `rank` DESC,id",'rows_id')) {
	foreach ($shop_parameters as $k => $v) {
		$table[] = 'p' . $v['id'];
		$a18n['p' . $v['id']] = $v['name'];
	}
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
		$reader = ReaderEntityFactory::createReaderFromFile($file);
		$reader->open($file);
		$i = 0;
		foreach ($reader->getSheetIterator() as $sheet) {
			foreach ($sheet->getRowIterator() as $row) {
				$i++;
				// do stuff with the row
				$cells = $row->getCells();
				foreach ($cells as $w) {
					$data[$i][] = (string)$w;
				}
			}
		}
		$reader->close();
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

		foreach ($data as $key=>$value) {
			$val = $value[0];
			if ($val>0) {
				$id = mysql_select("SELECT * FROM shop_products WHERE `article` = '".mysql_res($val)."' LIMIT 1",'string');
				$color = $id==0 ? 'table-danger' : 'table-success';
				if (isset($_POST['upload'])) $content.= '<tr class="'.$color.'">';
				$post = array();
				foreach ($table as $k=>$v) {
					$str = $value[$k];
					if (isset($_POST['upload'])) {
						//проверка есть ли у параметра такое значение
						$color = '';
						if ($str AND $k>=$table_count) {
							//отрезаем p и получаем ИД параметра
							$p = substr($v,1);
							if (in_array($shop_parameters[$p]['type'], array(1, 3))) {
								$values = $shop_parameters[$p]['values'] ? unserialize($shop_parameters[$p]['values']) : array();
								if (!in_array($str, $values)) {
									$color = ' style="background-color: #ffeeba;"';
								}
							}
						}
						$content.= '<td '.$color.'>'.$str.'</td>';
					}
					//формирование массива для обновления БД
					else {
						$post[$v] = $str;
					}
				}
				//обработка параметров
				if (isset($_POST['import'])) {
					if ($shop_parameters) {
						foreach ($shop_parameters as $k => $v) {
							$values = $v['values'] ? unserialize($v['values']) : array();
							//если выбор из вариантов, см $config['shop_parameters']['type']
							if (in_array($v['type'], array(1, 3))) {
								if (in_array($post['p' . $v['id']], $values)) {
									$post['p' . $v['id']] = array_search($post['p' . $v['id']], $values);
								} //если значение нет то добавляем его
								else {
									$values[] = $post['p' . $v['id']];
									$post['p' . $v['id']] = array_search($post['p' . $v['id']], $values);
									$shop_parameters[$k]['values'] = serialize($values);
									mysql_fn('update', 'shop_parameters', $shop_parameters[$k]);
								}
							}

						}
					}
				}

				if (isset($_POST['import'])) {
					if ($id==0) {
						mysql_fn('insert','shop_products',$post);
						$insert++;
					}
					else {
						$post['id'] = $id;
						mysql_fn('update','shop_products',$post);
						$update++;
					}
				}
				if (isset($_POST['upload']))  $content.= '</tr>';
			}
			//else echo '-'.$value[$a[0]].'-'.$value[0].'-';
		}
		if (isset($_POST['upload'])) $content.= '</table>';
		else {
			$content.= '<br />Количество обновленных товаров:'.$update;
			$content.= '<br />Количество добавленных товаров:'.$insert;
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
