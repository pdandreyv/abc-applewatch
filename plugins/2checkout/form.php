<?php
/**
 * http://nelex.in.ua/content/подключение-платежной-системы-2checkout-к-вам-на-сайт
 * https://www.2checkout.com/documentation/checkout/parameters
 */

$config['2checkout_id'] = '202321469';

$sid = $config['2checkout_id'];

$link = $config['domain'].'/result.php';

?>
<br><br>
<form action='https://sandbox.2checkout.com/checkout/purchase' method='post'>
	<br><input  name='sid' value='901272222' />
	<br><input  name='mode' value='2CO' />
	<br><input  name='li_0_type' value='product' />
	<br><input  name='li_0_name' value='invoice123' />
	<br><input  name='li_0_price' value='25.99' />
	<br><input  name='card_holder_name' value='Checkout Shopper' />
	<br><input  name='street_address' value='123 Test Address' />
	<br><input  name='street_address2' value='Suite 200' />
	<br><input  name='city' value='Columbus' />
	<br><input  name='state' value='OH' />
	<br><input  name='zip' value='43228' />
	<br><input  name='country' value='USA' />
	<br><input  name='email' value='example@2co.com' />
	<br><input  name='phone' value='614-921-2450' />
	<br><input  name="x_receipt_link_url" value="<?=$link?>" >
	<br><input name='submit' type='submit' value='Checkout' />
</form>
