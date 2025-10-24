<?php

//социальные профили
/*
 * v1.2.66 - добавлена
 * v1.4.17 - сокращение параметров form
 * v1.4.48 - удалил дату
 */

$a18n['name'] = 'Имя';
$a18n['type'] = 'Сайт';

$table = array(
	'id'		=>	'date:desc name url user title id',
	'avatar'    =>  '::table_img',
	'email'     =>  '',
	'name'		=>	'',
	'surname'	=>	'',
	'gender'    =>  $config['user_socials']['genders'],
	'birthday'	=>	'date',
	'type'      =>  $config['user_socials']['types'],
	'created_at'=>	'date_smart',
	'last_visit'=>	'date_smart',
	//'display'	=>	'boolean'
);
function table_img($q) {
	global $config;
	$content = '';
	$link = $q['link'];
	if ($q['avatar']) {
		$content = '<a target="_blank" href="'.$link.'"><img style="width:30px" src="'.$q['avatar'].'"/></a>';
	}
	else $content = '<a target="_blank" href="'.$link.'">'.$config['user_socials']['types'][$q['type']].'</a>';
	return '<td>'.$content.'</td>';
}

$where = (isset($get['type']) && $get['type']>0) ? "AND user_socials.type = '".$get['type']."' " : "";
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(user_socials.email) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(user_socials.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(user_socials.surname) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT *
	FROM user_socials
	WHERE 1 $where
";

//$form[] = array('user td4','user');
$form[] = array('select td4','user',array(
	'value'=>array(true,"SELECT id,email name FROM users WHERE id='".@$post['user']."'"),
	//v1.4.28 - автозаполенние
	'attr'=>'data-url="/admin.php?m=orders&u=get_users"'
));
$form[] = array('select td4','type',array('value'=>array(true,$config['user_socials']['types'])));
//$form[] = array('input td3','date',array('attr'=>'class="datepicker"'));
$form[] = array('input td4','last_visit',array('attr'=>'class="datepicker"'));


$form[] = array('input td4','email');
$form[] = array('input td4','name');
$form[] = array('input td4','surname');
//$form[] = array('checkbox','display',true);

$form[] = array('select td4','gender',array('value'=>array(true,$config['user_socials']['genders'],'')));
$form[] = array('input td4','birthday',array('attr'=>'class="datepicker"'));
$form[] = array('input td4','uid');

$form[] = array('input td4','login');
$form[] = array('input td4','avatar');
$form[] = array('input td4','link');
