<!doctype html>
<html lang="<?=$config['admin_lang']?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0,user-scalable=0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Site control panel</title>
	<link rel="shortcut icon" href="/<?=$config['style']?>/assets/media/image/favicon.png"/>
	<?=html_sources('return','admin_top')?>
</head>
<body class="rtl <?=@$_SESSION['sidebar'] == 'close' ? 'small-navigation':''?>">

<!-- begin::navigation -->
<div class="navigation">

	<!-- begin::logo -->
	<div id="logo">
		<a href="/admin.php">
			<img class="logo" style="width:143px;" src="/<?=$config['style']?>/assets/media/image/logo3.png" alt="logo">
			<img class="logo-sm"  style="width:50px" src="/<?=$config['style']?>/assets/media/image/logo2.png" alt="small logo">
		</a>
	</div>
	<!-- end::logo -->

	<!-- begin::navigation menu -->
	<div class="navigation-menu-body">
		<ul>
			<?=html_array('layouts/menu',$modules_admin)?>
			<li style="border-top: 1px solid rgba(102, 153, 204, 0.14);margin-top:15px;padding-top:15px">
				<a href="/" target="_blank" title="<?= a18n('go_to_site') ?>">
					<i class="nav-link-icon" data-feather="external-link"></i>
					<span><?= a18n('go_to_site') ?></span>
				</a>
			</li>
		</ul>
	</div>
	<!-- end::navigation menu -->

</div>
<!-- end::navigation -->

<!-- begin::main -->
<div id="main">

	<!-- begin::header -->
	<div class="header">

		<!-- begin::header left -->
		<ul class="navbar-nav">

			<!-- begin::navigation-toggler -->
			<li class="nav-item navigation-toggler">
				<a href="#" class="nav-link">
					<i data-feather="menu"></i>
					<i data-feather="arrow-left"></i>
					<i data-feather="arrow-right"></i>
				</a>
			</li>
			<!-- end::navigation-toggler -->

			<!-- begin::header-logo -->
			<li class="nav-item" id="header-logo">
				<a href="/admin.php">
					<img class="logo" style="width: 50px" src="/<?=$config['style']?>/assets/media/image/logo2.png" alt="logo">
					<img class="logo-sm" src="/<?=$config['style']?>/assets/media/image/logo2.png" alt="small logo">
				</a>
			</li>
			<!-- end::header-logo -->
		</ul>
		<!-- end::header left -->

		<!-- begin::header-right -->
		<div class="header-right">
			<ul class="navbar-nav">


				<!-- begin::search-form -->
				<li class="nav-item search-form"></li>
				<!-- end::search-form -->


				<?=html_array('layouts/feedback_header')?>

				<?=html_array('layouts/notifications')?>

				<li class="nav-item dropdown">
					<?=$user['email']?>
				</li>

				<li class="nav-item dropdown">
					<a href="/admin.php?m=login&u=exit" class="btn nav-link bg-danger-bright" title="Logout" data-toggle="tooltip">
						<i data-feather="log-out"></i>
					</a>
				</li>
			</ul>

			<!-- begin::mobile header toggler -->
			<ul class="navbar-nav d-flex align-items-center">
				<li class="nav-item header-toggler">
					<a href="#" class="nav-link">
						<i data-feather="arrow-down"></i>
					</a>
				</li>
			</ul>
			<!-- end::mobile header toggler -->
		</div>
		<!-- end::header-right -->
	</div>
	<!-- end::header -->

	<!-- begin::main-content -->
	<div class="main-content">

		<!-- begin::container -->
		<div class="container">

			<?php /*
			<div class="row">
				<div class="page-header">
					<h4><?=@$page_name?></h4>
				</div>
			</div>
			 */ ?>

			<?=$content?>

			<?php if ($filter) {
				?>
				<div class="form-row" id="filter">
					<?php
					foreach ($filter as $k=>$v) {
						echo is_array($v) ? call_user_func_array('filter', $v) : $v;

					}
					?>
				</div>
				<?php
			}?>

			<div class="row">
				<?php
				// class="col-md-12"
				if (@$table) {
					?>
					<div class="card" style="width: 100%;">
						<div class="card-body">
							<div class="table-responsive">
								<?=table($table, $query)?>
							</div>
						</div>
					</div>
					<?php
					//v1.2.130 - чекбоксы для админки
					if (isset($table['_check'])) {
						?>
					<div style="width:100%">
						<form method="post" class="table_check form-row" action="">
							<input type="hidden" name="_check[ids]" />
							<?php
							//операции в виде кнопок
							if (isset($table['_check']['buttons']) AND is_array($table['_check']['buttons'])) {
								foreach ($table['_check']['buttons'] as $k=>$v) {
									?>
								<div class="form-group col-xl-2">
									<input class="btn btn-secondary" type="submit" name="_check[<?=$k?>]" value="<?=$v?>" />
								</div>
								<?php
								}
							}
							//операции в виде селекта и кнопки применить
							if (isset($table['_check']['select']) AND is_array($table['_check']['select'])) {
								?>
								<div class="form-group col-xl-3">
									<select class="form-control" name="_check[select]"><?=select('',$table['_check']['select'],'')?></select>
								</div>
								<div class="form-group col-xl-3">
									<input class="btn btn-secondary" style="float:left" type="submit" value="Применить" />
								</div>
								<?php
							}
							?>
						</form>
					</div>
						<?php
					}
				}

				if (@$content_bottom) {
					echo $content_bottom;
				}

				//загружаем внутрь таблицы толлко если одна форма
				if (/*$get['id'] AND */isset($form) AND $module['one_form']==true) {
					?>
					<div class="card" style="width: 100%">
						<div class="card-body">
							<form id="form<?=$get['id']?>" class="form" method="post" enctype="multipart/form-data" action="<?=setUrlParams($_SERVER['REQUEST_URI'],array('u'=>'edit','id'=>false))?>">

								<?php
								require_once(ROOT_DIR.$config['style'].'/includes/layouts/form_body.php');
								?>
								<div class="modal-footer" style="padding: 15px 0 0">
									<?php
									require_once(ROOT_DIR.$config['style'].'/includes/layouts/form_footer.php');
									?>
								</div>
							</form>
						</div>
					</div>
					<?php
				}
				?>
			</div>

		</div>
		<!-- end::container -->

	</div>
	<!-- end::main-content -->

	<!-- begin::footer -->
	<footer style="padding-top: 0">
		<div style="margin-right: 30px; text-align: right">
			<?=date('Y')?> &copy; abc-cms.com
			<a href="/_documentation" target="_blank">v.<?=$config['cms_version']?></a>
		</div>
	</footer>
	<!-- end::footer -->

</div>
<!-- end::main -->

<!-- Plugin scripts -->

<?php /*
<!-- Chartjs -->
<script src="/<?=$config['style']?>/vendors/charts/chartjs/chart.min.js"></script>

<!-- Apex chart -->
<script src="/<?=$config['style']?>/vendors/charts/apex/apexcharts.min.js"></script>

<!-- Circle progress -->
<script src="/<?=$config['style']?>/vendors/circle-progress/circle-progress.min.js"></script>

<!-- Peity -->
<script src="/<?=$config['style']?>/vendors/charts/peity/jquery.peity.min.js"></script>
<script src="/<?=$config['style']?>/assets/js/examples/charts/peity.js"></script>

<!-- Datepicker -->
<script src="/<?=$config['style']?>/vendors/datepicker/daterangepicker.js"></script>

<!-- Slick -->
<script src="/<?=$config['style']?>/vendors/slick/slick.min.js"></script>

<!-- Vamp -->
<script src="/<?=$config['style']?>/vendors/vmap/jquery.vmap.min.js"></script>
<script src="/<?=$config['style']?>/vendors/vmap/maps/jquery.vmap.usa.js"></script>
<script src="/<?=$config['style']?>/assets/js/examples/vmap.js"></script>

<!-- Dashboard scripts -->
<script src="/<?=$config['style']?>/assets/js/examples/dashboard.js"></script>
 */?>

<script src="/<?=$config['style']?>/vendors/bundle.js"></script>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		$(document).on('click', '.navigation-toggler > a', function () {
			if ($(window).width() >= 1200) {
				if ($('body').hasClass('small-navigation')) {
					$.get('/admin.php', {'u': 'sidebar', 'action': 'close'});
				}
				else {
					$.get('/admin.php', {'u': 'sidebar', 'action': 'open'});
				}
			}
			else {

			}
		});
		//чтобы не делать два раза клик на телефоне по меню
		if ($(window).width() < 1200) {
			$('body.small-navigation').removeClass('small-navigation');
		}
	});
</script>

<?=html_sources('return','admin_bottom')?>
<?=html_sources('footer')?>


<div class="colors"> <!-- To use theme colors with Javascript -->
	<div class="bg-primary"></div>
	<div class="bg-primary-bright"></div>
	<div class="bg-secondary"></div>
	<div class="bg-secondary-bright"></div>
	<div class="bg-info"></div>
	<div class="bg-info-bright"></div>
	<div class="bg-success"></div>
	<div class="bg-success-bright"></div>
	<div class="bg-danger"></div>
	<div class="bg-danger-bright"></div>
	<div class="bg-warning"></div>
	<div class="bg-warning-bright"></div>
</div>


<?php
//v1.4.52 открытие окна админки по ссылке
if (@$get['id'] AND isset($form) AND @$module['one_form']==false) {
	require_once(ROOT_DIR . $config['style'].'/includes/layouts/form.php');
	?>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function () {
			$('#window').modal();
		});
	</script>
	<?php
}
?>

</body>
</html>