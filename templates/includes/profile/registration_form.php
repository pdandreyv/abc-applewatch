<?=html_sources('footer','jquery_validate.js')?>
<form id="registration" method="post" class="form validate ajax" data-action="/api/registration/?language=<?=$abc['lang']['id']?>" enctype="multipart/form-value">
<div class="message_box">
	<?=@$q['message'] ? html_render('form/messages',$q['message']) : ''?>
</div>
<?php
	echo html_array('form/input',array(
		'name'	=>	'email',
		'caption'	=>	i18n('profile|email',true),
		'value'	=>	isset($q['email']) ? $q['email'] : '',
		'attr'	=>	' required type="email"',
	));
	echo html_array('form/input',array(
		'name'	=>	'password',
		'caption'	=>	i18n('profile|password',true),
		'value'	=>	isset($q['password']) ? $q['password'] : '',
		'attr'	=>	' required id="password" type="password" autocomplete="off" minlength="6"',
	));
	echo html_array('form/input',array(
		'name'	=>	'password2',
		'caption'	=>	i18n('profile|password2',true),
		'value'	=>	isset($q['password2']) ? $q['password2'] : '',
		'attr'	=>	' required type="password" autocomplete="off"',
	));
	echo html_array('profile/fields',isset($q['fields']) ? $q['fields'] : array());
	echo html_array('form/captcha2');//скрытая капча
	echo html_array('form/button',array(
		'name'	=>	i18n('profile|registration'),
	));
	?>
</form>