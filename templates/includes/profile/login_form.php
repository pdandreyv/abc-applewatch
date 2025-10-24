<div id="login_form">
<?php if (access('user auth')) { ?>
	<?=i18n('profile|hello')?>, <?=$user['email']?>! &nbsp;
	<a href="<?=get_url('profile')?>"><?=i18n('profile|link')?></a> &nbsp;
	<a href="<?=get_url('login','exit')?>"><?=i18n('profile|exit')?></a>
	<?php if (access('user admin')) echo '<br /><a href="/admin.php">панель управления</a><br />'; ?>
<?php } else { ?>

	<a href="#" data-toggle="modal" data-target="#login_window" title="<?=i18n('profile|enter')?>"><?=i18n('profile|enter')?></a>
	| <a href="<?=get_url('registration')?>" title="<?=i18n('profile|registration')?>"><?=i18n('profile|registration')?></a>

	<div class="modal fade" id="login_window" >
		<div class="modal-dialog">
			<form class="modal-content validate" method="post" action="<?=get_url('login','enter')?>">
				<div class="modal-header">
					<?=i18n('profile|auth',true)?>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<input name="login" class="form-control" type="email" required value="" /><br />
					<input name="password" class="form-control required" type="password" value="" /><br />
					<?=html_array('form/captcha2')?>
					<a class="pull-right" href="<?=get_url('remind')?>" title="<?=i18n('profile|remind')?>"><?=i18n('profile|remind')?></a>
					<div class="checkbox">
						<label><input name="remember_me" type="checkbox" value="1" /> запомнить меня</label>
					</div>
					<?=html_array('profile/socials_login')/**/?>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-default pull-left" title="<?=i18n('profile|enter')?>"><?=i18n('profile|enter')?></button>
					<a class="btn btn-primary" href="<?=get_url('registration')?>"><?=i18n('profile|registration')?></a>
				</div>
			</form>
		</div>
	</div>

<?php } ?>
</div>
