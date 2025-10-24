<?=html_sources('footer','highslide_gallery')?>
<?php
$imgs = $q['imgs'] ? unserialize($q['imgs']) : false;
$parameters = $q['parameters'] ? unserialize($q['parameters']) : false;
$shop_parameters = false;
$brand = $q['brand'] ? get_data('shop_brands',$q['brand']) : array();
//массив картинок
$images = array();
/* *
//если нужно сделать общий массив из всех картинок то расскоментировать
if ($q['img']) {
	$images[] = array(
		'alt'=>filter_var(@$q['name'],FILTER_SANITIZE_STRING),
		'title'=>filter_var(@$q['name'],FILTER_SANITIZE_STRING),
		'img' => get_img('shop_products',$q,'img'),
		'preview' => get_img('shop_products',$q,'img','p-')
	);
}
/* */
if ($imgs) foreach ($imgs as $k=>$v) if (@$v['display']==1) {
	$images[] = array(
		'alt'=>filter_var(@$v['name'],FILTER_SANITIZE_STRING),
		'title'=>@$v['title'] ? filter_var(@$v['title'],FILTER_SANITIZE_STRING) : filter_var(@$v['name'],FILTER_SANITIZE_STRING),
		'img' => get_img('shop_products',$q,'imgs/'.$k),
		'preview' => get_img('shop_products',$q,'imgs/'.$k,'p-')
	);
}

if (is_array($parameters)) {
	$prms=array();
	foreach ($parameters as $k=>$v) if (@$v['display'] AND @$v['product']) $prms[]=$k;
	$shop_parameters = mysql_select("
		SELECT * FROM shop_parameters
		WHERE display=1 AND id IN('".implode("','",$prms)."')
		ORDER BY `rank` DESC
	",'rows_id');
}
$title = filter_var($q['name'],FILTER_SANITIZE_STRING);
?>
<h1><?=@$q['h1']?$q['h1']:$q['name']?></h1>
<div class="shop_product_text content">
	<div class="gallary">
		<?php if ($q['img']) {?><a title="<?=$title?>" onclick="return hs.expand(this, config1)" href="<?=$q['_img']?>"><?php } ?>
		<img src="/_imgs/400x400<?=$q['_img']?>" alt="<?=$title?>" />
		<?php if ($q['img']) {?></a><?php } ?>
		<?php if ($q['_imgs']) {?>
			<div class="carousel">
				<ul><?php
					foreach ($q['_imgs'] as $k=>$v) {
						?>
					<li><a title="<?=@$v['title']?>" onclick="return hs.expand(this, config1)" href="<?=$v['_']?>"><img src="/_imgs/150x150<?=$v['_']?>" alt="<?=@$v['name']?>" title="<?=@$v['title']?>" /></a></li>
						<?php
					}
				?></ul>
				<a class="next" href="#" title=""></a>
				<a class="prev" href="#" title=""></a>
			</div>
		<?php } ?>
	</div>
	<div class="info">

		<?php if ($parameters OR $q['brand_name'] OR $q['article']) {?>
		<dl class="dl-horizontal">
			<?php if ($q['article']) {?>
			<dt><?=i18n('shop|article',true)?>:</dt>
			<dd <?=editable('shop_products|article|'.$q['id'])?>><?=$q['article']?></dd>
			<?php } ?>
			<?php if ($brand) {?>
			<dt><?=i18n('shop|brand',true)?>:</dt><dd><?=$brand['name'.$lang['i']]?></dd>
			<?php } ?>
			<?php if ($parameters) foreach($parameters as $k=>$v) if ($q['p'.$k]!=0 AND isset($shop_parameters[$k])) {?>
			<dt><?=$shop_parameters[$k]['name']?>:</dt>
			<dd><?php
				$values = $shop_parameters[$k]['values'] ? unserialize($shop_parameters[$k]['values']) : array();
				if (in_array($shop_parameters[$k]['type'],array(1,3))) echo @$values[$q['p'.$k]];
				elseif ($shop_parameters[$k]['type']==2) echo $q['p'.$k];
				//v1.2.83 - вывод множественного параметра на старнице товара
				elseif ($shop_parameters[$k]['type']==4) {
					$array = explode(',',$q['p'.$k]);
					$params = array();
					foreach ($array as $k1=>$v1) if (@$values[$v1]) $params[] = @$values[$v1];
					if ($params) echo implode(', ',$params);
				}
				if ($shop_parameters[$k]['units']) echo $shop_parameters[$k]['units'];
			?></dd>
			<?php } ?>
		</dl>
		<?php } ?>
		<?php if ($q['price']>0) {?>
		<div class="price">
			<span<?=editable('shop_products|price|'.$q['id'])?>><?=$q['_price']?></span> <?=i18n('shop|currency',true)?>
			<?php if ($q['price2']>0){?>
			<s><span<?=editable('shop_products|price2|'.$q['id'])?>><?=$q['_price2']?></span> <?=i18n('shop|currency',true)?></s>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if ($q['price']>0) {?>
		<a class="btn btn-default pull-left" data-api="/api/basket_product_add/" data-id="<?=$q['id']?>" data-count="1" href="#" title="<?=i18n('basket|buy')?>"><i class="icon-shopping-cart"></i> <?=i18n('basket|buy')?></a>
		<?php } ?>

		<?=html_array('common/share')?>
		<div class="text"><?=$q['text']?></div>
	</div>
	<div class="clearfix"></div>
	<?=html_query('shop/review_list',"SELECT * FROM shop_reviews WHERE display=1 AND product=".$q['id']." ORDER BY date DESC",'')?>
	<?=html_array('shop/review_form',$q)?>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	//количетство фото в блоке
	var count = $('.shop_product_text .gallary .carousel ul li').length;
	//ширина одной фото
	var margin = $('.shop_product_text .gallary .carousel ul li:first-child').outerWidth();
	//задаем ширину всего блока
	$('.shop_product_text .gallary ul').width(count*margin);
	//поках стрелочек при ховере
	$('.shop_product_text .gallary .carousel').hover(
		function () {
			$('.shop_product_text .carousel .next, .shop_product_text .carousel .prev').show().css('display','block');
		},
		function () {
			$('.shop_product_text .carousel .next, .shop_product_text .carousel .prev').hide();
		}
	);
	//перемотка
	$('.shop_product_text .gallary .next,.shop_product_text .gallary .prev').click(function(){
		//текущее смещение (отрицательное значение)
		var left = parseInt($('.shop_product_text .gallary ul').css('margin-left'));
		//ширина всего блока
		var width_total = parseInt($('.shop_product_text .gallary ul').width());
		//ширина видимой части
		var width_box = parseInt($('.shop_product_text .gallary .carousel').width());
		//ширина одной фото
		var margin = $('.shop_product_text .gallary .carousel ul li:first-child').outerWidth();
		//console.log(width_total+' '+width_box+' '+left+' '+margin);
		//перемотка вперед
		if ($(this).hasClass('next')) {
			//если ширина видимой части + смещение больше общей ширины то больше не мотаем
			if (width_box+margin-left>width_total) return false;
			left = left-margin;
		}
		//перемотка назад
		else {
			if (left>=0) return false;
			left = left+margin;
		}
		$('.shop_product_text .gallary ul').animate({marginLeft:left+'px'},500);
		return false;
	});
});
</script>
