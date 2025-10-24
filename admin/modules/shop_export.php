<?php

/*
 * сохранение товаров в файл
 * динамические параметры тут - /admin/modules/shop_parameters.php
 * история обновления
 * v1.1.19
 * v1.4.31 - заменил PHPExcel на spout
 */


/*
 * https://opensource.box.com/spout/getting-started/
 * https://github.com/box/spout
 */
require ROOT_DIR . 'vendor/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
//use Box\Spout\Common\Entity\Row;

//папка куда будут складываться результаты
$dir = 'files/temp/shop_export';

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

//дополнительный массив для параметров
$table2 = array();

//добавляем динамические параметры у которых стоит галочка синхронизация
if ($shop_parameters = mysql_select("SELECT * FROM shop_parameters WHERE import=1 ORDER BY `rank` DESC,id",'rows')) {
	foreach ($shop_parameters as $k => $v) {
		$table2[] = 'p' . $v['id'];
		$a18n['p' . $v['id']] = $v['name'];
	}
}

//v1.4.31 - заменил PHPExcel на spout
if (isset($_POST['export'])) {
	$data = array();
	foreach ($table as $k=>$v) {
		$data[0][] = $a18n[$v];
	}
	foreach ($table2 as $k=>$v) {
		$data[0][] = $a18n[$v];
	}
	if ($shop_products = mysql_select("
		SELECT p.*
		FROM shop_products p
		WHERE 1
	",'rows')) {
		$i = 0;
		foreach ($shop_products as $q) {
			$i++;
			//$content .= '<tr valign="top">';
			foreach ($table as $k => $v) {
				$data[$i][] = $q[$v];
				//$content .= '<td>' . $q[$v] . '</td>';
			}
			if ($shop_parameters) {
				foreach ($shop_parameters as $k => $v) {
					$values = $v['values'] ? unserialize($v['values']) : array();
					//если выбор из вариантов, см $config['shop_parameters']['type']
					if (in_array($v['type'],array(1,3))) $str = @$values[$q['p'.$v['id']]];
					else $str = $q['p'.$v['id']];
					$data[$i][] = $str;
					//$content .= '<td>' . $str . '</td>';
				}
			}
			//$content .= '</tr>';
		}
	}

	// $writer = WriterEntityFactory::createODSWriter();
	if ($_POST['type']=='csv') {
		$fileName = date('Y-m-d_H_i'). ".csv";
		$writer = WriterEntityFactory::createCSVWriter();
	}
	else {
		$fileName = date('Y-m-d_H_i'). ".xlsx";
		$writer = WriterEntityFactory::createXLSXWriter();
	}

	//$writer->openToFile($filePath); // write data to a file or to a PHP stream
	$writer->openToBrowser($fileName); // stream data directly to the browser

	foreach ($data as $q) {
		$rowFromValues = WriterEntityFactory::createRowFromArray($q);
		$writer->addRow($rowFromValues);
	}
	$writer->close();

	die();
}
else {
	$content = '<br /><h2>Подтверждение создания файла</h2>';
	$content.= 'Будет сгенерирован файл excel c такой структурой:<br />';
	$content.= '<br /><table class="table"><tr>';
	foreach ($table as $k=>$v) {
		//$str.= '"'.str_replace('"',"&quot;",$fieldset[$v]).'";';
		$content.= '<th>'.$a18n[$v].'</th>';
	}
	foreach ($table2 as $k=>$v) {
		$content.= '<th>'.$a18n[$v].'</th>';
	}
	$content.= '</tr></table>';
	$content.= '<form method="post" action="">';
	$content.= '<br /><select name="type"><option value="csv">csv</option><option value="xlsx">xlsx</option></select> &nbsp; ';
	$content.= '<input type="submit" name="export" value=" Сгенерировать файл ">';
	$content.= '</form>';
}
unset($table);
