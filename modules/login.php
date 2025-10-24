<?php
$message = array();
//если авторизирован
if (access('user auth')) {
	//если нажал выйти
	if ($u[2]=='exit') {
		$user = user();
		$message[] = i18n('profile|msg_exit',true);
	}
}
//если не авторизирован
else {
	//если нажал вход
	if ($u[2]=='enter') {
		$message[] = ($user = user('enter')) ? i18n('profile|successful_auth',true) : i18n('profile|error_auth',true);
	}
	//v1.2.66 - вход через социальную сеть
	if ($u[2]=='social') {
		$message[] = ($user = user('social')) ? i18n('profile|successful_auth',true) : i18n('profile|error_auth_social',true);
	}
}
$abc['post']['message'] = $message;