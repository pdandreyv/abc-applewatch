<?php

/*
 * стандартный модуль элементов для шаблона лендинга
 * в зеркальных модулях лендинга (landing_items*.php) мы просто инициализируем переменную $template которая соотвествует шаблону лендинга
 * значение описаны в массиве $templates в файле landing.php
 * сколько там элементов, столько файлов landing_items*.php нужно создавать
 * в зеркальных модулях можно задавать немного другие поля и другие размеры для загрузки фото
 * v1.2.21 - $languages перенес в /admin/config_multilingual.php
 * v1.4.17 - сокращение параметров form
*/

if (!isset($template)) $template = 1;

//только если многоязычный сайт
if ($config['multilingual']) {
	//приоритет пост над гет
	if (isset($post['language'])) $get['language'] = $post['language'];
	if (@$get['language'] == 0) $get['language'] = key($languages);
	$filter[] = array('language', $languages);
	$form[1] = '<input name="language" type="hidden" value="'.$get['language'].'" />';
}
else $get['language'] = 1;

$table = array(
	'id'		=> 'rank:desc',
	'name'		=> '',
	'rank'		=> '',
	'display'	=> 'display'
);

$query = "
	SELECT landing_items.*
	FROM landing_items
	WHERE landing_items.language = '".$get['language']."' AND landing_items.template=$template
";

//echo $query;

$form[2] = '<input name="template" type="hidden" value="'.$template.'" />';
$form[3] = array('input td8','name');
$form[4] = array('input td2','rank');
$form[5] = array('checkbox','display');
$form[6] = array('tinymce td12','text');//,array('attr'=>'style="height:500px"'));
$form[7] = array('file td6','img',array(
	'name'=>'Основная картинка',
	'sizes'=>array(''=>'resize 1000x1000')
));

