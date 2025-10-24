<?=html_render('shop/product_filter', $abc['post'])?>
<?php
//если есть товары
if ($abc['products']['list']) {
	?>
<?=html_render('pagination/data',$abc['products'])?>
<?=html_render('shop/product_list', $abc['products']['list'])?>
<?=html_render('pagination/data',$abc['products'])?>

	<?php
}
//если нет товаров
else {
	echo i18n('common|msg_no_results');
}
