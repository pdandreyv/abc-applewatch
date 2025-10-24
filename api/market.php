<?php

//not_visible
/**
 * файл генерирует xml документ для yandex market
 * доступен по адресу /api/market/
 */

header('Content-type: text/xml; charset=UTF-8');

$cache = 60*60*1; //1 час
$file = ROOT_DIR.'market.xml';
if (file_exists($file) && (time()-$cache)<filemtime($file)) {
	echo file_get_contents($file);
	die();
}/**/

//основной язык
$lang = lang(); //print_r($lang);

//список модулей на сайте
$modules = mysql_select("SELECT url name,module id FROM pages WHERE module!='pages' AND language=".$lang['id']." AND display=1",'array',60*60);

$categories = mysql_select("SELECT id,url,name,parameters,parent FROM shop_categories WHERE display=1",'rows_id');
$shop_parameters = mysql_select("SELECT * FROM shop_parameters WHERE display=1 ORDER BY `rank` DESC ",'rows_id');

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml.= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
$xml.= '<yml_catalog date="'.date('Y-m-d h:m').'">';
$xml.= '<shop>';
$xml.= '<name>'.i18n('market|name').'</name>';
$xml.= '<company>'.i18n('market|company').'</company>';
$xml.= '<url>'.$config['http_domain'].'/</url>';
$xml.= '<currencies>';
$xml.= '<currency id="'.i18n('market|currency').'" rate="1"/>';
$xml.= '</currencies>';
$xml.= '<categories>';
//============
foreach ($categories as $k=>$v) {
	$xml.= '
<category id="'.$v['id'].'" parentId="'.$v['parent'].'">'.$v['name'].'</category>';
}
$xml.= '</categories>';
$xml.= '<offers>';
//==========
if ($shop_products = mysql_select("SELECT * FROM shop_products WHERE market=1 AND price>0",'rows')) {
	foreach ($shop_products as $q) {
		$q['category_url'] = $categories[$q['category']]['url'];
		$xml .= '
<offer id="' . $q['id'] . '" available="true">';
		$xml .= '<url>' . $config['http_domain'] . get_url('shop_product',$q) . '</url>';
		$xml .= '<price>' . $q['price'] . '</price>';
		$xml .= '<currencyId>' . i18n('market|currency') . '</currencyId>';
		$xml .= '<categoryId>' . $q['category'] . '</categoryId>';
		if ($q['img']) {
			$img = get_img('shop_products',$q,'img','');
			if (file_exists(ROOT_DIR . $img)){
				$xml .= '<picture>' . $config['http_domain'] . $img . '</picture>';
			}
		}
		$xml .= '<name>' . htmlspecialchars($q['name']) . '</name>';
		$xml .= '<description>';
		$parameters = $categories[$q['category']]['parameters'] ? unserialize($categories[$q['category']]['parameters']) : false;
		if ($parameters) foreach ($parameters as $k => $v) if ($q['p' . $k] != 0 AND isset($shop_parameters[$k])) {
			$name = $shop_parameters[$k]['name'];
			$values = $shop_parameters[$k]['values'] ? unserialize($shop_parameters[$k]['values']) : array();
			if (in_array($shop_parameters[$k]['type'], array(1, 3))) $name .= ': ' . @$values[$q['p' . $k]];
			elseif ($shop_parameters[$k]['type'] == 2) $name .= ': ' . $q['p' . $k];
			if ($shop_parameters[$k]['units']) $name .= ' ' . $shop_parameters[$k]['units'];
			$name .= '; ';
			$xml .= htmlspecialchars($name);
		}
		$xml .= '</description>';
		//$xml.= '<sales_notes>Минимальная сумма заказа 10 000 рублей.</sales_notes>';
		$xml .= '</offer>';
	}
}
$xml.= '</offers>';
$xml.= '</shop>';
$xml.= '</yml_catalog>';


//запись в файл
$fp = fopen(ROOT_DIR.'market.xml','w');
fwrite($fp, $xml);
fclose($fp);
/* */

echo $xml;

die();