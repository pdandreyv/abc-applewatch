<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>Документация abc-cms</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<style>
		html {position:relative; min-height:100%;}
		body {margin-bottom:65px;}
		#footer {position:absolute; bottom:0; width:100%; height:50px; background:#222; color:#fff; padding:15px 0 0; text-align:right}
		div.panel-collapse.bg-info {padding:5px 10px; margin:3px 0}
		#structure .panel-collapse {padding:0 0 0 15px}
		pre {overflow:hidden}
		#header {background:#222; color:#fff; padding:15px;}
		#menu {margin:10px 0 0;}
		#menu div {font-weight:bold; padding:10px 0 3px}
		#menu li a {padding:3px 15px}
		#content {padding-top:0px}
		.bs-callout {margin: 20px 0; padding: 20px; border-left: 3px solid #eee;}
		.bs-callout-danger{background-color:#fdf7f7;border-color:#d9534f}
		.bs-callout-danger h4{color:#d9534f}
		.bs-callout-warning{background-color:#fcf8f2;border-color:#f0ad4e}
		.bs-callout-warning h4{color:#f0ad4e}
		.bs-callout-info{background-color:#f4f8fa;border-color:#5bc0de}
		.bs-callout-info h4{color:#5bc0de}

	</style>
</head>

<body>

<div id="header">
	<div class="container">
		<div class="row">
			<div class="col-sm-2 container">abc-cms</div>
			<div class="col-sm-10">
				<div class="pull-right"><?=@$config['cms_version']?'v'.$config['cms_version']:''?></div>
				Полная техническая документация
			</div>
		</div>
	</div>
</div>

<div class="container">

	<div class="row">

		<div class="col-sm-2 container" >
			<div id="menu">
				<?php
				foreach ($pages as $key=>$val) {
					?>
				<div><?=$key?></div>
				<ul class="row nav nav-pills nav-stacked">
					<?php foreach ($val as $k=>$v) {?>
					<li <?=$k==$page?'class="active"':''?>><a href="?page=<?=$k?>"><?=$v?></a></li>
					<?php } ?>
				</ul>
					<?php
				}
				?>
			</div>
		</div>

		<div class="col-sm-10" id="content">
			<h3><?=$name?></h3>
			<?php include('pages/'.$page.'.php')?></div>
		</div>

	</div>



</div>


<div id="footer">
	<div class="container">
		<?=date('Y')?> © abc-cms.com
	</div>
</div>

</body>

</html>