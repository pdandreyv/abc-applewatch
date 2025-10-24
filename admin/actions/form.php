<?php

// FORM - ЗАГРУЗКА ФОРМЫ РЕДАКТИРОВАНИЯ
/*
 * v1.4.0 - html_render в админке
 */

if ($post = mysql_select("SELECT * FROM ".$module['table']." WHERE id = '".intval($get['id'])."'",'row')) {
	foreach ($filter as $f) {
		if (isset($post[$f[0]])) $get[$f[0]] = $post[$f[0]];
	}
	//создание масива $post[depend]
	if (isset($config['depend'][$module['table']])) {
		foreach ($config['depend'][$module['table']] as $k=>$v) {
			$post['depend'][$v] =  mysql_select("SELECT parent FROM `$v` WHERE child = '".intval($get['id'])."'",'rows');
			/*$result = mysql_query("SELECT parent FROM `$v` WHERE child = '".intval($get['id'])."'");
			while ($q = mysql_fetch_assoc($result)) {
				$post['depend'][$v][] = $q['parent'];
			}*/
		}
	}
//значения по умолчанию для новой записи
} else {
	$post = $get;
	$post['date'] = $config['datetime'];
	$post['rank'] = $post['seo'] = $post['change'] = $post['display'] = $post['indexing'] = 1;
	$post['user'] = $user['id'];
}
require_once(ROOT_DIR.'admin/modules/'.$get['m'].'.php');
//расширяем форму
multilingual();
require_once(ROOT_DIR . $config['style'].'/includes/layouts/form.php');
