<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>abc-cms.com</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href="/admin/templates/css/reset.css" rel="stylesheet" type="text/css" />
	<link href="/admin/templates/css/style.css?0" rel="stylesheet" type="text/css" />
	<meta name="robots" content="noindex, nofollow">
	<?= html_sources('return', 'jquery.js') ?>
	<style type="text/css">
		#auth {height:100%; width:348px; margin:auto}
		#auth .form {width:348px}
		#auth .form_head {padding-left:30px; background: #4C4C4C}
		#auth .form_content {padding-top:10px; background:#E8E8E8;}
		#auth .button {float:right; margin:15px 23px 0 0}
		#auth .remember {float:left; padding:20px 0 0;}
		#auth .copyright {margin-top:20px; padding:10px 0px 40px; color:#666; font:11px Arial; border-top:0px solid #333}
		#auth .copyright div {float:right}
		#auth .copyright a {color:#666;}
		#auth .message {padding:0 0 20px 0px}
	</style>
</head>
<body class="b-size">
<table id="auth"><tr><td>
			<?=(isset($message) AND $message) ? '<div class="message"><b>'.$message.'</b></div>' : ''?>
			<form class="form" method="post" action="/admin.php?m=<?=$get['m']?>">
				<div class="form_head corner_top">АВТОРИЗАЦИЯ</div>
				<div class="form_content corner_bottom">
					<?=form('input td4','login','',array('name'=>'Логин:'))?>
					<?=form('input td4','password','',array('name'=>'Пароль:','attr'=>'type="password"'))?>
					<input name="captcha" type="hidden" value="<?=time()?>" />
					<script type="text/javascript">$(document).ready(function(){$.get('/api/captcha/',function(data){if(data)$('input[name="captcha"]').val(data)})})</script>
					<div class="clear"></div>
					<div class="remember"><?=form('checkbox line','remember_me','',array('name'=>'запомнить меня'))?></div>
					<div class="button red"><input type="submit" value="ВОЙТИ" /></div>
					<div class="clear"></div>
				</div>
			</form>
			<div class="copyright">
				<div><?=date('Y')?> &copy; abc-cms.com</div>
				<a href="/" target="_blank" title="перейти на сайт">перейти на сайт</a>
			</div>
		</td></tr></table>
</body>
</html>