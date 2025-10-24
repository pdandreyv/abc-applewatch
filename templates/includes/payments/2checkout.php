<?php
/*
 * todo нужно доработать
 * http://nelex.in.ua/content/подключение-платежной-системы-2checkout-к-вам-на-сайт
 * https://www.2checkout.com/documentation/checkout/parameters
 */
if (isset($config['payments'][700])) {
	$action = 'https://sandbox.2checkout.com/checkout/purchase'; //песочница
	$params = array(
		'sid' => $config['2checkout_id'],
		'mode' => '2CO',
		//тут можно весь массив товаров перечислить
		'li_0_type' => 'product',
		'li_0_name' => 'invoice123',
		'li_0_price' => '25.99',
		'card_holder_name' => 'Checkout Shopper',
		'street_address' => '123 Test Address',
		'street_address2' => 'Suite 200',
		'city' => 'Columbus',
		'state' => 'OH',
		'zip' => '43228',
		'country' => 'USA',
		'email' => 'example@2co.com',
		'phone' => '614-921-2450',
		'x_receipt_link_url' => $config['http_domain']. '/plugins.php?payment_action=2checkout'
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
