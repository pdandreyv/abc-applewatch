<?php
/*
 * v.1.1.2
 * Документация
 * https://tech.yandex.ru/money/doc/payment-solution/About-docpage/
 */
if (isset($config['payments'][500])) {
	include(ROOT_DIR . 'plugins/liqpay/LiqPay.php');
	$liqpay = new LiqPay($config['liqpay_public_key'], $config['liqpay_private_key']);

	echo $liqpay->cnb_form(array(
		'action' => 'pay',
		'version' => '3',
		'amount' => $q['total'],
		'currency' => 'UAH',
		'description' => $config['domain'] . ' #' . $q['id'],
		'order_id' => $q['id'],
		'server_url' => $config['http_domain'] . '/api/payments/liqpay_result/',
		'result_url' => $config['http_domain'] . get_url('basket', 'success'),
		'public_key' => $config['liqpay_public_key'],
		//'sandbox' => 1
	));
}
