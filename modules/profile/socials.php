<?php

$post = array();

//удаление связи
if (@$_GET['delete']) {
	$id = intval($_GET['delete']);
	if ($id AND $social=mysql_select("SELECT * FROM user_socials WHERE id=".$id." AND user=".$user['id'],'row')) {
		mysql_fn('delete','user_socials',$social['id']);
	}
}

//привязка аккаунта
if (isset($_GET['type']) && isset($_GET['code'])) {
	$data = file_get_contents('http://auth.abc-cms.com/' . $_GET['type'] . '/?go=1&code=' . $_GET['code']);
	if ($data) {
		$data = json_decode($data, true);
		if (is_array($data)) {
			$social_type = array_search($_GET['type'], $config['user_socials']['types']);
			if ($social_type) {
				$social = mysql_select("
					SELECT * 
					FROM user_socials 
					WHERE uid='" . mysql_res($data['uid']) . "' AND type='" . intval($social_type) . "'
				", 'row');
				//пользователь есть в базе - показываем ошибку
				if ($social) {
					$post['message'] = i18n('socials|uid_error');
				}
				//добавляем привязку
				else {
					$data['user'] = $user['id'];
					$data['type'] = $social_type;
					$data['date'] = $config['datetime'];
					$data['last_visit'] = $config['datetime'];
					mysql_fn('insert', 'user_socials', $data);
				}
			}
		}
	}
}

//вывод нтмл
$abc['user_socials'] = mysql_select("SELECT * FROM user_socials WHERE user=".$user['id'],'rows');

$abc['content'] = html_array('profile/socials_edit',$post);
