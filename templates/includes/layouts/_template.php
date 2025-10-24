<?php //dd($abc['page'],true);
$abc['page']['title']			= isset($abc['page']['title']) ? filter_var($abc['page']['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : filter_var($abc['page']['name'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$abc['page']['description']	= isset($abc['page']['description']) ? filter_var($abc['page']['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : $abc['page']['title'];
?><!doctype html>
<html lang="<?=$lang['localization']?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0,user-scalable=0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title><?=$abc['page']['title']?></title>
	<meta name="description" content="<?=$abc['page']['description']?>"><?php
//v1.1.15 - запрет индексации на тестовом
//v1.2.37 - условие $abc['page']['noindex']
if(@$abc['page']['noindex']==1
	OR strripos($_SERVER['HTTP_HOST'], '.abc-cms.com')!==false
	OR strripos($_SERVER['HTTP_HOST'], '.tyt.kz')!==false) {
	?>
	<meta name="robots" content="noindex, nofollow"><?php
}
//v.1.2.31 - open graph
if (@$config['open_graph']) {
	$abc['og']['type'] = @$abc['og']['type'] ? $abc['og']['type'] : 'website';
	$abc['og']['title'] = $abc['page']['title'];
	$abc['og']['description'] = $abc['page']['description'];
	$abc['og']['url'] = @$abc['page']['canonical'] ? $abc['page']['canonical'] : $_SERVER['REQUEST_URI'];
	foreach ($abc['og'] as $k=>$v) {
		//добавляем к значениям урл полный путь с протоколом и доменом
		if (in_array($k,array('url','image'))) $v = $config['http_domain'].$v;
		echo '<meta property="og:'.$k.'" content="'.htmlspecialchars($v).'">';
	}
}
//v1.2.31 - canonical,next,prev
if (@$abc['page']['canonical'] AND $abc['page']['canonical']==$_SERVER['REQUEST_URI']) unset($abc['page']['canonical']);
foreach (array('canonical','next','prev') as $k=>$v) {
	if (@$abc['page'][$v]) echo '<link rel="'.$v.'" href="'.$config['http_domain'].$abc['page'][$v].'">';
}
//v1.2.13 - amp страницы
if (@$abc['page']['amp']) echo '<link rel="amphtml" href="'.$config['http_domain'].$abc['page']['amp'].'">';
?>
	<?=html_sources('return','bundle.css')?>
	<?=i18n('common|script_head')?>
	<?=access('editable scripts') ? html_sources('footer','tinymce.js editable.js') : ''?>
	<?=html_sources('head')?>
</head>

<body>
<?=i18n('common|script_body_start')?>
<div id="body">
	<div class="container" id="header">
		<div class="row pb">
			<div class="col-lg-3 col-xs-12 pt">
				<?php if ($abc['layout']=='index') {?>
				<span><img src="/<?=$config['style']?>/images/logo.jpg" alt="<?=i18n('common|site_name')?>" /></span>
				<?php } else {?>
				<a href="<?=get_url('index')?>" title="<?=i18n('common|site_name')?>"><img src="/<?=$config['style']?>/images/logo.jpg" alt="<?=i18n('common|site_name')?>" /></a>
				<?php } ?>
			</div>
			<div class="col-lg-6 col-xs-12 pt">
				<?=i18n('common|txt_head',true)?>
				<?=html_render('profile/login_form')?>
			</div>
			<div class="col-sm-3 col-xs-12 pt">
				<?=html_render('order/basket_info')?>
				<?=@$abc['languages']?html_render('menu/languages',$abc['languages']):''?>
			</div>
		</div>
	</div>

	<div id="wrapper">
		<div class="container">
			<?=html_render('menu/list',$abc['menu'])?>

			<div class="row pt">

				<?=@$abc['breadcrumb']?html_render('common/breadcrumb',$abc['breadcrumb']):''?>

				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
					<?=html_query('menu/category',$abc['menu_categories'],'')?>
					<?=html_query('shop/product_random',$abc['product_random'],''); ?>
				</div>

				<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
					<?php
					if (in_array($abc['layout'],array('index','shop_product'))) {
						echo html_render('layouts/'.$abc['layout']);
					}
					else {
						?>
						<h1><?=@$abc['page']['h1']?$abc['page']['h1']:$abc['page']['name']?></h1>
						<div class="content">
							<?=html_render('layouts/'.$abc['layout'])?>
						</div>
						<?php
					}
					?>
				</div>

			</div>

		</div>
	</div>
</div>
<div id="footer">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-4 col-xs-12 pb">
				<?=i18n('common|info',true)?>
				<?=html_query('menu/list2',$abc['menu_footer'],'')?>
			</div>
			<div class="col-md-3 col-sm-4 col-xs-12 pb">
				<h4><?=i18n('profile|link',true)?></h4>
				<ul>
					<li><a href="<?=get_url('profile','user_edit')?>"><?=i18n('profile|user_edit')?></a></li>
					<li><a href="<?=get_url('profile','orders')?>"><?=i18n('basket|orders')?></a></li>
				</ul>
			</div>
			<div class="col-md-3 col-sm-4 col-xs-12 pb">
				<?=html_array('common/socials',i18n('common|social'))?>
			</div>
			<div class="col-md-3 col-sm-12 col-xs-12 pb">
				<?=i18n('common|txt_footer',array('Y'=>date('Y')))?>
			</div>
		</div>
	</div>
</div>
<?=html_sources('return','bundle.js')?>
<?=html_sources('footer')?>
<?=i18n('common|script_body_end')?>
</body>
</html>