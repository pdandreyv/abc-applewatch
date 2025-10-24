<?php

//Основные блоки лендинга
/*
 *  v1.2.21 - $languages перенес в /admin/config_multilingual.php
 *  v1.4.16 - $delete удалил confirm
 *  v1.4.17 - сокращение параметров form
*/

//типы страниц лендинга (экраны)
$templates = array(
	1	=> 'О нас',
	2	=> 'Услуги',
	3	=> 'Стоимость работ',
);

$table = array(
	'id'		=> 'rank:desc',
	'name'		=> '',
	'template'	=> $templates,
	'rank'		=> '',
	'display'	=> 'display'
);

//только если многоязычный сайт
if ($config['multilingual']) {
	//приоритет пост над гет
	if (isset($post['language'])) $get['language'] = $post['language'];
	if (@$get['language'] == 0) $get['language'] = key($languages);
	$filter[] = array('language', $languages);
	$form[] = '<input name="language" type="hidden" value="'.$get['language'].'" />';
}
else $get['language'] = 1;

$query = "
	SELECT landing.*
	FROM landing
	WHERE landing.language = '".$get['language']."'
";

//v1.4.16 - $delete удалил confirm
$delete = array(
	'landing_items'=>"SELECT * FROM landing_items WHERE template='".@$post['template']."'"
);

$form[] = array('input td5','name');
$form[] = array('select td3','template',array(
	'help'=>'Шаблон отвечает за отображение одного экрана лендинга',
	'value'=>array(true,$templates)
));
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');

$form[] = array('tinymce td12','text');//,array('attr'=>'style="height:500px"'));
