<?php

//v.1.2.133
//функции работают с одноименными шаблонам в папке /templates/includes/
//вызываются в функциях html_render

function _news_list ($q) {
	$q['_url'] = get_url('news',$q);
	$q['_date'] = date2($q['date'],'%d.%m.%y');
	//$q['_text'] = strip_tags($q['text']);
	//$q['_text'] = truncate($q['_text'],300,'..',false);
	$q['_title'] = filter_var($q['name'],FILTER_SANITIZE_STRING);
	$q['_more'] = i18n('common|wrd_more');
	return $q;
}
function _news_text ($q) {
	$q['_date'] = date2($q['date'],'%d.%m.%y');
	$q['_text'] = hyppertext ($q,'news');
	return $q;
}

function _shop_product_list ($q) {
	global $config,$lang;
	if ($config['multilingual']) $q['name'] = $q['name'.$lang['i']];
	$q['_img'] = get_img('shop_products',$q,'img');
	$q['_title'] = filter_var($q['name'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$q['_alt'] = $q['img'] ? 'p-'.$q['img'] : i18n('common|wrd_no_photo');
	$q['_url'] = get_url('shop_product',$q);
	$q['_price'] = price_format($q['price']);
	$q['_price2'] = price_format($q['price2']);
	$q['_currency'] = i18n('shop|currency');
	$q['_buy'] = i18n('basket|buy');
	$q['_more'] = i18n('common|wrd_more');
	return $q;
}

function _shop_product_text ($q) {
	global $config,$lang;
	$q = _shop_product_list ($q);
	$q['_imgs'] = get_imgs('shop_products',$q,'imgs');
	//$q['_imgs'] = unserialize($q['imgs']);
	dd($q['_imgs']);
	return $q;
}

function _menu_languages ($q) {
	global $lang;
	$q['_title'] = htmlspecialchars($q['name']);
	$q['_url'] = get_url('index', $q);
	$q['active'] = $q['id'] == $lang['id'] ? 1 : 0;
	return $q;
}


function _common_socials($q) {
	$array = explode(PHP_EOL,$q);
	$socials = array();
	foreach ($array as $k=>$v) {
		$socials[] = array(
			'type'=>get_social($v),
			'url'=>$v
		);
	}
	//dd($socials);
	return $socials;
}
