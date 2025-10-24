<!doctype html>
<html lang="<?=$config['admin_lang']?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Site control panel</title>

	<!-- Plugin styles -->
	<link rel="stylesheet" href="/<?=$config['style']?>/vendors/bundle.css" type="text/css">

	<!-- App styles -->
	<link rel="stylesheet" href="/<?=$config['style']?>/assets/css/app.min.css" type="text/css">
</head>
<body class="form-membership">

<?php /*
<!-- begin::preloader-->
<div class="preloader">
	<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="50px" height="50px" viewBox="0 0 128 128"
		 xml:space="preserve">
        <rect x="0" y="0" width="100%" height="100%" fill="#FFFFFF"/>
		<g>
			<path d="M75.4 126.63a11.43 11.43 0 0 1-2.1-22.65 40.9 40.9 0 0 0 30.5-30.6 11.4 11.4 0 1 1 22.27 4.87h.02a63.77 63.77 0 0 1-47.8 48.05v-.02a11.38 11.38 0 0 1-2.93.37z"
				  fill="#000000" fill-opacity="1"/>
			<animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64"
							  dur="500ms" repeatCount="indefinite">
			</animateTransform>
		</g>
    </svg>
</div>
<!-- end::preloader -->
 */?>

<div class="form-wrapper">

	<!-- logo -->
	<div id="logo">
		<img class="logo" src="/<?=$config['style']?>/assets/media/image/logo3.png" alt="image">
		<img class="logo-dark" src="/<?=$config['style']?>/assets/media/image/logo3.png" alt="image">
	</div>
	<!-- ./ logo -->

	<h5>Авторизация</h5>

	<?=(isset($message) AND $message) ? '<div class="message"><b>'.$message.'</b></div>' : ''?>

	<!-- form -->
	<form method="post" action="/admin.php?m=<?=$get['m']?>">
		<div class="form-group">
			<input type="text" name="login" class="form-control" placeholder="Телефон или емейл" required autofocus>
		</div>
		<div class="form-group">
			<input type="password" name="password" class="form-control" placeholder="Пароль" required>
		</div>
		<div class="form-group d-flex justify-content-between">
			<div class="custom-control custom-checkbox">
				<input name="remember_me" type="checkbox" class="custom-control-input" checked="checked" value="1" id="customCheck1">
				<label class="custom-control-label" for="customCheck1">Запомнить меня</label>
			</div>
			<?php /*
			<a href="recover-password.html">Reset password</a>
 			*/?>
		</div>
		<input name="captcha" type="hidden" value="<?=time()?>" />
		<script type="text/javascript">
			document.addEventListener("DOMContentLoaded", function () {
				$.get('/api/captcha/',function(data){if(data)$('input[name="captcha"]').val(data)})
			})
		</script>
		<button class="btn btn-primary btn-block">Войти</button>

		<?php /*
		<hr>
		<p class="text-muted">Login with your social media account.</p>
		<ul class="list-inline">
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-facebook">
					<i class="fa fa-facebook"></i>
				</a>
			</li>
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-twitter">
					<i class="fa fa-twitter"></i>
				</a>
			</li>
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-dribbble">
					<i class="fa fa-dribbble"></i>
				</a>
			</li>
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-linkedin">
					<i class="fa fa-linkedin"></i>
				</a>
			</li>
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-google">
					<i class="fa fa-google"></i>
				</a>
			</li>
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-behance">
					<i class="fa fa-behance"></i>
				</a>
			</li>
			<li class="list-inline-item">
				<a href="#" class="btn btn-floating btn-instagram">
					<i class="fa fa-instagram"></i>
				</a>
			</li>
		</ul>
		<hr>
		<p class="text-muted">Don't have an account?</p>
		<a href="./register.html" class="btn btn-outline-light btn-sm">Register now!</a>
 */?>
	</form>
	<!-- ./ form -->

</div>

<!-- Plugin scripts -->
<script src="/<?=$config['style']?>/vendors/bundle.js"></script>

<!-- App scripts -->
<script src="/<?=$config['style']?>/assets/js/app.min.js"></script>
</body>
</html>