<?=html_sources('footer','jquery_validate.js')?>
<?=isset($q['message']) ? html_render('form/messages',$q['message']) : ''?>
<form method="post" class="form validate" enctype="multipart/form-data">
	<?php
	//v1.4.54 - убрал проверку старого пароля
	/*
	echo html_array('form/input',array(
		'caption'	=>	i18n('profile|old_password',true),
		'name'	=>	'password',
		'attr'	=>	'required" type="password" minlength="4',
	));
	*/
	echo html_array('form/input',array(
		'caption'	=>	i18n('profile|new_password',true),
		'name'	=>	'password_new',
		'attr'	=>	'required type="password" minlength="6"',
	));
	echo html_array('form/button',array(
		'name'	=>	i18n('profile|save')
	));
	?>
</form>
