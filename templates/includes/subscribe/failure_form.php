<?php
if ($q['display']==0) {	?>
<?=i18n('subscribe|failure_success',true)?>
	<?php
}
else {	?>
<?=i18n('subscribe|failure_text')?>
<form class="form" action="" method="post">
<?=html_array('form/button',array(
	'name'	=> i18n('subscribe|failure_button'),
));?>
<input name="action" type="hidden" value="failure" />
</form>
	<?php
}
?>