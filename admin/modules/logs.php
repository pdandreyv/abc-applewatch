<?php

//v1.4.74 - поиск в логах по ид

if (@$_POST['clear']) mysql_fn('query',"TRUNCATE `logs`");

$config['logs']['type'] = array(
	1	=>	'insert',
	2	=>	'update',
	3	=>	'delete',
);

$config['logs']['modules'] = array();
if ($tables = mysql_select("SHOW TABLES",'rows')) {
	foreach ($tables as $k=>$v) {
		$str = array_shift($v);
		$config['logs']['modules'][$str] = $str;
	}
}

$where = (isset($get['type']) && $get['type']>0) ? 'AND l.type = '.$get['type'].' ' : '';
$where.= (isset($get['user']) && $get['user']>0) ? 'AND l.user = '.$get['user'].' ' : '';
//v1.4.74 - поиск в логах по ид
$where.= @$_GET['parent']>0 ? 'AND l.parent = '.intval($_GET['parent']).' ' : '';
$where.= (@$get['module']) ? "AND l.module = '".mysql_res($get['module'])."' " : '';
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(l.ip) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT l.*,u.email
	FROM logs l
	LEFT JOIN users u ON u.id = l.user
	WHERE 1 $where
";

$filter[] = array('search');
$filter[] = array('user',"SELECT u.id,u.email name FROM users u RIGHT JOIN logs l ON l.user = u.id GROUP BY u.id",'пользователь');
$filter[] = array('type',$config['logs']['type'],'действие');
$filter[] = array('module',$config['logs']['modules'],'таблица');

$content_bottom = '<form action="" style="text-align:right" method="post">
	<input type="hidden" value="1" name="clear">
	<button class="btn btn-light" type="submit" onclick="if(confirm(\'Все логи будут удалены\')) {} else return false;">Очистить</button>
</form>';

$table = array(
	'_edit'=>false,
	'id'=>'id:desc',
	'user'=>'<a href="/admin.php?m=users&id={user}">[{user}] {email}</a>',
	'type'=>$config['logs']['type'],
	'module'=>'<a href="/admin.php?m={module}&id={parent}">{module}->{parent}</a>',
	'fields'=>'text',
	'ip'=>'text',
	'date'=>'text',
	'_delete'=>false
);