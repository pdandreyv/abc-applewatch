<?php

if (isset($config['payments'][900])) {
	$action = $config['http_domain'].'/api/payments/sberbank_order/';
	$params = array(
		'id' => $q['id'],
		'hash'=> md5($q['id'] . $q['date'])
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
