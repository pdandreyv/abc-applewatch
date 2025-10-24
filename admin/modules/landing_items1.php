<?php

//пример зеркального модуля где полностью другая форма и все другое
/* v1.2.21 - $languages перенес в /admin/config_multilingual.php
 * v1.4.17 - сокращение параметров form
*/

$template= 1;

//только если многоязычный сайт
if ($config['multilingual']) {
	//приоритет пост над гет
	if (isset($post['language'])) $get['language'] = $post['language'];
	if (@$get['language'] == 0) $get['language'] = key($languages);
	$filter[] = array('language', $languages);
	$form[] = '<input name="language" type="hidden" value="'.$get['language'].'" />';
}
else $get['language'] = 1;

//другая струкутра таблицы
$table = array(
	'id'		=> 'rank:desc',
	'name'		=> '',
	'caption'		=> '',
	'color'		=> '',
	'rank'		=> '',
	'display'	=> 'display'
);

$query = "
	SELECT landing_items.*
	FROM landing_items
	WHERE landing_items.language = '".$get['language']."' AND landing_items.template=$template
";

//задаем новую форму
$form[] = '<input name="template" type="hidden" value="'.$template.'" />';
$form[] = array('input td8','name');
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');
$form[] = array('input td8','caption');
$form[] = array('input td4','color');
$form[] = array('tinymce td12','text');//,array('attr'=>'style="height:500px"'));
$form[] = array('file td6','img',array(
	'name'=>'Основная картинка',
	'sizes'=>array(''=>'resize 1000x1000')
));
