<?php

//корневой сайтмап для ссылок на другие сайтмапы
//доступен по адресу /api/sitemap/index.xml

header('Content-type: text/xml; charset=UTF-8');

$config['cache'] = false;

$xml = new SimpleXMLElement('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');

//текстовые и другие страницы
$sitemap = $xml->addChild('sitemap');
$sitemap->addChild('loc', $config['http_domain'].'/api/sitemap/common.xml');

//товары
$products = mysql_select("
	SELECT COUNT(id) FROM shop_products WHERE display=1
",'string');
//разбиваем по 10 тыс
$n = intval($products/10000);
for ($i = 0; $i <= $n; ++$i) {
	$sitemap = $xml->addChild('sitemap');
	$sitemap->addChild('loc', $config['http_domain'].'/api/sitemap/products/'.$i.'/');
}

echo $xml->asXML();
//$content =  $xml->asXML();
//$zip =  gzencode($xml->asXML(), 9);

die();