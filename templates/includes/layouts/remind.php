<?php
//если письмо отправилось
if (isset($abc['post']['success'])) {
	echo html_render('form/messages',array(i18n('profile|successful_remind')));
}
else {
	?>
	<?=$abc['page']['text']?>
	<?=html_array('profile/remind',$abc['post'])?>
	<?php
}