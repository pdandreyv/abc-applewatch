<?php

//роли пользователей
/*
 * v1.4.14 - event_func
 * v1.2.66 - добавлена
 */

//исключение при редактировании модуля
if ($get['u']=='edit') {
	$post['access_admin'] = @$post['access_admin'] ? serialize($post['access_admin']) : '';
	$post['access_editable'] = @$post['access_editable'] ? serialize($post['access_editable']) : '';
}

//v1.4.14 - event_func
function event_change_user_types($q) {
	global $user;
	//переавторизация если меняем права доступа
	if ($user['type']==$q['id']) {
		$user = user('re-auth');
	}
}

$a18n['ut_name']		= 'название';
$a18n['access_delete']	= 'доступ к удалению';
$a18n['access_ftp']		= 'доступ к ftp';

$table = array(
	'id'			=>	'id',
	'ut_name'		=>	'',
	'access_delete'	=>	'boolean',
	'access_ftp'	=>	'boolean',
);

foreach ($modules_admin as $key => $value) {
	$value['name'] = isset($value['name']) ? $value['name'] : $value['module'];
	if (is_array($value['module'])) {
		$list[] = array('id'=>'','name'=>a18n($value['name']),'level'=>1);
		foreach ($value['module'] as $k=>$v) {
			$v['name'] = isset($v['name']) ? $v['name'] : $v['module'];
			$list[]= array('id'=>$v['module'],'name'=>a18n($v['name']),'level'=>2);
		}
	}
	else $list[] = array('id'=>$value['module'],'name'=>a18n($value['name']),'level'=>1);
}
$access_editable_array = array(
	array('id'=>'dictionary','name'=>'Словарь'),
	array('id'=>'pages','name'=>'Страницы'),
	array('id'=>'news','name'=>'Новости'),
	array('id'=>'shop_products','name'=>'Товары'),
	array('id'=>'shop_categories','name'=>'Категории'),
	array('id'=>'shop_brands','name'=>'Производители'),
	array('id'=>'shop_reviews','name'=>'Отзывы'),
	array('id'=>'user_fields','name'=>'Параметры пользователей'),
	array('id'=>'order_deliveries','name'=>'Доставка'),
);

$access_admin = (isset($post['access_admin']) && $post['access_admin']) ? unserialize($post['access_admin']) : array();
$access_editable = (isset($post['access_editable']) && $post['access_editable']) ? unserialize($post['access_editable']) : array();

if ($config['style']=='admin/template') {
	$form[] = array('multicheckbox td4 f_right tr4', 'access_admin', array(
		'value'=>array($access_admin, $list),
		'name' => 'админпанель'
	));
//$form[] = array('multicheckbox td4 f_right tr4','access_editable',array($access_editable,$access_editable_array),array('name'=>'быстрое редактирование (<a href="/admin.php?m=config">on/off</a>)','style'=>'size="20"'));
	$form[] = array('input td8', 'ut_name');
	$form[] = array('checkbox td4 line', 'access_delete');
	$form[] = array('checkbox td4 line', 'access_ftp');
}
else {
	$form[] = '<div class="col-xl-8"><div class="form-row">';
	$form[] = array('input td12', 'ut_name');
	$form[] = array('checkbox td6 line', 'access_delete');
	$form[] = array('checkbox td6 line', 'access_ftp');
	$form[] = '</div></div>';
	$form[] = array('multicheckbox td4 tr5', 'access_admin', array(
		'value'=>array($access_admin, $list),
		'name' => 'админпанель'
	));

}