<?php
/*
 * Документация
 * https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/
 * https://www.paypal.com/cgi-bin/webscr?cmd=_pdn_xclick_to_cart_outside
 *
 */
if (isset($config['payments'][600])) {
	$action = 'https://www.paypal.com/cgi-bin/webscr';
	$param = array(
		//The button that the person clicked was a Buy Now button.
		'cmd' => '_xclick',
		//Your PayPal ID or an email address associated with your PayPal account.
		'business' => $config['paypal_email'],

		//id заказа
		'item_number' => $q['id'],
		//Description of item.
		'item_name' => $q['id'] . ' | ' . $q['date'],
		//общая стоимость
		'amount' => number_format($q['total'], 2, '.', ''),
		//Валюта
		'currency_code' => 'USD',

		//кодировка
		'charset' => 'utf-8',

		/*
		//не разобрался что за параметры
		'quantity'=>1,
		'no_note'=>1,
		'rm'=>2,//???
		*/

		//урл для уведомления
		'notify_url' => $config['http_domain']. '/api/payments/paypal_result/',
		//урл после успешной оплаты
		'return' => $config['http_domain']. get_url('basket', 'success'),
		//урл после неудачной оплаты/отвены
		'cancel_return' => $config['http_domain']. get_url('basket', 'fail'),
	);
	?>
<form method="post" action="<?= $action ?>">
	<?php foreach ($param as $k => $v) {
		?><input type="hidden" name="<?= $k ?>" value="<?= $v ?>" /><?php
	} ?>
	<input type="submit" value="<?= i18n('order|pay') ?>"/>
</form>
	<?php
}