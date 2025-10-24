<?php
if ($config['multilingual']) $q['name'] = $q['name'.$lang['i']];
$img = get_img('shop_products',$q,'img','p-');
$title = filter_var($q['name'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$alt = $q['img'] ? 'p-'.$q['img'] : i18n('common|wrd_no_photo');
$url = get_url('shop_product',$q);
?>
<div class="shop_product_random">
	<h4><?=i18n('shop|product_random',true)?></h4>
	<div class="border clearfix">
		<a class="img" href="<?=$url?>" title="<?=$title?>"><img src="<?=$img?>" alt="<?=$title?>" /></a>
		<a class="name" href="<?=$url?>" title="<?=$title?>"><?=$q['name']?></a>
		<?php if ($q['price']>0) {?>
		<div class="price">
			<span><?=price_format($q['price'])?></span> <?=i18n('shop|currency')?>
			<?php if ($q['price2']>0) {?>
			<s><?=price_format($q['price2'])?> <?=i18n('shop|currency')?></s>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (isset($modules['basket']) AND $q['price']>0) {?>
		<a class="btn btn-default pull-left" data-api="/api/basket_product_add/" data-id="<?=$q['id']?>" data-count="1" href="#" title="<?=i18n('basket|buy')?>"><i class="icon-shopping-cart"></i> <?=i18n('basket|buy')?></a>
		<?php } ?>
		<a class="btn btn-primary pull-right" href="<?=$url?>" title="<?=i18n('common|wrd_more')?>"><?=i18n('common|wrd_more')?></a>
	</div>
</div>
