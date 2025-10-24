<?php

//лог писем
/*
 * v1.4.17 - сокращение параметров form
 */

if (@$_GET['clear']==1) mysql_fn('query',"TRUNCATE letters");

if (@$_GET['letter']) {
	die(mysql_select("SELECT text FROM letters WHERE id=".intval($_GET['letter']),'string'));
}

$a18n['subject']		= 'тема рассылки';

$filter[] = array ('search');
$filter[] = array('sent',array(1=>'на очереди',2=>'отправленные'));
$filter[] = '<a href="?m=letters&clear=1" onclick="if(confirm(\'подтвердите\')) {} else return false">удалить все письма</a>';

$where = @$_GET['sent']==1? " AND date_sent=0":"";
$where.= @$_GET['sent']==2? " AND date_sent!=0":"";

if (@$get['search']!='') {
	$like = " like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'";
	$where.= "
	AND (
		LOWER(receiver) $like
		OR LOWER(sender) $like
	)
";
}

$query = "
	SELECT *
	FROM letters
	WHERE 1 $where
"; //echo $query;

$table = array(
	'id'			=>	'id:desc date',
	'subject'		=>	'',
	'sender'		=>	'',
	'sender_name'	=>	'',
	'receiver'		=>	'',
	'date'			=>	'date',
	'date_sent'		=>	'date'
);

$form[] = array('input td8','subject');
$form[] = array('text td2','',array('name'=>'<a target="_blank" href="/api/cron/letters?id='.@$get['id'].'">отправить письмо</a>'));
$form[] = array('text td2','',array('name'=>'<a target="_blank" href="?m=letters&letter='.@$get['id'].'">посмотреть письмо</a>'));
$form[] = array('input td2','sender');
$form[] = array('input td2','sender_name');
$form[] = array('input td2','receiver');
$form[] = array('input td2','date');
$form[] = array('input td2','date_sent');
$form[] = array('textarea td12','text',array('attr'=>'style="height:300px"'));