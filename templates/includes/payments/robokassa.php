<?php
/*
 * Документация
 * http://docs.robokassa.ru/#1186
 */
if (isset($config['payments'][100])) {
	$action = 'https://auth.robokassa.ru/Merchant/Index.aspx?'; //реальная оплата
	$params = array(
		//Идентификатор магазина в ROBOKASSA, который Вы придумали при создании магазина.
		'MrchLogin' => $config['robokassa_login'],
		//Требуемая к получению сумма (буквально — стоимость заказа, сделанного клиентом)
		'OutSum' => $q['total'],
		//Номер счета в магазине.
		'InvId' => $q['id'],
		//способы оплаты http://docs.robokassa.ru/#1281
		//'IncCurrLabel'=>$IncCurrLabel[$q['merchant']],
		//Описание покупки, можно использовать только символы английского или русского алфавита, цифры и знаки препинания. Максимальная длина — 100 символов. Эта информация отображается в интерфейсе ROBOKASSA и в Электронной квитанции, которую мы выдаём клиенту после успешного платежа. Корректность отображения зависит от необязательного параметра Encoding
		'Desc' => $q['id'] . ' | ' . $q['date'],
		//контрольная суммаhttp://docs.robokassa.ru/#4146
		'SignatureValue' >= md5($config['robokassa_login'] . ':' . $q['total'] . ':' . $q['id'] . ':' . $config['robokassa_password1'] . ':Shp_item=1'),
		//тип товара
		'Shp_item' => 1,
		//Язык общения с клиентом en, ru.
		'Culture' => 'ru',
		//Кодировка, в которой отображается страница ROBOKASSA
		'Encoding' => 'utf-8',
		//Срок действия счета. ISO 8601 (YYYY-MM-DDThh:mm:ss.fffffffZZZZZ)
		//'ExpirationDate'=>''
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