<?php

//функции админпанели
/*
 * v1.4.0 - html_render в админке
 * v1.4.1 - пагинатор в админке
 * v1.4.4 - html_array для таблицы
 * v1.4.5 - html_array для form_file
 * v1.4.7 - admin/template2
 * v1.4.10 - nested_sets - ошибки при многоязычности
 * v1.4.16 - $delete - удалил confirm
 * v1.4.17 - сокращение параметров form
 * v1.4.20 - form - select|multicheckbox|multiple пофиксил ошибку
 * v1.4.30 - table_connections - аналог html_delete
 * v1.4.41 - удаление превью
 * v1.4.45 - form - в параметры можно передавать все что угодно
 * v1.4.59 - hypertext
 * v1.4.66 - hypertext размеры фото
 * v1.4.67 - form_file дублирование fields для hypertext
 * v1.4.82 - serialize->json
 * v1.4.85 - hypertext одна картинка
 * v1.4.89 - admin/template2/includes/filter/
 */

/**
 * кнопка удаления записи из БД и всех её файлов
 * @param string $delete
 * @return string
 * @see $delete
 * v1.4.16 - $delete - удалил confirm
 */
function html_delete($delete='') {
	global $get,$a18n;
	$content = '';
	if ($get['id']>0 && $delete) {
		//если есть связанные записи
		foreach ($delete as $k=>$v) {
			if (strpos($v, ' ')) { //запрос
				if (mysql_select($v,'row')) $content.= '[связанные '.a18n($k).'] ';
			} else {
				$query = 'SELECT `id` FROM `'.$k.'` WHERE `'.$v.'` = '.$get['id'];
				if (mysql_select($query,'row')) {
					if (array_key_exists($k, $a18n))
						$content .= '<a href="admin.php?m='.$k.'&'.$v.'='.$get['id'].'">['.a18n($k).']</a> ';
					else $content .= 'есть связи';
				}
			}
		}
		//если есть связанные записи
		if ($content) return 'удаление невозможно: '.$content;
	}
}
/**
 * аналог html_delete только возвращает массив с данными а не нтмл
 * @param array $delete - масив правил связанных записей
 * @return array - масив связанных записей
 * @see $delete
 * v1.4.30 - добавлена
 */
function table_connections ($delete=array()) {
	global $get,$a18n;
	$connections = array();
	if ($get['id']>0 && $delete) {
		//если есть связанные записи
		foreach ($delete as $k=>$v) {
			if (strpos($v, ' ')) { //запрос
				if (mysql_select($v,'row')) {
					$connections[] = array(
						'name'=>a18n($k)
					);
				}
			}
			else {
				$query = 'SELECT `id` FROM `'.$k.'` WHERE `'.$v.'` = '.$get['id'];
				if (mysql_select($query,'row')) {
					if (array_key_exists($k, $a18n)) {
						$connections[] = array(
							'link'=> 'admin.php?m=' . $k . '&' . $v . '=' . $get['id'],
							'name'=>a18n($k)
						);
					}
					else {
						$connections[] = array(
							'link'=> 'admin.php?m=' . $k . '&' . $v . '=' . $get['id'],
							'name'=>$k
						);
					}
				}
			}
		}
	}
	return $connections;
}


/**
 * функция вывода строк таблицы
 * @param array $table - массив колонок таблицы
 * @param array $q - массив данных ряда
 * @param bool $head - вернуть шапку или ряд
 * @return string - ряд <tr>
 * @see $table
 * @version v1.2.122
 * v1.2.102 - добавлен второй аргумент в 'field' => '::function',
 * v1.2.122 - просмотр на сайте - _view
 */
/*
function table_row($table,$q,$head = false) {
	global $config,$url,$module;
	if (!isset($table['_edit'])) $table = array_merge(array('_edit'=>true),$table);
	elseif ($table['_edit']==false) unset($table['_edit']);
	if (!isset($table['_delete'])) $table['_delete'] = true;
	elseif ($table['_delete']==false) unset($table['_delete']);
	$content = '';
	//ШАПКА ТАБЛИЦЫ
	if ($head) foreach ($table as $k=>$v) {
		//v1.2.130 - чекбоксы для админки
		if ($k=='_check')		$content.= '<th style="text-align:center; padding:0px"><input type="checkbox" name="_check" /></th>';
		elseif ($k=='_tree') $content.= '<th class="colspan" style="padding:0 0 0 10px"><span class="sprite tree" title="дерево вложенности"></span></th>';
		elseif ($k=='_sorting') $content.= '<th class="colspan"><span class="sprite sorting" title="сортировка"></span></th>';
		elseif ($k=='_edit') {
			if ($v==='edit') {
				$content.= '<th style="padding:0; text-align:center"></th>';
			}
			else {
				$content.= '<th style="padding:0; text-align:center"><a class="sprite plus2 open" href="/admin.php?'.$url.'id=new" title="добавить новую запись"></a></th>';
			}
		}
		elseif ($k=='_view') {
			$content.= '<th width="20px"></th>';
		}
		elseif ($k=='_delete') $content.= '<th width="20px"></th>';
		elseif ($k=='display') $content.= '<th></th>';
		elseif ($v=='boolean') $content.= '<th></th>';
		elseif ($v=='img') $content.= '<th></th>';
		else {
			global $get;

			//$fieldset[$k]  = isset($fieldset[$k]) ? $fieldset[$k] : $k; //если нет $fieldset называем ключом
			$content.= '<th>';
			//скрытый селект для быстрого редактирования
			if (is_array($v) AND substr($k,-1)==':') {
				$content.= '<select name="'.$k.'">'.select('',$v).'</select>';
			}
			$k = trim($k,':'); //удаляем двоеточие от селекта
			if (isset($q['sort_array']) && array_key_exists($k,$q['sort_array'])) {
				if ($q['order']==$k) {
					if ($get['s']) $s = ($get['s']=='desc') ? 'asc' : 'desc';
					else $s = $q['sort_array'][$k];
					$a = $s=='asc' ? ' desc' : ' asc';
				}
				else {
					$s = $q['sort_array'][$k];
					$a = ' none '.$s;
				}
				$content.= '<a class="sort'.($q['order']==$k ? ' active' : '').'" href="?'.$url.'o='.$k.'&s='.$s.'"><span class="sprite '.$a.'"></span>'.a18n($k).'</a>';
			}
			else $content.= a18n($k);
			$content.= '</th>';
		}
	}
	//РЯД ТАБЛИЦЫ
	else foreach ($table as $k=>$v) {
		if ($v && !is_array($v)) {
			preg_match_all('/{(.*?)}/',$v,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key=>$val) $matches[1][$key] = isset($q[$val]) ? $q[$val] : '';
			$v = str_replace($matches[0],$matches[1],$v);
		}
		//v1.2.130 - чекбоксы для админки
		if ($k=='_check')		$content.= '<td><input type="checkbox" name="_check" value="'.$q['id'].'"/></td>';
		elseif ($k=='_edit')		$content.= '<td align="center"><a href="/admin.php?'.$url.'id='.$q['id'].'" class="sprite edit open"></a></td>';
		elseif ($k=='_view') {
			$content.= '<td><a class="sprite view" target="_blank" href="'.get_url($v,$q).'"></a></td>';
		}
		elseif ($k=='_tree')	$content.= '<td class="level"><span class="sprite level item"></span></td>';
		elseif ($k=='_sorting')	$content.= '<td><span class="sprite sorting"></span></td>';
		elseif ($k=='_delete')	$content.= '<td align="center"><a class="sprite delete" href="#"></a></td>';
		elseif ($k=='id')		$content.= '<td align="right"><b>'.$q[$k].'</b></td>';
		elseif (is_array($v))	{
			if (substr($k,-1)==':') {
				$k = trim($k,':');
				//$content.= '<td><select name="'.$k.'">'.select($q[$k],$v).'</select></td>';
				$str = '';
				if (isset($q[$k]) AND isset($v[$q[$k]])) {
					$str = is_array($v[$q[$k]]) ? $v[$q[$k]]['name'] : $v[$q[$k]];
				}
				$content.= '<td class="select" data-id="'.$q[$k].'" data-name="'.$k.'">'.$str.'</td>';
			}
			else {
				$str = '';
				if (isset($q[$k]) AND isset($v[$q[$k]])) {
					$str = is_array($v[$q[$k]]) ? $v[$q[$k]]['name'] : $v[$q[$k]];
				}
				$content.= '<td><b>'.$str.'</b></td>';
			}
		}
		elseif ($v=='date')		$content.= '<td data-name="'.$k.'" class="post">'.$q[$k].'</td>';
		elseif ($v=='boolean' OR $v=='display') {
			$key = in_array($k,$config['boolean']) ? $k : 'boolean';
			$content.= '<td align="center" data-name="'.$k.'" data-key="'.$key.'">';//key - клас спрайта для иконки
			$content.= '<a class="sprite '.$key.'_'.($q[$k]==1 ? '1' : '0').' js_boolean" href="#" title="'.a18n($k).'"></a>';
			$content.= '</td>';
		}
		elseif ($v=='right')	$content.= '<td data-name="'.$k.'" align="right" class="post">'.$q[$k].'</td>';
		elseif ($v=='text')		$content.= '<td data-name="'.$k.'"><b>'.$q[$k].'</b></td>';
		elseif ($v=='img')		{
			//v1.2.115 заменил пути на get_img
			$img =  get_img($module['table'],$q,$k,'');
			$preview = get_img($module['table'],$q,$k,'a-');
			$content.= '<td align="center" data-name="'.$k.'">'.($q[$k] ? '<a onclick="return hs.expand(this)" href="'.$img.'"><img class="img" src="'.$preview.'" /></a>' : '').'</td>';
		}
		elseif ($v=='')			$content.= '<td data-name="'.$k.'" class="post">'.(isset($q[$k]) ? $q[$k] : '').'</td>';
		elseif (substr($v,0,2)=='::') {
			$function = substr($v,2);
			//v1.2.102 - добавлен второй аргумент в 'field' => '::function',
			if (function_exists($function)) $content.= $function($q,$k);
			else $content.= '<td>'.$function.'</td>';
		}
		else					$content.= '<td>'.$v.'</td>';
	}
	return $content;
}
*/

/**
 * функция формирования нтмл кода таблицы в админке
 * @param array $table - массив колонок таблицы
 * @param string $query - запрос
 * @return string - нтмл код таблицы
 * @see $table, table_row()
 * v1.4.1 - пагинатор в админке
 */
function table ($table,$query='') {
	global $get,$filter,$module;
	$array_count	= array(20=>20,50=>50,100=>100,'all'=>'all');
	$count			= array_key_exists(@$_GET['c'],$array_count) ? $_GET['c'] : key($array_count);
	if ($count=='all') $count = 0;
	$sorting		= explode(' ',$table['id']);
	$sort_array = array();
	foreach ($sorting as $s) {
		$s = explode(':',$s);
		$sort_array[$s[0]] = (isset($s[1]) && $s[1]=='desc') ? 'desc' : 'asc';
	}
	$tree = array_key_exists('_tree',$table);
	$sorting = array_key_exists('_sorting',$table);
	//ГЕНЕРАЦИЯ $query
	if ($query=='') {
		$query = "SELECT ";
		if ($tree) $query.= $module['table'].'.level,'.$module['table'].'.parent,';
		foreach ($table as $k=>$v) if ($k[0]!='_') $query.= '`'.$k.'`,';
		$query = substr($query,0, -1);
		$query.= " FROM ".$module['table']." WHERE 1";
		//если есть фильтр (например, для языка)
		if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) if (isset($_GET[$v[0]])){
			$query.= " AND ".$module['table'].".".$v[0]." = ".intval($_GET[$v[0]]);
		}
	}
	//НАСТРОЙКА СОРТИРОВКИ
	//деревовидный список
	$th = array();
	if ($tree) {
		$order = $module['table'].".left_key";
		$sort  = '';
	}
	//сортировка
	elseif ($sorting) {
		$order = $module['table'].'.'.$table['_sorting'];
		$sort  = '';
	}
	//обычный список
	else {
	    //добавил для коректного сортирования для селектов где добавлено : к названию
        $table_sort = array();
        foreach ($table as $tk => $tv){
            $table_sort[str_replace(':','',$tk)] = $tv;
        }
        $_GET['o'] = @$_GET['o']?str_replace(':','',$_GET['o']):'';
        $th['order'] = $order = (@$_GET['o'] && array_key_exists($_GET['o'],$table_sort)) ? $_GET['o'] : key($sort_array);
		if (!@$_GET['s']) $_GET['s'] = $sort_array[$order];
		$sort = ($_GET['s']=='desc') ? 'DESC' : 'ASC';
		$th['sort_array'] = $sort_array;
	}

	$order = str_replace('.','`.`',$order);
	$query.= ' ORDER BY `'.$order.'` '.$sort.',id '.$sort;

	$data = mysql_data(
		$query,
		false,
		$count,
		@$_GET['n']
	);
	$data['array_count'] = $array_count;
	$data['type'] = $tree ? 'tree':'';
	$data['type'] = $sorting ? 'sorting':$data['type'];
	$data['table'] = $table;
	$data['module'] = $module['table'];
	$data = array_merge($data,$th);
	//v1.4.4
	return html_render('table/table',$data);
}

/**
 * фильтр, ситнаксис аналогичен select()
 * @param $key - ключ $_GET
 * @param string|array $query - название таблицы | SQL запрос | массив
 * @param string $default - значение по умолчанию
 * @param bool $clear - соединять значения других фильтров либо сбрасывать
 * @return html - html код фильтра
 * v1.4.7 - admin/template2
 * v1.4.89 - admin/template2/includes/filter/
 */


function filter ($key,$query='',$default='',$clear=false) {
	global $get,$config;
	if ($clear==false) $url=build_query($key);
	else $url = 'm='.$_GET['m'];
	if ($query!='') {
		$content = html_render('filter/select',[
			'key'=>$key,
			'url'=>$url,
			'value'=>isset($get[$key]) ? $get[$key] : '',
			'query'=>$query,
			'default'=>$default
		]);
	}

	else {
		if ($key=='date_from' OR $key=='date_to' ) {
			$content = html_render('filter/date',[
				'key'=>$key,
				'url'=>$url
			]);
		}
		else {
			$content = html_render('filter/search', [
				'key' => $key,
				'url' => $url
			]);
		}
	}
	return $content;
}

/**
 * конструктор полей формы
 * @param string $class - тип и класс поля
 * @param string $key - ключ $_GET
 * @param array $param array(
 *  'attr'=>'id="field"',
 *  'value'=>'значение поля'
 *  'name'=>'название поля',
 *  'help'=>'всплывающая подсказка',
 *  'select'=>array() - массив для селекта
 * )
 * @return string
 * @version v1.4.45
 * v1.1.32 - замена iconv на mb
 * v1.2.3 - убрал $lang['i'] в сеополях
 * v1.2.19 - оптимизировал multicheckbox
 * v1.2.70 - карта яндекс
 * v1.2.73 - карта гугл
 * v1.2.77 - правки в картах
 * v1.2.79 - правка в user
 * v1.2.126 - правки в инициализации переменных
 * v1.3.11 - hypertext
 * v1.4.0 - html_render в админке
 * v1.4.15 - multiple
 * v1.4.17 - сокращение параметров form
 * v1.4.20 - select|multicheckbox|multiple пофиксил ошибку
 * v1.4.45 - в параметры можно передавать все что угодно
 */
function form ($class,$key,$param=array()) {
	global $get,$filter,$config,$module,$post; //массив с названиями блоков
	//v1.4.7 - admin/template2
	if ($config['style']!='admin/templates') {
		$class = str_replace('td','col-xl-',$class);
	}
	//атрибуты поля, стили
	$param['attr'] = isset($param['attr']) ? $param['attr'] : '';
	//title
	$param['title'] = isset($param['title']) ? $param['title'] : '';
	//подсказка
	$param['help'] = isset($param['help']) ? $param['help'] : '';
	//название поля, по умолчанию указано в массиве $fieldset
	$param['name'] = isset($param['name']) ? $param['name'] : a18n($key);
	//значение поля - v1.4.17
	$value = isset($param['value']) ? $param['value'] : @$post[$key];
	$type	= current(explode(' ',$class));

	//v1.4.15 - multiple
	if (in_array($type,array('select','multicheckbox','multiple'))) {
		//$value[0] = isset($post[$key]) ? $post[$key] : '';
		//v1.4.20 - select|multicheckbox|multiple пофиксил ошибку
		$value[0] = @$param['value'][0]===true ? @$post[$key] : @$param['value'][0];

	}
	elseif(in_array($type,array('parent','seo','basket'))) {
		$value = isset($post) ? $post : array();
	}

	//v1.4.7 - admin/template2
	$explode	= explode(' ',$class);
	if (in_array('datepicker',$explode)) {
		if ($value=='0000-00-00') $value = '';
	}
	if (in_array('datetimepicker',$explode)) {
		if ($value=='0000-00-00 00:00:00') $value = '';
	}
	//v1.4.45 - в параметры можно передавать все что угодно
	$data = $param;
	$data = array_merge($data,array(
		//'title'=>$param['title'],
		//'help'=>$param['help'],
		//'name'=>$param['name'],
		'class'=>$class,
		//'attr'=>$param['attr'],
		'value'=>$value,
		'key'=>$key
	));

	if ($type=='multicheckbox') {
		//dd($value);
		$data['value'] = is_array($value[0]) ? $value[0] : ($value[0] ? explode(',',$value[0]) : array());
		//dd($val);
		$data['data'] = is_array($value[1]) ? $value[1] : ($value[1] ? mysql_select($value[1],'rows') : array());
		if ($data['data']) {
			//переделка простого массива в многоуровневый
			foreach ($data['data'] as $k=>$v) {
				if (!is_array($v)) {
					$data2 = array();
					foreach ($data['data'] as $k1=>$v1) {
						$data2[] = array('id'=>$k1,'name'=>$v1);
					}
					$data['data'] = $data2;
				}
				break;
			}
		}
	}

	if ($type=='parent') {
		$cl = explode(' ',$class);
		$cl[1] = isset($cl[1]) ? $cl[1] : 'td4';
		$cl[2] = isset($cl[2]) ? $cl[2] : 'td4';
		$previos = 0;
		$parent_array = $previos_array = array();
		if (isset($_GET['id']) && $_GET['id']=='new') {
			$value['left_key'] = $value['right_key'] = $value['parent'] = 0;
			$value['level'] = 1;
			if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
				$value[$v[0]] = isset($get[$v[0]]) ? $get[$v[0]] : '';
			}
		}
		if (isset($value['left_key'])) {
			//если есть фильтр (например, для языка)
			$where = '';
			if (isset($filter) && is_array($filter)) foreach ($filter as $k=>$v) {
				if (isset($value[$v[0]]))
					$where.= " AND ".$v[0]." = '".$value[$v[0]]."'";
			}
			$previos = mysql_select("SELECT id FROM `".$module['table']."` WHERE left_key>".$value['left_key']." AND level=".$value['level']." $where ORDER BY left_key LIMIT 1",'string');
			if ($previos==false) $previos=0;
			$parent_array = "
				SELECT id,name,level,parent
				FROM `".$module['table']."`
				WHERE (left_key<'".$value['left_key']."' OR left_key>'".$value['right_key']."') $where
				ORDER BY left_key
			";
			$previos_array = mysql_select("
				SELECT id,name,level,parent
				FROM `".$module['table']."`
				WHERE parent='".$value['parent']."' AND id!='".$value['id']."' $where
				ORDER BY left_key
			",'array');
			if ($previos_array==false) $previos_array = array();
		}
		$previos_array = array(0=>'В конце списка') + $previos_array;
		$content = form('select '.$cl[1],'nested_sets[parent]',array(
			'value'=>array(isset($value['parent']) ? $value['parent'] : '',$parent_array,'Корень списка'),
			'name'=>'Родитель',
			'help'=>'Запись будет находится в корне списка или внутри выбранного элемента'
		));
		$content.= form('select '.$cl[2],'nested_sets[previous]',array(
			'value'=>array($previos,$previos_array),
			'name'=>'Положение внутри родителя перед',
			'help'=>'Запись будет находится в начале списка или перед выбранным элементом'
		));
		return $content;
	}
	else {
		return html_render('form/'.$type,$data);
	}
}

/**
 * загрузка файлов
 * @param $type - тип загрузки (mysql|simple|file|file_multi|file_milti_db)
 * @param $key - поле в таблице где будут хранится названия файлов
 * @param $name - название блока загрузки
 * @param string $param = array(
 *  'name'=>'имя поля',
 *  'sizes'=>array(''=>'1000x1000'),
 *  'fields'=>array('name'=>'input','title'=>'input','display'=>'checkbox')
 * )
 *  размеров картинки
 * @param array $fields - настройки доп полей для мультизагрузки файлов
 * @return string
 * @version v1.4.85
 * v1.1.16 - функция copy2 для загрузки файлов с генерацией превью
 * v1.1.25 - добавил селект для file_multi и simple
 * v1.2.42 - поправил ошибку с версии v1.1.25
 * v1.3.17 - удаление _imgs
 * v1.4.5 - html_array
 * v1.4.17 - сокращение параметров form
 * v1.4.41 - удаление превью
 * v1.4.66 - hypertext размеры фото
 * v1.4.67 - дублирование fields для hypertext
 * v1.4.82 - serialize->json
 * v1.4.85 - одна картинка
 */
function form_file ($type,$key, $param = array()) {
	global $get,$config,$post,$module;
	//имя поля
	$name = isset($param['name']) ? $param['name'] : a18n($key);
	//размеры картинок
	$param['sizes'] = @$param['sizes'] ? $param['sizes'] : '';
	//доп поля для мультиселекта
	$fields = isset($param['fields']) ? $param['fields'] : array('name'=>'input','title'=>'input','display'=>'checkbox');
	$message = ''; //сообщение с ошибкой
	$relative = 'files/'.$module['table'].'/'.$get['id'].'/'.$key.'/'; //v1.3.17 относительный путь папки
	$root = ROOT_DIR.$relative; //папка от корня основной папки
	$t = current(explode(' ',$type));
	//обычная загрузка файлов если нет нтмл5
	if ($config['uploader']==0) {
		if ($t=='file') $t = 'mysql';
		if ($t=='file_multi') $t = 'simple';
	}
	//обычная загрузка
	if ($t=='simple') {
		//v1.4.82 - serialize->json
		$photos = (isset($post[$key]) && $post[$key]) ? json_decode($post[$key],true) : array();
		$n = $photos ? max(array_keys($photos)) : 0; //порядковый номер в массиве
		//данные объекта
		$q = array(
			'id' => $get['id'],
			$key => $photos
		);
		//удаление лишнего
		if ($get['id']!='new' && is_dir($root) && $handle = opendir($root)) {
			while (false !== ($dir = readdir($handle))) {
				if ($dir!= '.' AND $dir!= '..') {
					//удаление масива если нет картинки
					if (!is_dir($root.$dir)) {
						if (isset($photos[$dir])) unset($photos[$dir]);
					}
					//удаление картинки, если нет масива
					elseif (!array_key_exists($dir,$photos)) {
						delete_all($root.$dir.'/',true);
						//v1.4.41 - удаление превью
						delete_imgs ($module['table'],$get['id']);
					}
				}
			}
			closedir($handle);
		}
		//загрузка файлов
		if ($get['u']=='edit') {
			if (is_dir($root) || mkdir($root,0755,true)) { //создание папки
				$temp = isset($_FILES[$key]['tmp_name']) ? $_FILES[$key]['tmp_name'] : ''; //массив файлов
				if (is_array($temp)) {
					//формируем массив
					foreach($temp as $k1=>$v1) {
						if (is_uploaded_file($v1)) {//проверка записался ли файл на сервер во временную папку
							$n++;
							$file = strtolower(trunslit($_FILES[$key]['name'][$k1])); //название файла
							//успешное копирование файла
							if (copy2 ($v1,$root.$n.'/',$file,$param['sizes'])) {
								$photos[$n] = array(
									'file' => $file,
									'name' => current(explode('.',$_FILES[$key]['name'][$k1],2)),
									'display' => 1,
								);
							}
							else $message.= $file.' ошибка загрузки!<br />';
						}
					}
					//v1.4.82 - serialize->json
					$q[$key] = json_encode($photos);
					mysql_fn('update',$module['table'],$q);
				}
			}
			else $message = 'ошибка создания каталога!';
		}

		$data = array(
			'key'=>$key,
			'name'=>$name,
			'message'=>$message,
			'photos'=>$photos,
			'module'=>$module['table'],
			'item'=>$q,
			'fields'=>$fields
		);
		return html_array('form/file_simple',$data);
	}
	//загрузка с записью в БД
	elseif ($t=='mysql') {
		$file = isset($post[$key]) ? $post[$key] : ''; //название файла
		$root = ROOT_DIR.'files/'.$module['table'].'/'.$get['id'].'/'.$key.'/'; //папка от корня основной папки
		$temp = isset($_FILES[$key]['tmp_name']) ? $_FILES[$key]['tmp_name'] : ''; //error_handler(1,2,3,'-'.serialize($_FILES).'-');
		//данные объекта
		$q = array(
			'id' => $get['id'],
			$key => $file
		);
		$message = '';//сообщение с ошибкой
		if ($get['u']=='edit') {
			if (is_uploaded_file($temp)) {//проверка записался ли файл на сервер во временную папку
				if (is_dir($root)) delete_all($root,false); //удаляем без слеша в конце
				if (is_dir($root) || mkdir ($root,0755,true)) { //создание папок для файла
					$file = strtolower(trunslit($_FILES[$key]['name'])); //название файла
					//успешное копирование файла
					if (copy2 ($temp,$root,$file,$param['sizes'])) {
						$q[$key] = $file;
						$message = 'файл загружен!';
					} else {
						$q[$key] = '';
						$message = 'ошибка загрузки!';
					}
					mysql_fn('update',$module['table'],$q);
				}
				else $message = 'ошибка создания каталога!';
			}
		}
		//шаблон
		$img = get_img($module['table'],$q,$key,'');
		$data = array(
			'key'=>$key,
			'img'=>$img,
			'preview'=>'/_imgs/100x100'.$img,
			'is_file'=>is_file($root.$file),
			'file'=>$file,
			'message'=>$message,
			'name'=>$name
		);
		return html_array('form/file_mysql',$data);
	}
	//загрузка с записью в БД (HTML5)
	elseif ($t=='file') {
		$file = $post[$key] = isset($post[$key]) ? $post[$key] : ''; //название файла
		//данные объекта
		$q = array(
			'id' => $get['id'],
			$key => $file
		);
		if ($get['u']=='edit') {
			//ручное удаление картинки или загрузка новой
			if ($file=='' OR is_numeric($file)) {
				delete_all($root,true);
				//v1.4.41 - удаление превью
				delete_imgs ($module['table'],$get['id']);
			}
			$temp = ROOT_DIR.'files/temp/'.$file.'/'; //временная папка на сервере
			//если название файла целое число и есть временная папка, значит происходит загрузка нового файла
			if (is_numeric($file) AND is_dir($temp) AND $handle = opendir($temp)) {
				$temp_file = ''; //название временного файла на сервере
				while (false !== ($f = readdir($handle))) {
					if (strlen($f)>2 && is_file($temp.$f)) {
						$file = strtolower(trunslit($f));
						$temp_file = $temp.$f;
						break;
					}
				}
				//успешное копирование файла
				if (copy2 ($temp_file,$root,$file,$param['sizes'])) {
					$q[$key] = $file;
				}
				//ошибка
				else {
					$q[$key] = '';
				}
				$post[$key] = $q[$key];
				mysql_fn('update',$module['table'],$q);
				//удаляем временный файл
				delete_all($temp,true);
			}
		}
		$data = array(
			'img'=>get_img($module['table'],$q,$key),
			'name'=>$name,
			'type'=>$type,
			'is_file'=>is_file($root.$file),
			'key'=>$key,
			'item'=>$q,
			'sizes'=>$param['sizes'],
			'module'=>$module['table'],
			'file'=>$file
		);
		return html_array('form/file',$data);
	}
	//обычная загрузка (HTML5)
	if ($t=='file_multi') {
		//error_handler(1,serialize($_FILES),1,1);
		//v1.4.82 - serialize->json
		$photos = (isset($post[$key]) && $post[$key]) ? json_decode($post[$key],true) : array();
		//загрузка файлов
		if ($get['u']=='edit' AND $photos) {
			if ($photos) {
				$update = 0;
				if (is_dir($root) || mkdir($root,0755,true)) { //создание папки
					foreach ($photos as $n=>$val) {
						$temp = ROOT_DIR.'files/temp/'.@$val['temp'].'/';
						//если есть временная папка, то копируем картинку
						if (@$val['temp'] AND $handle = opendir($temp)) {
							$update++;
							$temp_file = ''; //название временного файла на сервере
							$file = '';
							while (false !== ($f = readdir($handle))) {
								if (strlen($f)>2 && is_file($temp.$f)) {
									$file = strtolower(trunslit($f));
									$temp_file = $temp.$f;
									break;
								}
							}
							//успешное копирование файла
							if ($file AND copy2 ($temp_file,$root.$n.'/',$file,$param['sizes'])) {
								$photos[$n]['file'] = $file;
								unset($photos[$n]['temp']);
							}
							else {
								unset($photos[$n]);
							}
							//удаляем временную папку
							delete_all(ROOT_DIR.'files/temp/'.$val['temp'].'/',true);
						}
						//v1.3.8 удаляем значение временной папки
						unset($photos[$n]['temp']);
					}
				}
				//v1.4.82 - serialize->json
				if ($update>0) mysql_fn('update',$module['table'],array('id'=>$get['id'],$key=>$photos ? json_encode($photos) : ''));
			}
		}
		//список загруженых файлов
		if ($get['id']!='new' && is_dir($root)) {
			if ($handle = opendir($root)) {
				while (false !== ($dir = readdir($handle))) {
					if ($dir!= '.' AND $dir!= '..') {
						//удаление масива если нет картинки
						if (!is_dir($root.$dir)) {
							if (isset($photos[$dir])) unset($photos[$dir]);
						}
						//удаление картинки, если нет масива
						elseif (!array_key_exists($dir,$photos)) {
							delete_all($root.$dir.'/',true);
							//v1.4.41 - удаление превью
							delete_imgs ($module['table'],$get['id']);
						}
					}
				}
				closedir($handle);
			}
		}
		$data = array(
			'type'=>$type,
			'key'=>$key,
			'name'=>$name,
			'photos'=>$photos,
			'fields'=>$fields,
			'module'=>$module['table'],
			'item'=>array(
				'id' => $get['id'],
				$key => $photos
			),
		);
		return html_array('form/file_multi',$data);
	}
	//закгрузка многих файлов с записью в другую таблицу (HTML5)
	if ($t=='file_multi_db') {
		//error_handler(1,serialize($post),1,1);
		$photos = false;
		if ($get['id']!='new' OR @$_GET['save_as']>0) {
			$photos = mysql_select("SELECT * FROM `" . $key . "` WHERE `parent`=" . $post['id'] . " ORDER BY n", 'rows');
		}
		$path = 'files/'.$key.'/'; //папка от корня основной папки
		$root = ROOT_DIR.$path; //папка от корня сервера

		//загрузка файлов
		if ($get['u']=='edit') {
			$uploads = isset($_POST[$key]) ? stripslashes_smart($_POST[$key]) : array();
			$i = 1; //сортировка для mysql
			foreach ($uploads as $k=>$v) {
				$uploads[$k]['n'] = $i++;
			}
			if ($photos) foreach ($photos as $k=>$v) {
				//удаление отсутсвующих записей
				if (!isset($uploads[$v['n']])) {
					mysql_fn('delete',$key,$v['id']);
					//удаляем файлы
					delete_all($root.$v['id'].'/', true);
					unset($photos[$k]);
				}
				//обновление существующих
				else {
					$photos[$k]['name'] = $uploads[$v['n']]['name'];
					$photos[$k]['display'] = $uploads[$v['n']]['display'];
					$photos[$k]['n'] = $uploads[$v['n']]['n'];
					unset($uploads[$v['n']]);
					mysql_fn('update',$key,$photos[$k]);
				}
			}
			//error_handler(1,serialize($post),1,1);
			if ($uploads) foreach ($uploads as $n=>$val) {
				//загрузка нового файла
				if (@$val['temp']) {
					$temp = ROOT_DIR . 'files/temp/' . @$val['temp'] . '/';
					//если есть временная папка, то копируем картинку
					if ($handle = opendir($temp)) {
						$temp_file = ''; //название временного файла на сервере
						while (false !== ($f = readdir($handle))) {
							if (strlen($f) > 2 && is_file($temp . $f)) {
								$file = strtolower(trunslit($f));
								$temp_file = $temp . $f;
								break;
							}
						}
						//есть временный файл
						if ($temp_file) {
							$photos[$val['n']] = array(
								'parent'=>$get['id'],
								'n'=>$val['n'],
								'name'=>$val['name'],
								'display'=>$val['display'],
								'img'=>$file
							);
							$photos[$val['n']]['id'] = mysql_fn('insert',$key,$photos[$val['n']]);
							$path2 = $photos[$val['n']]['id'].'/img';
							//успешное копирование файла
							copy2 ($temp_file,$root.$path2.'/',$file,$param['sizes']);
						}
						//удаляем временную папку
						delete_all(ROOT_DIR . 'files/temp/' . $val['temp'].'/' , true);
					}
				}
			}
		}
		$photos2 = array();
		if ($photos) {
			foreach ($photos as $k => $v) {
				$photos2[$v['n']] = $v;
				$photos2[$v['n']]['file'] = $v['img'];
			}
			ksort($photos2);
		}
		$data = array(
			'type'=>$type,
			'key'=>$key,
			'name'=>$name,
			'photos'=>$photos2,
			//todo - доделать поля
			'fields'=>array('name'=>'input','display'=>'checkbox'),
			'module'=>$key,
			'item'=>array(
				'id' => $get['id'],
				$key => $photos
			),
		);
		return html_array('form/file_multi',$data);
	}
	//v1.4.59 - hypertext
	if ($t=='hypertext') {
		$blocks = array();
		$hypertext = $post[$key] ? json_decode($post[$key],true) : array();
		$update = 0;
		$photos2 = array(); //список всех картинок
		
		foreach ($hypertext as $k=>$v) {
			if ($v['type']=='images') {
				$file = '';
				if ($get['u']=='edit' AND @$v['images']) {
					foreach ($v['images'] as $n=>$val) {
						$temp = ROOT_DIR.'files/temp/'.@$val['temp'].'/';
						//если есть временная папка, то копируем картинку
						if (@$val['temp'] AND $handle = opendir($temp)) {
							$update++;
							$temp_file = ''; //название временного файла на сервере
							while (false !== ($f = readdir($handle))) {
								if (strlen($f) > 2 && is_file($temp . $f)) {
									$file = strtolower(trunslit($f));
									$temp_file = $temp . $f;
									break;
								}
							}
							//успешное копирование файла
							if (copy2($temp_file, $root . $k.'_'.$n . '/', $file, $param['sizes'])) {
								$hypertext[$k]['images'][$n]['file'] = $file;
								unset($hypertext[$k]['images'][$n]['temp']);
							}
							else unset($hypertext[$k]['images'][$n]);
							//удаляем временную папку
							delete_all(ROOT_DIR . 'files/temp/' . $val['temp'] . '/', true);
						}
						//удаляем значение временной папки
						unset($hypertext[$k]['images'][$n]['temp']);
						//список всех картинок
						$photos2[$k.'_'.$n] = $hypertext[$k]['images'][$n]['file'];
						//v1.4.66 - hypertext размеры фото
						if ($size = getimagesize($root . $k.'_'.$n . '/'.$hypertext[$k]['images'][$n]['file'])) {
							$hypertext[$k]['images'][$n]['size'] = $size[0].'x'.$size['1'];
						}
						else $hypertext[$k]['images'][$n]['size'] = '123';

					}
				}

				//v1.4.67 дублирование fields admin/templates2/includes/form/hypertext_images.php
				$fields = array('name'=>'input','title'=>'input','display'=>'checkbox');

				$photos = isset($hypertext[$k]['images']) ? $hypertext[$k]['images'] : array();
				$data = array(
					'type'=>'file_multi file_hypertext',
					'key'=>$key.'['.$k.'][images]',
					'n'=>$k,
					'field'=>$key,
					'name' => '',
					'photos' => $photos,
					'fields' => $fields,
					'module' => $module['table'],
					'item' => array(
						'id' => $get['id'],
						$key => $hypertext//$photos
					),
				);
				//log_add('fields.txt',$data);
				//v1.4.66 - добавил ключ $key
				$blocks[$key.'['.$k.'][images]'] = html_array('form/file_multi', $data);
			}

			//v1.4.85 - одна картинка
			if ($v['type']=='img') {
				$param['sizes'] = '';//array(''=>'resize 1000x1000');
				$root2 = $root . $k . '/';
				$file = isset($v['img']) ? $v['img'] : ''; //название файла

				if ($get['u'] == 'edit') {
					//ручное удаление картинки или загрузка новой
					if ($file == '' OR is_numeric($file)) {
						delete_all($root2, true);
						//v1.4.41 - удаление превью
						delete_imgs($module['table'], $get['id']);
					}
					$temp = ROOT_DIR . 'files/temp/' . $file . '/'; //временная папка на сервере
					//если название файла целое число и есть временная папка, значит происходит загрузка нового файла
					if (is_numeric($file) AND is_dir($temp) AND $handle = opendir($temp)) {
						$file_name = '';
						$temp_file = ''; //название временного файла на сервере
						while (false !== ($f = readdir($handle))) {
							if (strlen($f) > 2 && is_file($temp . $f)) {
								$file_name = strtolower(trunslit($f));
								$temp_file = $temp . $f;
								break;
							}
						}
						if ($file_name) {
							$update = 1;
							//успешное копирование файла
							if (copy2($temp_file, $root2, $file_name, $param['sizes'])) {
								$hypertext[$k]['img'] = $file_name;
								//unset($hypertext[$k]['temp']);
								$photos2[$k] = $file_name;
							}
							//ошибка
							else {
								$hypertext[$k]['img'] = '';
							}
						}
						//удаляем временный файл
						delete_all($temp, true);

						//данные объекта
						$q = array(
							'id' => $get['id'],
							$key . '/' . $k => $file_name
						);
						$data = array(
							'img' => get_img($module['table'], $q, $key . '/' . $k),
							'name' => '',
							'type' => 'file',
							'is_file' => is_file($root2 . $file_name),
							'key' => $key . '[' . $k . '][img]',
							'item' => $q,
							'sizes' => $param['sizes'],
							'module' => $module['table'],
							'file' => $file_name
						);
						$blocks[$key . '[' . $k . '][img]'] = html_array('form/file', $data);
					}
				}
			}
		}

		//удаляем картинки
		if ($get['id']!='new' && is_dir($root)) {
			if ($handle = opendir($root)) {
				while (false !== ($dir = readdir($handle))) {
					if ($dir!= '.' AND $dir!= '..') {
						//удаление картинки, если нет масива
						if (!array_key_exists($dir,$photos2)) {
							delete_all($root.$dir.'/',true);
							//v1.4.41 - удаление превью
							delete_imgs ($module['table'],$get['id']);
						}
					}
				}
				closedir($handle);
			}
		}

		//если были изменения по картинкам то обновляем
		if ($update>0) {
			$hypertext = $hypertext ? json_encode($hypertext,JSON_UNESCAPED_UNICODE) : '';
			mysql_fn('update',$module['table'],array('id'=>$get['id'],$key=>$hypertext));
		}

		return $blocks;
	}
}

//верхнее меню модулей
/*
function head ($modules,$m='') {
	global $user;
	$top=$bottom='';
	$parent = $child = 0;
	$modules = array_merge_recursive(array('<span class="sprite home"></span>'=>'index'),$modules);
	foreach ($modules as $key => $value) {
		if (is_array($value)) {
			$i=0;
			if (in_array($m, $value)) {
				foreach ($value as $k=>$v) {
					if (access('admin module',$v)) {
						$parent++;
						$child++;
						$i++;
						if ($i==1) $top.='<a href="/admin.php?m='.$v.'" class="a">'.a18n($key).'</a>';
						$link = $m==$v ? ' class="a"' : '';
						$bottom.='<a href="/admin.php?m='.$v.'"'.$link.'>'.a18n($k).'</a>';
					}
				}
			}
			else {
				foreach ($value as $k=>$v) {
					if (access('admin module',$v)) {
						$parent++;
						$top.='<a href="/admin.php?m='.$v.'">'.a18n($key).'</a> ';
						break;
					}
				}
			}
		}
		elseif (access('admin module',$value)) {
			$parent++;
			$link = $m==$value ? ' class="a"' : '';
			$top.='<a href="/admin.php?m='.$value.'"'.$link.'>'.a18n($key).'</a>';
		}
	}
	if ($parent>1)
		return '<div class="menu_parent gradient">'.$top.'</div>'.(($bottom AND $child>1) ? '<div class="menu_child corner_bottom">'.$bottom.'<div class="clear"></div></div>' : '');
}
*/

/**
 * дерево вложенности
 * @param $m - название таблицы
 * @param $id - ИД принимающей ветки
 * @param $selected - ИД перемещаемой ветки
 * @param $insert - тип вставки (prev - вставка перед веткой $id, parent - вставка в ветку $id в конец)
 * @param array $filter - фильтр для дерева, например язык, для каждого свое дерево будет
 * @return bool|string
 * @version v1.2.103
 * v1.2.103 - InnoDB и трансакции
 * v1.4.10 - ошибки при многоязычности
 */
function nested_sets($m,$id,$selected,$insert,$filter=array()) {
	//v1.2.103 старт трансакции
	if (mysql_transaction('start')) {
		//перемещаемый
		$selected = mysql_select("
			SELECT *
			FROM " . $m . "
			WHERE id = '" . intval($selected) . "'
		", 'row');
		if ($selected==false) return 'ошибка родителя!';

		//если дерево многослойное и есть фильтр
		$where = '';
		if (isset($filter) && is_array($filter)) foreach ($filter as $k => $v) {
			$where .= " AND `" . $v[0] . "` = " . $selected[$v[0]];
		}
		//принимающий
		if ($id) {
			$id = mysql_select("
				SELECT *
				FROM " . $m . "
				WHERE id = '" . intval($id) . "' $where
			", 'row');
			if ($id==false) return 'ошибка выборки родителя!';
		}
		//если ид равно нулю, так для parent
		else {
			if ($insert == 'prev') {
				return 'не указан предыдущий!';
			}
		}

		//количество переносимых записей * 2
		$dbl_count = $selected['right_key'] - $selected['left_key'] + 1;
		//имитация удаления узла - level делаем минусовым для отличия
		$query = "
			UPDATE " . $m . "
			SET level = (0 - level),
				left_key = (left_key - " . $selected['left_key'] . " + 1),
				right_key = (right_key - " . $selected['left_key'] . " + 1)
			WHERE left_key>=" . $selected['left_key'] . "
				AND right_key<=" . $selected['right_key'] .
			$where; //echo $query.'<br />';
		mysql_fn('query', $query);
		//пересортировка после псевдоудаления для всеx у кого level>0 (те что level<0 считаются удаленными)
		$query = "
	        UPDATE " . $m . "
			SET left_key = CASE WHEN left_key > " . $selected['left_key'] . "
								THEN left_key - " . $dbl_count . "
								ELSE left_key END,
				right_key = right_key - " . $dbl_count . "
			WHERE right_key > " . $selected['right_key'] . "
				AND level > 0" .
			$where; //echo $query.'<br />';
		mysql_fn('query', $query);

		//обновляем принимающий, т.к. была произведена пересортировка шагом ранее
		if (is_array($id))
			$id = mysql_select("
				SELECT *
				FROM " . $m . "
				WHERE id = '" . $id['id'] . "'
			", 'row');
		else
			$id = array(
				'id' => 0,
				'right_key' => intval(mysql_select("SELECT IFNULL(MAX(right_key),0) FROM " . $m . " WHERE " . $m . ".level>0 " . $where, 'string')) + 1,
				'level' => 0
			);
		//вставка в конец узла ======================
		if ($insert == 'parent') {
			//подготовка для создания создания нового узла
			//пересортировка - освобождение места для нового узла
			if ($id['id'] > 0) {
				$query = "
					UPDATE " . $m . "
					SET right_key = right_key + " . $dbl_count . ",
						left_key = CASE WHEN left_key > " . $id['right_key'] . "
										THEN left_key + " . $dbl_count . "
										ELSE left_key END
					WHERE right_key >= " . $id['right_key'] . "
						AND level > 0" .
					$where; //echo $query.'<br />';
				mysql_fn('query', $query);
			}
			//имитация создания нового узла
			$shift = $id['right_key'] - 1;
			$level = $id['level'] + 1 - $selected['level'];
			$query = "
				UPDATE " . $m . "
				SET level = (0 - level + " . $level . "),
					left_key = (left_key + " . $shift . "),
					right_key = (right_key + " . $shift . ")
				WHERE level < 0" .
				$where; //echo $query.'<br />';
			mysql_fn('query', $query);
			//обновление родителя
			$query = "
				UPDATE " . $m . "
				SET parent = " . $id['id'] . "
				WHERE id = " . $selected['id'] .
				$where; //echo $query.'<br />';
			mysql_fn('query', $query);
			//вставка перед узлом ======================
		} elseif ($insert == 'prev') {
			//подготовка для создания создания нового узла
			//пересортировка - освобождение места для нового узла
			mysql_fn('query', "
				UPDATE " . $m . "
				SET right_key = right_key + " . $dbl_count . ",
					left_key = CASE WHEN left_key >= " . $id['left_key'] . "
								THEN left_key + " . $dbl_count . "
								ELSE left_key END
				WHERE right_key > " . $id['left_key'] . "
					AND level > 0" .
				$where
			);
			//имитация создания нового узла
			$shift = $id['left_key'] - 1;
			$level = $id['level'] - $selected['level'];
			mysql_fn('query', "
				UPDATE " . $m . "
				SET level = (0 - level + " . $level . "),
					left_key = (left_key + " . $shift . "),
					right_key = (right_key + " . $shift . ")
				WHERE level < 0" .
				$where
			);
			//обновление родителя
			mysql_fn('query', "
				UPDATE " . $m . "
				SET parent = " . $id['parent'] . "
				WHERE id = " . $selected['id'] .
				$where
			);
		}
		//проверка
		$where = '';
		if (isset($filter) && is_array($filter)) foreach ($filter as $k => $v) {
			if (isset($id[$v[0]])) $where .= " AND t1." . $v[0] . " = " . $selected[$v[0]] . " AND t2." . $v[0] . " = " . $selected[$v[0]] . "";
		}
		$num_rows = mysql_select("
			SELECT t1.*,t2.*
			FROM " . $m . " AS t1, " . $m . " AS t2
			WHERE (t1.left_key = t2.left_key OR t1.right_key = t2.right_key)
				AND t1.id!=t2.id " .
			$where . "
		", 'num_rows');
		if ($num_rows > 0) {
			//v1.2.103 откат трансакции
			mysql_transaction('rollback');
			//log_add('nested_sets',array($m,$id,$selected,$insert));
			return 'error nested sets!';
		}
		else {
			//v1.2.103 завершение трансакции
			mysql_transaction('commit');
		}
		return true;
	}
	return 'transaction already started!';
}

/**
 * добавляем в форму поля и вкладки
 * version v1.2.3
 * v1.2.3 - добавлена
 */
function multilingual() {
	global $config,$tabs,$form,$get;
	if ($config['multilingual']) {
		if (isset($config['lang_fields'][$get['m']])) {
			foreach ($config['languages'] as $lang) if ($lang['id']!=1) {
				//вкладки
				$tabs['lang' . $lang['id']] = $lang['name'];
				//поля
				foreach ($config['lang_fields'][$get['m']] as $k=>$v) {
					//добавляем ИД к имени поля
					$v[1].= $lang['id'];
					$form['lang' . $lang['id']][] = $v;
				}
			}
		}
	}
}