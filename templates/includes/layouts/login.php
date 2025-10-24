<?php
//если авторизирован
if (access('user auth')) {
	echo html_render('form/messages',array(i18n('profile|successful_auth',true)));
	echo '<a href="'.get_url('profile').'" title="'.i18n('profile|go_to_profile').'">'.i18n('profile|go_to_profile').'</a>';
}
//если не авторизирован
else {
	?>
	<?=$abc['page']['text']?>
	<?=html_render('profile/auth',$abc['post'])?>
	<?php
}