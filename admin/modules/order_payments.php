<?php

$table = array(
	'id'		=> 'rank',
	'name'		=> '',
	'merchant'	=> @$config['merchants'],
	'rank'		=> '',
	'display'	=> 'display'
);

$content = '<div style="margin:10px 0 0; padding:5px 10px; font:12px/14px Arial; background:#DFE0E0; border-radius:3px">
	Платежные агрегаторы <a target="_blank" href="/admin.php?m=config#2">настроить</a>
</div>';

$form[] = array('input td4','name');
$form[] = array('select td4','merchant',array('value'=>array(true,@$config['merchants'])));
$form[] = array('input td1 right','rank');
$form[] = array('checkbox','display');
$form[] = array('textarea td12','text');