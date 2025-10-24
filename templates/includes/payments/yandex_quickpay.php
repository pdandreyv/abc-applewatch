<?php
//оплата сразу на кошель яндекса
//статья - https://misha.blog/yandex/http-uvedomleniya.html
//документация - https://money.yandex.ru/i/forms/guide-to-custom-p2p-forms.pdf
//настройка данных - https://money.yandex.ru/myservices/online.xml
if (isset($config['payments'][250])) {
	$action = 'https://money.yandex.ru/quickpay/confirm.xml';
	$params = array(
		//Номер кошелька в системе Яндекс Денег
		'receiver' => $config['yandex2_receiver'],
		//Название платежа, можно адрес сайта (длина 50 символов)
		'formcomment' => $config['domain'],
		//ID плагина - у нас ИД заказа (длина 64 символа)
		'label' => $q['id'],
		//Тип формы, может принимать значения shop (универсальное), donate (благотворительная), small (кнопка)-->
		'quickpay-form' => 'shop',
		//Назначение платежа, это покупатель видит на сайте Яндекс Денег при вводе платежного пароля (длина 150 символов)-->
		'targets' => '#' . $q['id'],
		//Стоимость заказа. (рубли)
		'sum' => $q['total'],
		//Должен ли Яндекс запрашивать ФИО покупателя
		'need-fio' => 'false',
		//Должен ли Яндекс запрашивать email покупателя
		'need-email' => 'false',
		//Должен ли Яндекс запрашивать телефон покупателя
		'need-phone' => 'false',
		//Должен ли Яндекс запрашивать адрес покупателя-->
		'need-address' => 'false',
		//Способ оплаты - можно поставить радиобаттонами
		//https://tech.yandex.ru/money/doc/payment-solution/reference/payment-type-codes-docpage/
		//Метод оплаты, PC - Яндекс Деньги, AC - банковская карта-->
		'paymentType' => 'PC',
		//URL, на который будет вести ссылка «Вернуться в магазин» со страницы успешного платежа.
		'shopSuccessURL' => $config['http_domain'] . get_url('basket', 'success'),
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