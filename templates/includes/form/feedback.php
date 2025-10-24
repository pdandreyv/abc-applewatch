<?php
/**
 * форма обратной связи
 * v1.2.22 - добавил отправку аджаксом
 */
//если письмо отправилось
if (isset($q['success'])) {
	echo html_render('form/messages',array(i18n('feedback|message_is_sent',true)));
}
else {
	?>
<?=html_sources('footer','jquery_validate.js')?>
<noscript><?=i18n('validate|not_valid_captcha2')?></noscript>
<form id="feedback" method="post" class="form validate ajax" enctype="multipart/form-data" data-window_success="#feedback_success" data-action="/api/feedback/">
	<div class="message_box">
		<?=isset($q['message']) ? html_render('form/messages',$q['message']) : ''?>
	</div>
	<input type="hidden" name="language" value="<?=$lang['id']?>"/>
<?php
echo html_array('form/input',array(
	'caption'	=>	i18n('feedback|email',true),
	'name'		=>	'email',
	'value'		=>	isset($q['email']) ? $q['email'] : '',
	'attr'		=>	' required type="email"',
));
echo html_array('form/input',array(
	'caption'	=>	i18n('feedback|name',true),
	'name'		=>	'name',
	'value'		=>	isset($q['name']) ? $q['name'] : '',
	'attr'		=>	' required',
));
echo html_array('form/textarea',array(
	'name'		=>	'text',
	'caption'	=>	i18n('feedback|text',true),
	'value'		=>	isset($q['text']) ? $q['text'] : '',
	'attr'		=>	' required',
));
echo html_array('form/file',array(
	'caption'	=>	i18n('feedback|attach',true),
	'name'		=>	'attaches[]',
	'attr'      =>  'multiple="multiple"',
));
echo html_array('form/captcha2');//скрытая капча
echo html_array('form/button',array(
	'name'	=>	i18n('feedback|send'),
));
?>
	<input type="hidden" name="page_name" value="<?=$abc['page']['name']?>">
	<input type="hidden" name="page_url" value="<?=$_SERVER['REQUEST_URI']?>">
</form>
<div class="modal fade" id="feedback_success" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<?=i18n('feedback|message_is_sent')?>
			</div>
		</div>
	</div>
</div>
<?php } ?>