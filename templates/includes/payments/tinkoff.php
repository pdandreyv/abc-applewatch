<?php
/*
* Документация
* https://oplata.tinkoff.ru/landing/develop/plug
*/
if (isset($config['payments'][800])) {
	$config['tinkoff_terminalkey'] = '1508743510969DEMO';
	$params = array(
		'terminalkey'=>$config['tinkoff_terminalkey'],
		'frame'=>'false',//если false то перекинет на сайт тинькоф
		'language'=>$lang['localization'],
		'amount'=>intval($q['total']),//Сумма заказа
		'order'=>$q['id'],//Номер заказа
		//не обязательные поля
		'description'=>'',//Описание заказа
		'name'=>'',//ФИО плательщика
		'email'=>'',//E-mail
		'phone'=>'',//Контактный телефон
	);
	?>
<script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff.js"></script>
<form name="TinkoffPayForm" onsubmit="pay(this); return false;">
	<?php foreach ($params as $k=>$v) {
		?><input type="hidden" name="<?=$k?>" value="<?=$v?>" /><?php
	} ?>
	<input type="submit" value="<?=i18n('order|pay')?>"/>
</form>
	<?php
}
