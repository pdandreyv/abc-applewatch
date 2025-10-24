<?=html_sources('footer','jquery_validate.js')?>
<?=isset($q['message']) ? html_render('form/messages',$q['message']) : ''?>
<form method="post" class="form validate" enctype="multipart/form-data">
<?php
echo html_array('form/input',array(
	'caption'	=>	i18n('profile|email',true),
	'name'	=>	'email',
	'value'	=>	@$q['email'],
	'attr'	=>	' required type="email"',
));
echo html_array('profile/fields',isset($q['fields']) ? $q['fields'] : array());
echo html_array('form/button',array(
'name'	=>	i18n('profile|save')
));
?>
</form>
