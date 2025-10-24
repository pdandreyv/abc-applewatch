<?php
/*
 * Документация
 * https://tech.yandex.ru/money/doc/payment-solution/About-docpage/
 */
if (isset($config['payments'][200])) {
	//тестовый сервер
	if ($config['yandex_demo']) $action = 'https://demomoney.yandex.ru/eshop.xml';
	//боевой сервер
	else $action = 'https://money.yandex.ru/eshop.xml';
	$params = array(
		//Идентификатор магазина, выдается при подключении к Яндекс.Кассе.
		'shopId' => $config['yandex_shopId'],
		//Идентификатор витрины магазина, выдается при подключении к Яндекс.Кассе.
		'scid' => $config['yandex_scid'],
		//Идентификатор плательщика в системе магазина. В качестве идентификатора может использоваться номер договора плательщика, логин плательщика и т. п.
		'customerNumber' => $config['yandex_customerNumber'],

		//Уникальный номер заказа в системе магазина. Уникальность контролируется Яндекс.Деньгами в сочетании с параметром shopId.
		'orderNumber' => $q['id'],
		//Стоимость заказа. (рубли)
		'sum' => $q['total'],

		//Способ оплаты - можно поставить радиобаттонами
		//https://tech.yandex.ru/money/doc/payment-solution/reference/payment-type-codes-docpage/
		//'paymentType'=>'PC'//яндекс деньги

		//URL, на который будет вести ссылка «Вернуться в магазин» со страницы успешного платежа.
		'shopSuccessURL' => $config['http_domain']. get_url('basket', 'success'),
		//URL, на который будет вести ссылка «Вернуться в магазин» со страницы ошибки платежа.
		'shopFailURL' => $config['http_domain']. get_url('basket', 'fail'),
	);
	?>
<form action="<?= $action ?>" method="post">
	<?php foreach ($params as $k => $v) {
		?><input type="hidden" name="<?= $k ?>" value="<?= $v ?>" /><?php
	} ?>
	<input type="submit" value="<?= i18n('order|pay') ?>"/>
</form>
	<?php
}