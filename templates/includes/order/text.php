<?php
$basket = unserialize($q['basket']);
//dd($abc['page']);
?>
<div class="content pb">

	<?php /*
<h1><?=$abc['page']['name']?></h1>
 */?>

<?=i18n('basket|order_status')?>: <?=$q['ot_name']?> | <?=$q['paid']==1?i18n('order|paid',true):i18n('order|not_paid',true)?>

<div style="padding:10px 0"><?=$q['ot_text']?></div>

<table class="table basket_product_list">
<thead>
	<tr>
		<th class="id"><?=i18n('basket|product_id',true)?></th>
		<th class="name"><?=i18n('basket|product_name',true)?></th>
		<th class="price text-right"><?=i18n('basket|product_price',true)?></th>
		<th class="count text-right"><?=i18n('basket|product_count',true)?></th>
		<th class="sum text-right"><?=i18n('basket|product_cost',true)?></th>
	</tr>
</thead>
<tbody>
<?php
$i = 0;
foreach ($basket['products'] as $k=>$v) {
	$i = $i==0 ? 1 : 0;
	$sum = $v['price']*$v['count'];
?>
<tr class="tr<?=$i?>">
	<td class="id"><?=$v['id']?></td>
	<td class="name"><?=$v['name']?></td>
	<td class="price text-right"><span><?=price_format($v['price'])?></span><?=i18n('shop|currency')?></td>
	<td class="count text-right"><?=$v['count']?></td>
	<td class="sum text-right"><span ><?=price_format($sum)?></span><?=i18n('shop|currency')?></td>
</tr>
<?php } if ($basket['delivery']['type']) { ?>
<tr>
	<td colspan="4"  style="text-align:right"><?=i18n('basket|delivery_cost',true)?>
	<?php
	$delivery = mysql_select("SELECT * FROM order_deliveries WHERE id = '".intval($basket['delivery']['type'])."'",'row');
	if ($delivery) {
		if ($config['multilingual']) $delivery['name'] = $delivery['name'.$lang['i']];
		echo '('.$delivery['name'].')';
	}
	?>:
	</td>
	<td class="text-right"><?=price_format($basket['delivery']['cost'])?><?=i18n('shop|currency')?></td>
</tr>
<?php } ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="4"><?=i18n('basket|total')?>:</td>
		<td class="total text-right"><span><?=price_format($q['total'])?></span> <?=i18n('shop|currency')?></td>
	</tr>
</tfoot>
</table>

<h2><?=i18n('basket|profile',true)?></h2>
<dl class="dl-horizontal">
	<dt><?=i18n('profile|email',true)?>:</dt>
	<dd><?=$q['email']?></dd>
<?php
if (is_array($basket['user'])) {
	if ($fields = mysql_select("SELECT * FROM user_fields WHERE display = 1 ORDER BY `rank` DESC",'rows')) {
		foreach ($fields as $f) if (isset($basket['user'][$f['id']])) {
			if ($config['multilingual']) $f['name'] = $f['name' . $lang['i']];
			?>
			<dt><?= $f['name'] ?>:</dt>
			<dd><?php
				if ($f['type'] == 2) {
					$values = $f['values'] ? unserialize($f['values']) : '';
					echo $values[$basket['user'][$f['id']][0]];
				} else echo $basket['user'][$f['id']][0];
				?></dd>
		<?php
		}
	}
}
?>
</dl>

<?php if ($basket['text']) {?>
	<h2><?=i18n('basket|comment',true)?></h2>
	<?=str_replace ("\n",'<br />',$basket['text']);?>
<?php } ?>


<?php
//способы оплаты
if ($q['paid']==0) {
	?><h2><?=i18n('order|payments',true)?></h2><?php
	echo html_array('payments/robokassa',$q);
	echo html_array('payments/yandex',$q);
	echo html_array('payments/paypal',$q);
	echo html_array('payments/yandex_quickpay',$q);
	echo html_array('payments/tinkoff',$q);
	echo html_array('payments/alfabank',$q);
	echo html_array('payments/sberbank',$q);
}
?>

</div>