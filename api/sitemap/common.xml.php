<?php

//файл генерирует xml документ из всех страниц сайта для поисковиков
//доступен по адресу /api/sitemap/common.xml

/*
 * v1.3.37 - created_at - mysql_fn
 */

header('Content-type: text/xml; charset=UTF-8');

$config['cache'] = false;
$cache = 60 * 60; //один час
$file = ROOT_DIR . '/api/sitemap/common.xml';

//если не указана генерация файла или кеш еще актуальный
if (file_exists($file) AND (time() - $cache) < filemtime($file)) {
	echo file_get_contents($file);
	die();
}

/*
$content = '<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url><loc>http://'.$config['domain'].'/</loc></url>';
*/
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');

//генерация всех ссылок
$languages = mysql_select("SELECT * FROM languages WHERE display=1 ORDER BY `rank` DESC", 'rows');
foreach ($languages as $lang) {
	//язык
	$lang = lang($lang['id']);
	//список модулей на сайте
	$modules = mysql_select("
		SELECT url name,module id
		FROM pages
		WHERE module!='pages' AND noindex=0 AND display=1 AND language=" . $lang['id'] . "
	", 'array', 60 * 60);
	//дерево сайта
	if ($pages = mysql_select("
		SELECT module,url,updated_at FROM pages
		WHERE noindex=0 AND display=1 AND language=" . $lang['id'] . "
		ORDER BY left_key", "rows")
	) {
		foreach ($pages as $q) {
			$urls[] = array(
				'loc' => $config['http_domain'] . get_url('page', $q),
				'lastmod' => date2($q['updated_at'], '%Y-%m-%d'),
				//'changefreq'=>'monthly',
				//'priority'=>0.5
			);
		}
	}
	//новости
	if (isset($modules['news']) AND $pages = mysql_select("
		SELECT id,url,updated_at
		FROM news
		WHERE display=1
		ORDER BY date DESC", 'rows')
	) {
		foreach ($pages as $q) {
			$urls[] = array(
				'loc' => $config['http_domain'] . get_url('news', $q),
				'lastmod' => date2($q['updated_at'], '%Y-%m-%d'),
				//'changefreq'=>'monthly',
				//'priority'=>0.8
			);
		}
	}
	//галерея
	if (isset($modules['gallery']) AND $pages = mysql_select("
		SELECT id,url,updated_at
		FROM gallery
		WHERE display=1
		ORDER BY `rank` DESC", 'rows')
	) {
		foreach ($pages as $q) {
			$urls[] = array(
				'loc' => $config['http_domain'] . get_url('gallery', $q),
				'lastmod' => date2($q['updated_at'], '%Y-%m-%d'),
				//'changefreq'=>'monthly',
				//'priority'=>0.8
			);
		}
	}
	//каталог
	if (isset($modules['shop'])) {
		//товары
		if ($pages = mysql_select("
			SELECT sp.*
			FROM shop_products sp, shop_categories sc
			WHERE sp.display=1 AND sc.display=1 AND sp.category=sc.id
			ORDER BY sc.left_key,sp.id", 'rows')
		) {
			foreach ($pages as $q) {
				$urls[] = array(
					'loc' => $config['http_domain'] . get_url('shop_product', $q),
					'lastmod' => date2($q['updated_at'], '%Y-%m-%d'),
					//'changefreq'=>'monthly',
					//'priority'=>0.8
				);
			}
		}
		//категории
		if ($pages = mysql_select("
			SELECT *
			FROM shop_categories
			WHERE display=1
			ORDER BY left_key", 'rows')
		) {
			foreach ($pages as $q) {
				$urls[] = array(
					'loc' => $config['http_domain'] . get_url('shop_category', $q),
					'lastmod' => date2($q['updated_at'], '%Y-%m-%d'),
					//'changefreq'=>'monthly',
					//'priority'=>0.8
				);
			}
		}
	}

	foreach ($urls as $val) {
		/*$content.= '
	<url><loc>http://'.$config['domain'].$v.'</loc></url>';*/
		$url = $xml->addChild('url');
		foreach ($val as $k => $v) {
			$url->addChild($k, $v);
		}
	}
}

//генерация только не в индексе
/*
elseif (@$config['sitemap_generation']==2) {
	$urls[] = sitemap("SELECT url FROM seo_pages WHERE exist=1 AND yandex_index=0 ORDER BY yandex_check",'{url}');
	foreach ($urls as $k=>$v) {
		$url = $xml->addChild('url');
		$url->addChild('loc', $config['http_domain'].$v);
	}
}
*/
/*$content.= '
</urlset>';*/

$content = $xml->asXML();

//запись в файл
$fp = fopen($file, 'w');
fwrite($fp, $content);
/**/

echo $content;

die();