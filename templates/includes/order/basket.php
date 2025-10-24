<div class="content pb" id="basket">
<?php
$total = 0; // общая стоимость всех товаров
if (isset($q['products']) && is_array($q['products']) && $q['total']>0) {
?>
<form method="post" class="validate">
<table class="table basket_product_list">
<thead>
	<tr>
		<th class="id"><?=i18n('basket|product_id',true)?></th>
		<th class="name" colspan="2"><?=i18n('basket|product_name',true)?></th>
		<th class="price text-right"><?=i18n('basket|product_price',true)?></th>
		<th class="count"><?=i18n('basket|product_count',true)?></th>
		<th class="sum text-right"><?=i18n('basket|product_cost',true)?></th>
		<th>&nbsp;</th>
	</tr>
</thead>
<tbody>
<?php
	$i = 0;
	foreach ($q['products'] as $k=>$v) if ($product = mysql_select("SELECT * FROM shop_products WHERE id=".intval($v['id']),'row')) {

		$i=$i==0 ? 1 : 0;
		$sum = $v['price']*$v['count'];
		$total+= $sum;
		$img = get_img('shop_products',$product,'img');
?>
	<tr class="tr<?=$i?>">
		<td class="id"><?=$v['id']?></td>
		<td class="img"><?php if ($product['img']) {?><img src="/_imgs/100x100<?=$img?>"><?php } ?></td>
		<td class="name"><?=$v['name']?></td>
		<td class="price text-right"><span><?=price_format($v['price'])?></span> <?=i18n('shop|currency')?></td>
		<td class="count"><input class="form-control" name="count[<?=$k?>]" value="<?=$v['count']?>" /></td>
		<td class="sum text-right"><span><?=price_format($sum)?></span> <?=i18n('shop|currency')?></td>
		<td class="delete"><a href="?delete=<?=$k?>" title="<?=i18n('basket|product_delete')?>" ><span class="glyphicon glyphicon-remove"></span></a></td>
	</tr>
<?php } ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="5" class="text-right"><?=i18n('basket|total')?>:</td>
		<td class="total text-right"><span><?=price_format($total)?></span> <?=i18n('shop|currency')?></td>
		<td></td>
	</tr>
</tfoot>
</table>

<h2><?=i18n('basket|delivery',true)?></h2>
<?php
$i = 0;
if($deliveries = mysql_select("SELECT * FROM order_deliveries WHERE `rank`>0",'rows'))
	foreach($deliveries as $k=>$v) {
		$i++;
		$checked = (isset($q['delivery']) AND $q['delivery']==$v['id']) ? ' checked="checked"' : '';
		$checked = $i==1 ? ' checked="checked"' : $checked;
?>
<div class="radio">
	<label>
		<input type="radio" name="delivery_type" value="<?=$v['id']?>" <?=$checked ?>>
		<span><?=$v['name'.$lang['i']]?></span> &ndash; <span<?=editable('order_deliveries|cost|'.$v['id'],'editable_str')?>><?=$v['cost']?></span> <?=i18n('shop|currency',true)?>
		<div><?=$v['text'.$lang['i']]?></div>
	</label>
</div>
<?php } ?>

<div class="basket_box">
	<h2><?=i18n('basket|profile',true)?></h2>
	<div class="form">
	<?=html_array('form/input',array(
		'name'		=>	'email',
		'caption'	=>	i18n('profile|email',true),
		'value'		=>	isset($q['email']) ? $q['email'] : '',
		'attr'		=>	' required email',
	))?>
	<?=html_array('profile/fields',isset($q['user']) ? $q['user'] : array())?>
	</div>
</div>

<div class="basket_box">
	<h2><?=i18n('basket|comment',true)?></h2>
	<div class="form">
	<?=html_array('form/textarea',array(
		'name'			=>	'text',
		'value'			=>	isset($q['text']) ? $q['text'] : '',
		'attr'			=>	' required',
	));
	?>
	</div>
</div>

<div class="clear"></div>
<?=html_array('form/button',array(
	'name' =>	i18n('basket|order'),
));?>
</form>
<?=html_sources('footer','jquery_validate.js')?>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	$('.basket_product_list input').change(function () {
		total();
	})
})
function total () {//каклькулятор в корзине
	var price,count,sum,total=0;
	//для каждого товара в корзине
	$('.basket_product_list tbody tr').each(
		function(){
			price = $(this).find('.price span').text();	//определение цены
			price = price.replace(' ',''); //удаляем пробелы
			price = price.replace(',','.');//меняем запятую на точку
			count = $(this).find('.count input').val();	//определение количетва
			sum = price*count; //стоимость нескольких одинаковый товаров
			total+= parseInt(sum.toFixed(2)); //общая стоимость
			$(this).find('.sum span').text(price_format(sum)); //установка новой стоимости нескольких одинаковый товаров
		}
	)
	$('.basket_product_list tfoot .total span').text(price_format(total)); //установка новой общей стоимости
}
//форматирование цены
function price_format(price) {
	//два знака после запятой
	price = number_format(price, 2, '.', ' ');
	return price;
}
// Format a number with grouped thousands
function number_format( number, decimals, dec_point, thousands_sep ) {
	var i, j, kw, kd, km;
	// input sanitation & defaults
	if( isNaN(decimals = Math.abs(decimals)) ){
		decimals = 2;
	}
	if( dec_point == undefined ){
		dec_point = ",";
	}
	if( thousands_sep == undefined ){
		thousands_sep = ".";
	}
	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
	if( (j = i.length) > 3 ){
		j = j % 3;
	} else{
		j = 0;
	}
	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	//kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
	return km + kw + kd;
}
</script>
<?php
}
else echo i18n('basket|empty');
?>
</div>