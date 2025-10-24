<?=html_sources('footer','jquery_validate.js')?>
<form method="post" class="form validate">
	<?=isset($q['message']) ? html_render('form/messages',$q['message']) : ''?>
<?php
	echo html_array('form/input',array(
		'name'		=>	'email',
		'value'		=>	isset($q['email']) ? $q['email'] : '',
		'attr'		=>	' required type="email"',
	));
	echo html_array('form/captcha2');//скрытая капча
	echo html_array('form/button',array(
		'name'=>i18n('profile|remind_button'),
	));
?>
</form>
