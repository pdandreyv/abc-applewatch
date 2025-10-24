<?php

//редактирование формы
/*
 *  v1.4.4 - html_array для таблицы
 *  v1.4.14 - event_func
 *  v1.4.15 - multiple
 *  v1.4.32 - если нужен чистых жейсон
 *  v1.4.56 - права добавления/редактирования
 *  v1.4.59 - hypertext
 *  v1.4.82 - serialize->json
 */

//создание массива post и его бработка
$post = stripslashes_smart($_POST); //error_handler(1,serialize($post),1,1);
$data = array();
//генерация SEO-полей
if (isset($post['seo'])) {
	if($post['seo']==1) {
		$data['seo'] = array();
		//v1.2.94 [3.164] form edit SEO
		if (isset($post['name']) AND isset($post['url'])) $data['seo']['url'] = $post['url'] = trunslit($post['name']);
		if (isset($post['title'])) $data['seo']['title'] = $post['title'] = $post['name'];
		if (isset($post['description'])) $data['seo']['description'] = $post['description'] = description((isset($post['about']) ? $post['about'].' ' : '').(isset($post['text']) ? $post['text'].' ' : '').$post['name']);
		//сеополя для языков
		if ($config['multilingual']) {
			foreach ($config['languages'] as $k => $v) {
				//v1.2.32 - сеополя будут генерироваться только в том случае если модуль описан как зеркальный в /admin/config_multilingual.php
				if (isset($config['lang_fields'][$module['table']])) {
					if (isset($post['name' . $v['id']]) AND isset($post['url' . $v['id']])) $data['seo']['url' . $v['id']] = $post['url' . $v['id']] = trunslit($post['name' . $v['id']]);
					if (isset($post['title' . $v['id']])) $data['seo']['title' . $v['id']] = $post['title' . $v['id']] = $post['name' . $v['id']];
					if (isset($post['description' . $v['id']])) $data['seo']['description' . $v['id']] = $post['description' . $v['id']] = description((isset($post['about' . $v['id']]) ? $post['about'] . ' ' : '') . (isset($post['text' . $v['id']]) ? $post['text' . $v['id']] . ' ' : '') . $post['name' . $v['id']]);
				}
			}
		}
	}
	unset($post['seo']);
}
//дерево сложенности
if (isset($post['nested_sets'])) unset($post['nested_sets']);
//депенды
if (isset($config['depend'][$module['table']])) foreach ($config['depend'][$module['table']] as $key=>$value)
	$post[$key] = isset($post[$key]) ? implode(',',$post[$key]) : '';

//загрузка модуля
require_once(ROOT_DIR.'admin/modules/'.$get['m'].'.php');
//по умолчанию разрешено редактирование
if (!isset($table['_edit'])) $table = array_merge(array('_edit'=>true),$table);

//если нет прав редактировать
if ($table['_edit']===false) die();

//расширяем форму
multilingual();
//dd($post);
//если дерево то удаляем родителя и предыдущего
if (is_array($form)) {
	//v1.4.59 - если нет вкладок то имитирум вкладки
	if (count($tabs)==0) {
		$form = array(
			1=>$form
		);
	}
	foreach ($form as $k => $v) {
		foreach ($v as $k1 => $v1) {
			if (is_array($v1)) {
				if (preg_match('/simple|file_multi/', $v1[0])) {
					//v1.3.8 удаляем ключ temp, он не нужен в базе
					if (isset($post[$v1[1]])) foreach ($post[$v1[1]] as $k=>$v) {
						if (isset($v['temp']) AND $v['temp']=='') unset($post[$v1[1]][$k]['temp']);
					}
					//v1.4.82 - serialize->json
					$post[$v1[1]] = isset($post[$v1[1]]) ? json_encode($post[$v1[1]]) : '';
				}
				//удаляем данные о file_multi_db
				if ($v1[0] == 'file_multi_db' AND isset($post[$v1[1]])) {
					unset($post[$v1[1]]);
				}
				//multicheckbox v1.2.19
				//v1.4.15 - multiple
				if (preg_match('/multicheckbox|multiple/', $v1[0])) {
					//если нет поля значит надо сделать его пустым чтобы можно было снять чекбокс
					if (empty($post[$v1[1]])) $post[$v1[1]] = '';
					elseif (is_array($post[$v1[1]])) {
						$post[$v1[1]] = implode(',', $post[$v1[1]]);
					}
				}
				//v1.4.59 - hypertext
				if (preg_match('/hypertext/', $v1[0])) {
					$post[$v1[1]] = isset($post[$v1[1]]) ? json_encode($post[$v1[1]], JSON_UNESCAPED_UNICODE) : '';
				}
				// сериализация словаря wallpaper_languages (поле dictionary хранит JSON)
				if ($v1[1] == 'dictionary') {
					$post[$v1[1]] = isset($post[$v1[1]]) ? $post[$v1[1]] : '';
				}
			}
		}
	}
}
//dd($post); die();

//редактирование текущей записи
if (intval($get['id']) > 0) {
	//v1.4.56 - если разрешено добавление
	if ($table['_edit']===true OR $table['_edit']=='edit') {
		$post['id'] = $get['id'];
		//сравнение изменений
		$fields = array();
		$post0 = mysql_select("SELECT * FROM `" . $module['table'] . "` WHERE id=" . intval($get['id']), 'row');
		if ($post0) {
			foreach ($post as $k => $v) {
				if ($v != $post0[$k]) $fields[] = $k;
			}
			$fields = implode(',', $fields);
			mysql_fn('update', $module['table'], $post);
			$logs['type'] = 2;
		}
	}
	//если нет прав редактировать запись
	else die();
}
//создание новой записи
else {
	//v1.4.56 - если разрешено добавление
	if ($table['_edit']===true OR $table['_edit']=='add') {
		$post['id'] = $get['id'] = mysql_fn('insert', $module['table'], $post);
		$logs['type'] = 1;
		$fields = '';
	}
	//если нет права создавать новую запись
	else die();
}
$error = mysqli_affected_rows($config['mysql_connect']) == 1 ? 0 : mysqli_error($config['mysql_connect']);

//функция после сохранения
//v1.4.14 - event_func
$event_function = 'event_change_'.$module['table'];
if (!$error AND function_exists($event_function)) {
	$event_function($post);
}

//логирование действия
//if ($error===0) {
	mysql_fn('insert','logs',array(
		'user'		=> $user['id'],
		'date'		=> date('Y-m-d H:i:s'),
		'parent'	=> $get['id'],
		'module'	=> $module['table'],
		'type'		=> $logs['type'],
		'ip'        => get_ip(),
		'fields'    => $fields
	));
//}

//обработка депендов
if (isset($config['depend'][$module['table']])) foreach ($config['depend'][$module['table']] as $key=>$value) {
	$depend = mysql_select("SELECT id,parent name FROM `".$value."` WHERE child = '".intval($get['id'])."'",'array');
	if ($depend==false) $depend = array();
	if ($post[$key]=='' AND count($depend)>0) mysql_fn('delete',$value,false," AND child = '".intval($get['id'])."'");
	elseif ($post[$key]) {
		$depend2 = explode(',',$post[$key]);
		foreach ($depend2 as $k=>$v) {
			if (!in_array($v,$depend))
				mysql_fn('insert',$value,array('child'=>intval($get['id']),'parent'=>intval($v)));
		}
		foreach ($depend as $k=>$v)
			if (is_array($depend2) AND !in_array($v,$depend2))
				mysql_fn('delete',$value,$k);
	}
}

//копирование всех файлов когда сохранить как
if (@$_GET['save_as']>0) {
	rcopy(ROOT_DIR.'files/'.$module['table'].'/'.intval($_GET['save_as']).'/', ROOT_DIR.'files/'.$module['table'].'/'.intval($get['id']).'/');
}

//загрузка файлов
if (is_array($form)) {
	foreach ($form as $k=>$v) {
		foreach ($v as $k1=>$v1) {
			if (is_array($v1) && preg_match('/mysql|simple|file|file_multi|file_multi_db/',$v1[0])) {
				//копирование папок file_multi_db
				if ($v1[0]=='file_multi_db' AND @$_GET['save_as']>0) {
					$file_multi_db = mysql_select("SELECT * FROM `".$v1[1]."` WHERE parent=".intval($_GET['save_as']),'rows');
					foreach($file_multi_db as $row) {
						//старая папка
						$old = ROOT_DIR."files/".$v1[1]."/".$row['id']."/";
						//добавляем запись в БД
						unset($row['id']);
						$row['parent'] = $get['id'];
						$row['id'] = mysql_fn('insert',$v1[1],$row);
						//новая папка
						$new = ROOT_DIR."files/".$v1[1]."/".$row['id']."/";
						if(is_dir($new)||mkdir($new,0755,true)) {
							rcopy($old, $new);
						}
					}
				}

				$data['files'][$v1[1]] = call_user_func_array('form_file', $v1);
				//обновление картинки file в ряде
				if (current(explode(' ',$v1[0]))=='file') $q[$v1[1]] = $post[$v1[1]];
			}
			//hypertext
			if (is_array($v1) && preg_match('/hypertext/',$v1[0])) {
				$files = call_user_func_array('form_file', $v1);
				foreach ($files as $k2=>$v2) {
					$data['files'][$k2] = $v2;
				}
			}
		}
	}
}

//запрос на ряд для одной записи
$query_row = $query ? $query." AND ".$module['table'].".id = '".$get['id']."'" : "SELECT * FROM ".$module['table']." WHERE id = '".$get['id']."'";
$q = mysql_select($query_row,'row');
//для nested_sets при создании новой записи
$data['table'] = '';
if ($q AND array_key_exists('level',$q) AND array_key_exists('left_key',$q)) {
	if ($_GET['id']=='new') {
		$q['level'] = 1;
		$where = '';
		//если есть фильтр (например, для языка)
		if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
			$where.= " AND `".$v[0]."` = ".intval($q[$v[0]]);
		}
		$max = mysql_select("SELECT IFNULL(MAX(right_key),0) FROM ".$module['table']." WHERE 1 ".$where,'string');
		mysql_fn('update',$module['table'],array('level'=>1,'left_key'=>($max+1),'right_key'=>($max+2),'id'=>$get['id']));
		//v1.4.4 нужно для шаблона admin/templates/includes/table/row.php
		$_GET['id'] = $get['id'];
	}
	//перемещение дерева
	if (isset($_POST['nested_sets']['on']) AND $_POST['nested_sets']['on']==1) {
		//вставка типа prev
		if ($_POST['nested_sets']['previous']) nested_sets($module['table'],$_POST['nested_sets']['previous'],$q['id'],'prev',$filter);
		//вставка типа parent
		else nested_sets($module['table'],@$_POST['nested_sets']['parent'],$q['id'],'parent',$filter);
		if (isset($table) AND is_array($table)) {
			$where = '';
			if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
				$where.= " AND ".$module['table'].".".$v[0]." = '".$q[$v[0]]."'";
			}
			$query = $query ? $query.$where : "SELECT ".$module['table'].".* FROM ".$module['table']." WHERE 1 ".$where;
			$data['table'] = table($table,$query);
		}
	}
}

//создание ряда
//$data['tr'] = (is_array($table) AND $data['table']=='') ? table_row($table,$q) : '';
//if ($_GET['id']=='new') $data['tr'] = (isset($q['parent']) ? '<tr class="is_open" data-id="'.$q['id'].'" data-parent="'.$q['parent'].'" data-level="'.$q['level'].'" class="a">' : '<tr class="is_open" data-id="'.$q['id'].'" data-parent="0" data-level="1" class="a">').$data['tr'].'</tr>';
//v1.4.4 - новый ряд даем только если не возвращаем всю таблицу
$data['tr'] = '';
if ($data['table']==false) {
	$array = array(
		'table' => $table,
		'list' => array($q),
		'module'=>$module['table']
	);
	$data['tr'] = html_array('table/row', $array);
}

$data['error']	= $error;
$data['id']		= $get['id'];

//v1.4.32 если нужен чистых жейсон
if (@$_GET['option']=='json') {
	header('Content-type: application/json; charset=UTF-8');
	echo json_encode($data);
}
else {
	echo '<textarea>'.json_encode($data, JSON_HEX_AMP).'</textarea>';
}
