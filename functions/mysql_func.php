<?php

//функции для работы с БД
/*
 * v1.3.37 - created_at - mysql_fn
 * v1.4.20 - created_at пофиксил ошибку - mysql_fn
 * v1.4.61 - обрыв строки для num_rows
 * v1.4.72 - $query_nr
 */

/**
 * соединение с БД
 * @param string $server
 * @param string $username
 * @param string $password
 * @param string $database
 * @return bool - подключено или нет
 * @version v1.2.52
 * v1.2.52 - переделка на mysqli
 */
function mysql_connect_db($server='',$username='',$password='',$database='') {
	global $config;
	if (@$config['mysql_connect']==false) {
		//если подключение без параметров то используем данные из $config
		if ($server=='') {
			$server		= $config['mysql_server'];
			$username	= $config['mysql_username'];
			$password	= $config['mysql_password'];
			$database	= $config['mysql_database'];
		}
		if ($config['mysql_connect'] = @mysqli_connect($server,$username,$password)) {
			$connect = $config['mysql_connect'];
			if (mysqli_select_db($connect,$database)) {
				mysqli_query($connect,"SET NAMES '" . $config['mysql_charset'] . "'");
				mysqli_query($connect,"SET CHARACTER SET '" . $config['mysql_charset'] . "'");
				//сброс настроек, чтобы нормально проходили инсерты и апдейты
				//например когда не передают значение поля у которого нет значения по умолчанию
				mysqli_query($connect,"SET @@GLOBAL.sql_mode= ''");
				mysqli_query($connect,"SET @@SESSION.sql_mode= ''");
				mysqli_query($connect,"SET innodb_strict_mode='OFF'");
				return $config['mysql_connect'];
			}
			$config['mysql_error'] = 'cannot connect to database';
			trigger_error($config['mysql_error'], E_USER_DEPRECATED);
			return false;
		}
		$config['mysql_error'] = 'cannot connect to mysql server';
		trigger_error($config['mysql_error'], E_USER_DEPRECATED);
		return false;
	}
	else return $config['mysql_connect'];
}

/**
 * отключение соединения с БД
 * @version v1.2.52
 * v1.2.6 - добавлена
 * v1.2.52 - переделка на mysqli
 */
function mysql_close_db() {
	global $config;
	if (@$config['mysql_connect']) {
		mysqli_close($config['mysql_connect']);
		$config['mysql_connect'] = false;
	}
}

/**
 * своя mysql_real_escape_string
 * @param string $str - строка для экранирования
 * @return string - экранированное значение
 * @version v1.2.52
 * v1.2.52 - переделка на mysqli
 */
function mysql_res ($str) {
	if ($connect = mysql_connect_db()) return mysqli_real_escape_string($connect,$str);
	return false;
}


/**
 * выборка с БД
 * @param string $query - SQL запрос
 * @param string $type - тип данных ответа [string,num_rows,row,rows,rows_id,array]
 * string - строка, одна ячейка из запроса SELECT name FROM ..
 * num_rows - количество записей
 * row - одна строка, массив - SELECT id,name,text .. LIMIT 1 => array('id'=>'12','name'=>'Название','text'=>'текст')
 * rows - массив из row
 * rows_id массив из row где ключем будет id
 * array - массив $k->$v - SELECT id,name .. FROM LIMIT 1 => array(1=>'значение',2=>'значение')
 * @param int $cache - время жизни кеша в секундах
 * @return array|int|string - данные с базы
 * @version v1.2.52
 * v1.1.13 - временные метки для лога скл запросов
 * v1.1.28 - правильный тип данных в mysql_select
 * v1.2.52 - переделка на mysqli
 */
function mysql_select($query,$type='rows',$cache=false) {
	global $config;
	$config['cache'] = isset($config['cache']) ? $config['cache'] : 0;
	$file	= ROOT_DIR.'cache/'.md5($query).'.php';
	//используем кеш
	if ($config['cache'] && $cache && file_exists($file) && (time()-$cache)<filemtime($file)) {
		$config['queries'][] = array(md5($query).'.php',$query);
		$result = file_get_contents ($file);
		return json_decode($result,true);
	}
	//запросы к БД
	else {
		//$config['queries'][] = $query;
		if (($connect = mysql_connect_db()) !== false) {
			$time = microtime(true);
			$result = mysqli_query($connect,$query); //echo $query;
			//сделано, чтобы функция возвращала правильный тип данных даже если нет результатов
			if (in_array($type,array('string','num_rows'))) $data = '';
			else $data = array();
			if ($error = mysqli_error($connect)) {
				trigger_error($error.' '.$query, E_USER_DEPRECATED);
				return $data;
			}
			//строка
			if ($type=='string')		{
				//$data = @mysql_result($result,0);
				$numrows = mysqli_num_rows($result);
				if ($numrows)
				{
					mysqli_data_seek($result, 0);
					$resrow = mysqli_fetch_row($result);
					if (isset($resrow[0]))
					{
						$data = $resrow[0];
					}
				} else {
					$data = false;
				}
			}
			//количество записей
			elseif ($type=='num_rows')	$data = mysqli_num_rows($result);
			//один ряд массивом
			elseif ($type=='row')		$data = mysqli_fetch_assoc($result);
			//несколько рядов двоуровневым массивом
			elseif ($type=='rows')		while ($q = mysqli_fetch_assoc($result)) $data[] = $q;
			//несколько рядов двоуровневым массивом с ключем ИД
			elseif ($type=='rows_id')	while ($q = mysqli_fetch_assoc($result)) $data[$q['id']] = $q;
			// несколько рядов с выдачей поля Field (используется для просмотра доступных полей таблицы)
			elseif ($type=='rows_field')	while ($q = mysqli_fetch_assoc($result)) $data[$q['Field']] = $q;
			//массив {id}->{name}
			elseif ($type=='array')		while ($q = mysqli_fetch_assoc($result)) $data[$q['id']] = $q['name'];
			//кеширование
			if (@$config['cache'] && $cache) {
				if (is_dir(ROOT_DIR.'cache') || mkdir(ROOT_DIR.'cache',0755,true)) {
					$f = fopen($file,'w');
					fwrite($f,json_encode($data));
					fclose($f);
				}
			}
			$time = microtime(true)-$time;
			$config['queries'][] = array($time,$query);
			return $data;
			//возвращаем false если пустой массив (пустой результат запросов array,rows_id,rows)
			//return (is_array($data) AND count($data)==0) ? false : $data;
		}
	}
}

/**
 * @param $query - запрос без лимита
 * @param string $query_nr - запрос для количества строк
 * @param int $limit - количество записей на старницу
 * @param int $n - номер страницы пагинатора
 * @param bool $cache
 * @return array list - массив данных, limit - $limit, n - $n, num_rows - количество записей всего
 * @version v1.2.110
 * v1.2.110 - добавлена
 * v1.4.61 - обрыв строки для num_rows
 * v1.4.72 - $query_nr
 */
function mysql_data($query,$query_nr=false,$limit=10,$n=1,$cache=false) {
	global $config;
	$hash  = md5($query.$query_nr.$limit.$n);
	$file	= ROOT_DIR.'cache/'.$hash.'.php';
	//используем кеш
	if (@$config['cache'] && $cache && file_exists($file) && (time()-$cache)<filemtime($file)) {
		$config['queries'][] = array($hash,$query);
		$result = file_get_contents ($file);
		return json_decode($result,true);
	}
	//запросы к БД
	else {
		$data = array(
			'list'=>array(),
			'limit'=>$limit,
			'n'=>$n,
			'num_rows'=>0
		);
		if (mysql_connect_db() !== false) {
			if ($query_nr===false) {
				$query_nr = str_replace(PHP_EOL, ' ', $query);
				$query_nr = str_replace("\r", ' ', $query_nr);
				$query_nr = str_replace("\n", ' ', $query_nr);
				$query_nr = str_replace('	',' ',$query_nr);
				$query_nr = str_replace('SELECT','SELECT ',$query_nr);
				$query_nr = str_replace('FROM',' FROM',$query_nr);
				$query_nr = preg_replace('/SELECT .* FROM/', 'SELECT COUNT(*) FROM', $query_nr);
				$query_nr = explode('ORDER',$query_nr);
				$query_nr = $query_nr[0];
			}

			//v1.4.72 чтобы количество можно было передавать параметром
			if (is_int($query_nr)) {
				$data['num_rows'] = $query_nr;
			}
			//количество всего
			else {
				$data['num_rows'] = mysql_select($query_nr,'string');
			}

			if ($limit>0) {
				$n = abs(intval($n));
				if ($n==0) $n=1;
				$data['n'] = $n;
				$offset = $n * $limit - $limit;
				$query.= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
			}
			//массив данных
			$data['list'] = mysql_select($query, 'rows');
			//кеширование
			if (@$config['cache'] && $cache) {
				if (is_dir(ROOT_DIR.'cache') || mkdir(ROOT_DIR.'cache',0755,true)) {
					$f = fopen($file,'w');
					fwrite($f,json_encode($data));
					fclose($f);
				}
			}
		}
		return $data;
	}
}

/**
 * запросы к БД кроме селект
 * @param string $type - тип запроса [inser,update,delete]
 * @param string $tbl_name - название таблицы или весь запрос если $type=='query'
 * @param array $post - массив данных
 * @param string $where -
 * @return boolean|int|string - да/нет | ID инсера | запрос удаления
 * @version v1.4.20
 * v1.1.10 обработка NULL
 * v1.1.13 - временные метки для лога скл запросов
 * v1.1.26 - исправление ошибки при удалении (нет массива $post)
 * v1.2.52 - переделка на mysqli
 * v1.2.82 - $config['mysql_null']
 * v1.2.109 - info return mysqli_info();
 * v1.3.37 - created_at
 * v1.4.20 - created_at пофиксил ошибку
 */
function mysql_fn($type, $tbl_name, $post=array() ,$where = '', $ignore = false) {
	global $config;
	if (($connect = mysql_connect_db()) !== false) {
		$query = '';
		//если четвертый параметр - массив
		if (!is_string($where)) {
			$exceptions = $where;
			$where = '';
		}
		else $exceptions = false;

		//1.3.37 - created_at
		if ($type=='insert' OR $type=='insert values') {
			if (!isset($config['_created_at'][$tbl_name])) {
				$config['_created_at'][$tbl_name] = mysql_select("SHOW COLUMNS FROM ".$tbl_name." LIKE 'created_at'",'rows');
			}
			//если есть такое поле то добавляем дату создания
			if ($config['_created_at'][$tbl_name]) {
				$post['created_at'] = $config['datetime'];
			}
		}

		//тело запроса INSERT множества записей
		if ($type == 'insert values') {
			$into = implode('`,`', array_keys(current($post)));
			foreach ($post as $q) {
				$values = array();
				foreach ($q as $v) $values[] = "'" . mysql_res($v) . "'";
				$sql[] = implode(',', $values);
			}
			$sql = implode('),(', $sql);
		}
		//тело запроса INSERT одиночной записи или UPDATE
		else {
			//v1.1.26 - исправление ошибки при удалении (нет массива $post)
			if (is_array($post)) {
				foreach ($post as $k => $v) {
					//если есть исключения
					if ($exceptions == false OR !in_array($k, $exceptions)) {
						//v1.1.10 обработка NULL
						//v1.2.82 - добавил $config['mysql_null'] для настройки
						if ($v === NULL AND @$config['mysql_null']==true) $sql[] = "`" . $k . "` = NULL";
						else $sql[] = "`" . $k . "` = '" . mysql_res($v) . "'";
					}
				}
				$sql = isset($sql) ? implode(', ', $sql) : '';
			}
		}


		$ignore = $ignore ? "IGNORE" : "";
		switch ($type) {
			//запрос на вставку новой строки
			case 'insert':
				$query = "
					INSERT " . $ignore . " INTO `" . $tbl_name . "`
					SET " . $sql . ";
				";
				break;
			//запрос на вставку новой строки с обновлением при совпадении unique ключа
			case 'insert update':
				$query = "
					INSERT " . $ignore . " INTO `" . $tbl_name . "`
					SET " . $sql . "
					ON DUPLICATE KEY UPDATE " . $sql . ";
				";
				break;
			//запрос на вставку множества строк
			case 'insert values':
				$query = "
					INSERT " . $ignore . " INTO `" . $tbl_name . "` (`" . $into . "`)
					VALUES (" . $sql . ")
				";
				break;
			//запрос на обновление одной или нескольких строк
			case 'update':
				if ($id = intval(@$post['id'])) $where .= " AND id = '" . $id . "' ";
				$query = "
					UPDATE `" . $tbl_name . "`
					SET " . $sql . "
					WHERE 1	" . $where;
				//нельзя делать обновление без условий
				if ($where=='') {
					trigger_error('error_update ' . $query, E_USER_DEPRECATED);
					$query = '';
				}
				break;
			//запрос на удаление одной или нескольких строк
			case 'delete':
				if (is_array($post)) $id = intval(@$post['id']);
				else $id = intval($post);
				if ($id) $where .= " AND id = '" . $id . "' ";
				$query = "
					DELETE
					FROM `" . $tbl_name . "`
					WHERE 1	" . $where;
				//нельзя удалять без условий
				if ($where=='') {
					trigger_error('error_delete ' . $query, E_USER_DEPRECATED);
					$query = '';
				}
				break;
			//любой запрос
			case 'query':
				$query = $tbl_name;
				break;
			//по умолчанию возвращаем тело запроса
			default:
				return $sql;
		}
		//если запрос есть
		if ($query) {
			//выполняем запрос

			//log_add('sql.txt',$query);
			$time = microtime(true);
			mysqli_query($connect,$query); //echo $query;
			$time = microtime(true)-$time;
			$config['queries'][] = array($time,$query);

			if (($error = mysqli_error($connect)) == false) {
				switch ($type) {
					case 'insert':
					case 'insert update':
						return (mysqli_affected_rows($connect) > 0) ? mysqli_insert_id($connect) : false;
					case 'update':
					case 'delete':
					case 'insert values':
						return (($rows = mysqli_affected_rows($connect)) > 0) ? $rows : false;
					case 'query':
						if ($post=='affected_rows') return mysqli_affected_rows($connect);
						//v1.2.109
						if ($post=='info') return mysqli_info($connect);
				}
				return false;
			} else {
				trigger_error($error . ' ' . $query, E_USER_DEPRECATED);
			}
		}
	}
}

/**
 * старт трансакции
 * @param $action - старт, откат или завершение
 * @return bool - только в случае старта
 * @version v1.2.103
 * v1.2.103 - добавлена - InnoDB и трансакции
 */
function mysql_transaction($action) {
	global $config;
	//стартуем
	if ($action=='start') {
		if (@$config['mysql_transaction']>0) {
			log_add('transactions.txt','transaction already started');
			return false;
		}
		else {
			$config['mysql_transaction'] = 1;
			//mysql_fn('query', 'SET AUTOCOMMIT=0'); // этот параметр не обязателен, как я понял старт транзакции его подразумевает. Оставил для примера на всякий случай
			mysql_fn('query', 'START TRANSACTION');
			return true;
		}
	}
	//откатываем
	elseif ($action=='rollback') {
		mysql_fn('query', 'ROLLBACK');
	}
	//завершаем
	elseif ($action=='commit') {
		mysql_fn('query', 'COMMIT');
	}
	//если передали неверное значение
	else {
		log_add('transactions.txt','error action: '.$action);
		return false;
	}
}

/**
 * функция для генерации условий запроса
 * @param $type -  find_in_set
 * @param $field - поле для поиска
 * @param $value - значения для поиска
 * @return string - условие запроса
 * @version v1.3.23
 * v1.3.23 - добавлена
 */
function mysql_where ($type,$field,$value) {
	if ($type='find_in_set') {
		if ($value) {
			$where = array();
			$array = explode(',', $value);
			foreach ($array as $k => $v) {
				$where[] = " FIND_IN_SET (" . $v . ",".$field.")";
			}
			return " AND (" . implode(' OR ', $where) . ")";
		}
	}
}

//выборка с БД - вариант Саши
/*
function mysql_array($query, $type = 'rows',$cache = 0) {
	global $config;
	$file = ROOT_DIR.'cache/'.md5($query).'.php';
	if ($config['cache'] && $cache && file_exists($file) && (time() - $cache) < filemtime($file)) {
		$ret = file_get_contents($file);
		return json_decode($ret,true);

	} else {
		if (!defined('config_db')) require_once(ROOT_DIR.'config_db.php');
		$result = mysqli_query($query);
		if ($error = mysqli_error()) {
			trigger_error($error.' '.$query, E_USER_DEPRECATED);
			return false;
		}
		if ($result) {
			switch ($type) {
				//одно значение
				case 'value':
				case 'result':		if (mysqli_num_rows($result)) $r = mysqli_result($result,0); break;
				//массив одиночных значений
				case 'values':
				case 'results':		while ($q = mysqli_fetch_row($result)) $r[] = $q[0]; break;
				//количество строк
				case 'num_rows':	$r = mysqli_num_rows($result); break;
				//одна строка
				case 'row':			$r = mysqli_fetch_assoc($result); break;
				//все строки
				case 'rows':		while ($q = mysqli_fetch_assoc($result)) $r[] = $q; break;
				//все строки в массиве с ключами-id
				case 'id':			while ($q = mysqli_fetch_assoc($result)) $r[$q['id']] = $q; break;
				//все значения поля name в массиве с ключами-id
				case 'names':		while ($q = mysqli_fetch_assoc($result)) $r[$q['id']] = $q['name']; break;
				//пары ключ-значение
				case 'key_val':		while ($q = mysqli_fetch_row($result)) $r[$q[0]] = $q[1]; break;
				//все строки в массиве с ключами из первого поля
				case 'first':		while ($q = mysqli_fetch_assoc($result)) $r[current($q)] = $q; break;
				//все строки в массиве с ключами из первого поля
				case 'first_second':while ($q = mysqli_fetch_assoc($result)) $r[current($q)][next($q)] = $q; break;
				//массивы из значений второго поля, сгруппированных по первому полю
				case 'arrays':		while ($q = mysqli_fetch_row($result)) $r[$q[0]][] = $q[1]; break;
				//массивы из значений второго поля, сгруппированных по первому полю с ключами-значениями
				case 'arrays_num':	while ($q = mysqli_fetch_row($result)) $r[$q[0]][$q[1]] = $q[1]; break;
				//массивы строк, сгруппированных по какому-либо полю
				default:	 		while ($q = mysqli_fetch_assoc($result)) $r[$q[$type]][] = $q;
			}

		} else $r = false;
		if ($config['cache'] && $cache) {
			if (is_dir(ROOT_DIR.'cache') || mkdir(ROOT_DIR.'cache',0755,true)) {
				$f = fopen($file,'w');
				fwrite($f,json_encode(@$r));
				fclose($f);
			}
		}
		return @$r;
	}
}
*/