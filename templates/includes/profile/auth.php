<?=html_sources('footer','jquery_validate.js')?>
<form method="post" class="validate ajax" data-action="/api/login/?action=enter&language=<?=$abc['lang']['id']?>" action="<?=get_url('login','enter')?>" >
	<div class="message_box">
		<?=@$q['message'] ? html_render('form/messages',$q['message']) : ''?>
	</div>
<?php
echo html_array('form/input',array(
	'caption'	=>	i18n('profile|email',true),
	'name'	=>	'login',
	'data'	=>	isset($q['login']) ? $q['login'] : '',
	//'attr'	=>	' required', //email
	'attr'	=>	' required type="email"', //email
));
echo html_array('form/input',array(
	'caption'	=>	i18n('profile|password',true),
	'name'	=>	'password',
	'attr'	=>	' required minlength="4" type="password" autocomplete="off"',
	'data'	=>	isset($q['password']) ? $q['password'] : '',
));
echo html_array('form/checkbox',array(
	'units'	=>	i18n('profile|remember_me',true),
	'name'	=>	'remember_me',
	//'attr'	=>	'required'
));
echo html_array('form/captcha2');
echo html_array('form/button',array(
	'name'	=>	i18n('profile|enter'),
));
?>
<?php if (isset($modules['remind'])) {?>
	&nbsp; <a href="<?=get_url('remind')?>"><?=i18n('profile|remind')?></a>
<?php } ?>
</form>
