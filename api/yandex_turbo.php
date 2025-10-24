<?php

/**
 * файл генерирует rss-xml документ для турбостраниц
 * доступен по адресу /api/yandex_turbo/
 *
 */

$config['cache'] = false;

//header('Content-type: text/xml; charset=UTF-8');
header('Content-Type: application/rss+xml; charset=utf-8');

/*$xml = new SimpleXMLElement('<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0"
/>');

$channel = $xml->addChild('channel');
*/
$urls = array();
$languages = mysql_select("SELECT * FROM languages WHERE display=1 ORDER BY `rank` DESC",'rows');
foreach ($languages as $lang) {
	//язык
	$lang = lang($lang['id']);
	//список модулей на сайте
	$modules = mysql_select("
		SELECT url name,module id
		FROM pages
		WHERE module!='pages' AND noindex=0 AND display=1 AND language=".$lang['id']."
	", 'array', 60 * 60);
	//дерево сайта
	if ($pages = mysql_select("
		SELECT id,module,url,name,h1,text,imgs,video FROM pages
		WHERE noindex=0 AND display=1 AND language=".$lang['id']."
		ORDER BY left_key", "rows")) {
		foreach ($pages as $q) {
			$urls[] = array(
				'link'=>$config['http_domain'].get_url('page',$q),
				'turbo:content'=>turbo_content($q,'pages'),
			);
		}
	}
	//новости
	if (isset($modules['news']) AND $pages = mysql_select("
		SELECT id,url,name,h1,text,imgs,video
		FROM news
		WHERE display=1
		ORDER BY date DESC", 'rows')) {
		foreach ($pages as $q) {
			$urls[] = array(
				'link'=>$config['http_domain'].get_url('news',$q),
				'turbo:content'=>turbo_content($q,'news'),
			);
		}
	}
	//каталог
	if (isset($modules['shop'])) {
		//товары
		if ($pages = mysql_select("
			SELECT sp.id,sp.category,sp.name,sp.h1,sp.url,sp.text
			FROM shop_products sp, shop_categories sc
			WHERE sp.display=1 AND sc.display=1 AND sp.category=sc.id
			ORDER BY sc.left_key,sp.id", 'rows')) {
			foreach ($pages as $q) {
				$urls[] = array(
					'link'=>$config['http_domain'].get_url('shop_product', $q),
					'turbo:content'=>turbo_content($q,'product'),
				);
			}
		}
		//категории
		if ($pages = mysql_select("
			SELECT id,name,h1,url,text
			FROM shop_categories
			WHERE display=1
			ORDER BY left_key", 'rows')) {
			foreach ($pages as $q) {
				$urls[] = array(
					'link'=>$config['http_domain'].get_url('shop_category', $q),
					'turbo:content'=>turbo_content($q,'category'),
				);
			}
		}
	}
}

//$content = $xml->asXML();
if ($urls) {
	echo '<?xml version="1.0" encoding="utf-8"?>
<rss
	xmlns:yandex="http://news.yandex.ru"
	xmlns:media="http://search.yahoo.com/mrss/"
	xmlns:turbo="http://turbo.yandex.ru"
	version="2.0"
>
	<channel>';
	foreach ($urls as $val)  {
		//$item = $channel->addChild('item');
		//$item->addAttribute('turbo', true);
		//если нет текста то и не нужно делать турбо страницу
		if ($val['turbo:content']) {
			echo '<item turbo="true">';
			foreach ($val as $k => $v) {
				//$item->addChild($k,  $v);
				echo '<' . $k . '>' . $v . '</' . $k . '>';
			}
			echo '</item>';
		}
	}
	echo '
	</channel>
</rss>';
}

//echo $content;

/**
 * @param $q - данные элемента
 * @param string $type - тип материала
 * @return string - код для turbo:content
 */
function turbo_content($q,$type='') {
	$name = $q['name'];
	if (@$q['h1'])$name = $q['h1'];
	$text = $q['text'];
	if ($type=='news') {
		$text = template_img('news', $q);
		$text = template_video($text, $q['video']);
	}
	if ($type=='pages') {
		$text = template_img('pages', $q);
		$text = template_video($text, $q['video']);
	}
	$content = '';
	//если нет текста то и не нужно делать турбо страницу
	if ($text) {
		$content = '<![CDATA[
<header>
	<h1>' . $name . '</h1>
</header>
' . $text . '
]]>';
	}
	return $content;
}

die();