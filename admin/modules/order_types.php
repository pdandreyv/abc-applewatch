<?php

//типы заказов
/*
 * v1.4.16 - $delete удалил confirm
 * v1.4.17 - сокращение параметров form
 */

$table = array(
	'id'		=>	'rank',
	'name'		=>	'',
	'rank'		=>	'',
	'count'		=>	'::table_count',
	'total'		=>	'::table_total',
	'display'	=>	'display'
);

function table_count ($q) {
	$content = '<td>';
	$content.= mysql_select("SELECT id FROM orders WHERE type=".$q['id'],'num_rows');
	$content.= '</td>';
	return $content;
}
function table_total ($q) {
	$content = '<td>';
	$content.= mysql_select("SELECT SUM(total) FROM orders WHERE type=".$q['id'],'string');
	$content.= '</td>';
	return $content;
}

$query = "
	SELECT *
	FROM order_types
	WHERE 1
";

$delete = array('orders'=>'type');

$tabs = array(
	1=>'Общее',
);

$form[1][] = array('input td4','name');
$form[1][] = array('input td1','rank');
$form[1][] = array('checkbox','display');
$form[1][] = array('textarea td12','text');