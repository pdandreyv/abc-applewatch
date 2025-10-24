<?php
/**
 * симуляция запроса от пейпала или других систем
 */
//массив с запросом
$str = 'a:37:{s:8:"mc_gross";s:4:"0.01";s:22:"protection_eligibility";s:8:"Eligible";s:14:"address_status";s:9:"confirmed";s:8:"payer_id";s:13:"JWXFVEE6YJ3RL";s:14:"address_street";s:38:" ,  1,  206";s:12:"payment_date";s:25:"01:42:37 Oct 05, 2016 PDT";s:14:"payment_status";s:9:"Completed";s:7:"charset";s:12:"windows-1252";s:11:"address_zip";s:6:"445047";s:10:"first_name";s:9:"";s:6:"mc_fee";s:4:"0.01";s:20:"address_country_code";s:2:"RU";s:12:"address_name";s:15:" ";s:14:"notify_version";s:3:"3.8";s:6:"custom";s:0:"";s:12:"payer_status";s:8:"verified";s:8:"business";s:17:"pestryakov@my.com";s:15:"address_country";s:6:"Russia";s:12:"address_city";s:8:"";s:8:"quantity";s:1:"1";s:11:"verify_sign";s:56:"ADmPnIPY3I7Vr6t4FxyXGQRPk2D-AXfYzfY7rJkjGb.J7aBVbTMPa5Lc";s:11:"payer_email";s:19:"flowerlia@yandex.ru";s:6:"txn_id";s:17:"7PP69941EE358480F";s:12:"payment_type";s:7:"instant";s:9:"last_name";s:5:"";s:13:"address_state";s:17:" ";s:14:"receiver_email";s:17:"pestryakov@my.com";s:11:"payment_fee";s:4:"0.01";s:11:"receiver_id";s:13:"XQ4ACC35RCBJ2";s:8:"txn_type";s:10:"web_accept";s:9:"item_name";s:23:"9 | 2015-03-17 14:07:56";s:11:"mc_currency";s:3:"USD";s:11:"item_number";s:1:"9";s:17:"residence_country";s:2:"RU";s:19:"transaction_subject";s:0:"";s:13:"payment_gross";s:4:"0.01";s:12:"ipn_track_id";s:13:"7aa938261031e";}';
$post = unserialize($str);
//print_r($post);
$action = 'paypal_result';
?>
<form action="/api/payments/<?=$action?>/" method="POST" xmlns="http://www.w3.org/1999/html">
	<strong><?=$action?></strong>
	<?php foreach ($post as $k=>$v) {?>
		<br><?=$k?>
		<br><input name="<?=$k?>" value="<?=$v?>" />
	<?php } ?>
	<br><input type="submit" value="Отправить">
</form>
