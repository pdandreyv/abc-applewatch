<?php

/*
 * скрипт для выполнения прямых запросов к базе
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');

mysql_connect_db();
$tables = array(
	array('feedback'=>'files'),
	array('shop_products'=>'imgs'),
	array('shop_products'=>'images'),
	array('gallery'=>'images'),
);

foreach ($tables as $arr) {
	foreach ($arr as $table=>$key) {
		$items = mysql_select("SELECT id,".$key." FROM ".$table."",'rows');
		foreach ($items as $q) {
			$q[$key] = $q[$key] ? unserialize($q[$key]) : array();
			$q[$key] = $q[$key] ? json_encode($q[$key]) : '';
			mysql_fn('update',$table,$q);
		}
	}
}
//unserialize = json_decode true
//serialize = json_encode

//список скл запросов
$queries = array(

);

foreach ($queries as $query) {
	if ($query) {
		if (mysql_fn('query',$query,'affected_rows')) echo '<div style="color:#00f">'.$query.'</div>';
		else echo '<div style="color:#f00">'.$query.' - '.mysqli_error($config['mysql_connect']).'</div>';
	}
}

/* *
	"ALTER TABLE  `seo_pages` ADD  `articles` VARCHAR( 255 ) NOT NULL COMMENT  'ИД статтей' AFTER  `links`"
	/* *
	"ALTER TABLE  `shop_categories` ADD  `name1` VARCHAR( 255 ) NOT NULL AFTER  `name` ",
	"UPDATE  `shop_categories` SET  name1= `name`",
	"ALTER TABLE  `shop_categories` ADD  `url1` VARCHAR( 255 ) NOT NULL AFTER  `url` ",
	"UPDATE  `shop_categories` SET  url1= `url`",
	"ALTER TABLE  `shop_categories` ADD  `title1` VARCHAR( 255 ) NOT NULL AFTER  `title` ",
	"UPDATE  `shop_categories` SET  title1= `title`",
	"ALTER TABLE  `shop_categories` ADD  `keywords1` VARCHAR( 255 ) NOT NULL AFTER  `keywords` ",
	"UPDATE  `shop_categories` SET  keywords1= `keywords`",
	"ALTER TABLE  `shop_categories` ADD  `description1` VARCHAR( 255 ) NOT NULL AFTER  `description` ",
	"UPDATE  `shop_categories` SET  description1= `description`",
	/* *
	"UPDATE  `shop_products` SET  name1= `name`",
	"ALTER TABLE  `shop_products` ADD  `url1` VARCHAR( 255 ) NOT NULL AFTER  `url` ",
	"UPDATE  `shop_products` SET  url1= `url`",
	"ALTER TABLE  `shop_products` ADD  `title1` VARCHAR( 255 ) NOT NULL AFTER  `title` ",
	"UPDATE  `shop_products` SET  title1= `title`",
	"ALTER TABLE  `shop_products` ADD  `keywords1` VARCHAR( 255 ) NOT NULL AFTER  `keywords` ",
	"UPDATE  `shop_products` SET  keywords1= `keywords`",
	"ALTER TABLE  `shop_products` ADD  `description1` VARCHAR( 255 ) NOT NULL AFTER  `description` ",
	"UPDATE  `shop_products` SET  description1= `description`",
	/* *
	"ALTER TABLE  `user_fields` ADD  `name1` VARCHAR( 255 ) NOT NULL AFTER  `name`",
	"UPDATE  `user_fields` SET  name1= `name`",
	"ALTER TABLE  `user_fields` ADD  `hint1` VARCHAR( 255 ) NOT NULL AFTER  `hint`",
	"UPDATE  `user_fields` SET  hint1= `hint`",
	/* *
	"ALTER TABLE  `order_deliveries` ADD  `name1` VARCHAR( 255 ) NOT NULL AFTER  `name`",
	"UPDATE  `order_deliveries` SET  name1= `name`",
	"ALTER TABLE  `order_deliveries` ADD  `text1` TEXT NOT NULL AFTER  `text`",
	"UPDATE  `order_deliveries` SET  text1= `text`",
	/* *
	//v1.0.30
	"ALTER TABLE  `order_types` ADD  `name1` VARCHAR( 255 ) NOT NULL AFTER  `name`",
	"UPDATE  `order_types` SET  name1= `name`",
	"ALTER TABLE  `order_types` ADD  `text1` TEXT NOT NULL AFTER  `text`",
	"UPDATE  `order_types` SET  text1= `text`",
	/* *
	//v1.1.19
	"ALTER TABLE  `shop_parameters` ADD  `import` TINYINT( 1 ) NOT NULL COMMENT  'использовать при импорте/экспорте' AFTER  `values`",
	/* *
	//v1.1.30
	"ALTER TABLE  `logs` ADD  `ip` VARCHAR( 32 ) NOT NULL COMMENT  'IP адрес' AFTER  `date`",
	"ALTER TABLE  `logs` ADD  `fields` VARCHAR( 1000 ) NOT NULL COMMENT  'затронутые поля'",
	/* *
	//v1.2.1
	"ALTER TABLE  `users` ADD  `phone` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `email` ,
		ADD  `salt` VARCHAR( 32 ) NOT NULL COMMENT  'соль для пароля' AFTER  `phone`",
	"UPDATE users SET salt=email",
	"ALTER TABLE  `users` ADD UNIQUE (
		`phone`
	)",
	"ALTER TABLE  `users` ADD UNIQUE (
		`salt`
	)",
	"ALTER TABLE  `users` CHANGE  `email`  `email` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT  'email'",
	"ALTER TABLE  `users` ADD UNIQUE (`hash`)",
	/* *
	//v1.2.3
	"ALTER TABLE `shop_products`
	  DROP `name1`,
	  DROP `text1`,
	  DROP `title1`,
	  DROP `url1`,
	  DROP `keywords1`,
	  DROP `description1`",
	"ALTER TABLE `user_fields` DROP `name1`,  DROP `hint1`",
	"ALTER TABLE `shop_categories`
	  DROP `name1`,
	  DROP `title1`,
	  DROP `url1`,
	  DROP `keywords1`,
	  DROP `description1`",
	"ALTER TABLE `order_deliveries` DROP `name1`, DROP `text1`",
	"ALTER TABLE `order_types` DROP `name1`, DROP `text1`",
	/* */

/*
//v.1.2.21
	"ALTER TABLE  `feedback` ADD  `language` INT UNSIGNED NOT NULL COMMENT  'язык' AFTER  `display`",
*/
/*
//1.2.53
"ALTER TABLE `pages` ADD `noindex` TINYINT(1) NOT NULL COMMENT 'запрет индексации' AFTER `display`",
	"ALTER TABLE `pages` ADD `timestamp` TIMESTAMP NOT NULL COMMENT 'дата изменений' AFTER `noindex`",
	"UPDATE `pages` SET timestamp=now()",
	"ALTER TABLE `news` ADD `timestamp` TIMESTAMP NOT NULL AFTER `date`",
	"UPDATE `news` SET timestamp=now()",
	"ALTER TABLE `shop_products` ADD `timestamp` TIMESTAMP NOT NULL AFTER `date`",
	"UPDATE `shop_products` SET timestamp=now()",
	"ALTER TABLE `shop_categories` ADD `timestamp` TIMESTAMP NOT NULL AFTER `display`",
	"UPDATE `shop_categories` SET timestamp=now()",
	"ALTER TABLE `gallery` ADD `timestamp` TIMESTAMP NOT NULL AFTER `rank`",
	"UPDATE `gallery` SET timestamp=now()",

//v1.2.66 - OAuth2.0
	"CREATE TABLE `user_socials` (
`id` int(11) NOT NULL,
  `date` datetime NOT NULL COMMENT 'дата добавления',
  `user` int(10) UNSIGNED NOT NULL COMMENT 'ИД пользователя',
  `type` tinyint(1) UNSIGNED NOT NULL COMMENT 'вид сети',
  `uid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `gender` tinyint(1) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8",
	"ALTER TABLE `user_socials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_uid` (`type`,`uid`) USING BTREE",
	"ALTER TABLE `user_socials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT",

	//1.2.69
	"ALTER TABLE `user_socials` ADD `last_visit` DATETIME NOT NULL COMMENT 'дата последнего визита' AFTER `date`",

//1.2.70
	"CREATE TABLE `shop_branches` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `display` tinyint(1) unsigned NOT NULL,
	  `rank` int(11) NOT NULL,
	  `lat` varchar(10) NOT NULL,
	  `lng` varchar(10) NOT NULL,
	  `name` varchar(255) NOT NULL,
	  `address` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB"
//1.2.73
	"ALTER TABLE `shop_branches` CHANGE `lat` `lat` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `lng` `lng` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL"

//1.2.94
	"ALTER TABLE `gallery` DROP `keywords`",
    "ALTER TABLE `news`  DROP `keywords`",
	"ALTER TABLE `pages` DROP `keywords`",
	"ALTER TABLE `shop_brands` DROP `keywords`",
	"ALTER TABLE `shop_categories` DROP `keywords`",
	"ALTER TABLE `shop_products` DROP `keywords`",

//1.2.101
	"ALTER TABLE `news` ADD `imgs` TEXT NOT NULL COMMENT 'массив картинок' AFTER `description`, ADD `video` TEXT NOT NULL COMMENT 'ссылки на видео с новой строчки' AFTER `imgs`",
	"ALTER TABLE `pages` ADD `imgs` TEXT NOT NULL COMMENT 'массив картинок' AFTER `description`, ADD `video` TEXT NOT NULL COMMENT 'ссылки на видео с новой строчки' AFTER `imgs`"

//1.2.103
	$query = "SHOW TABLES";
	if ($tables = mysql_select($query,'rows')) {
		$i = 0;
		foreach ($tables as $table) {
			$queries[] = "ALTER TABLE `" . array_shift($table) . "` ENGINE = INNODB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ROW_FORMAT = COMPACT";
		}
	}

//v1.2.109
$query = "SHOW TABLES";
if ($tables = mysql_select($query,'rows')) {
	foreach ($tables as $table) {
		$t = array_shift($table);
		echo $t;
		$query = "ALTER TABLE `".$t."` CHANGE `description` `description` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
		if (mysql_fn('query',$query,'info')) {
			$query = "ALTER TABLE `".$t."` ADD `h1` VARCHAR(255) NOT NULL AFTER `name`";
			mysql_fn('query',$query,'info');
		}
		else echo 'error';
		echo '<br>';
	}
}
*/
/*
//1.2.127
"ALTER TABLE `feedback` ADD `page_name` VARCHAR(255) NOT NULL COMMENT 'название страницы' AFTER `email`, ADD `page_url` VARCHAR(255) NOT NULL COMMENT 'урл страницы' AFTER `page_name`"

//v1.3.11
"ALTER TABLE `news` ADD `hypertext` TEXT NOT NULL AFTER `video`"

//v1.3.13
"ALTER TABLE `letter_templates` ADD `sender_name` VARCHAR(255) NOT NULL AFTER `sender`"

//1.3.37 - created_at,updated_at
$queries = array(
	"ALTER TABLE `gallery` DROP `timestamp`",
	"ALTER TABLE `news` DROP `timestamp`",
	"ALTER TABLE `pages` DROP `timestamp`",
	"ALTER TABLE `shop_products` DROP `timestamp`",
	"ALTER TABLE `shop_categories` DROP `timestamp`",
	"DROP TABLE `push_tokens`"
);
//1.3.37 - created_at,updated_at
$query = "SHOW TABLES";
if ($tables = mysql_select($query,'rows')) {
	$i = 0;
	foreach ($tables as $table) {
		$tbl = array_shift($table);
		$queries[] = "ALTER TABLE `" . $tbl . "` ADD `created_at` DATETIME NOT NULL AFTER `id`,
			ADD `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP() NOT NULL DEFAULT CURRENT_TIMESTAMP() AFTER `created_at`";
		$queries[] = "UPDATE `" . $tbl . "` SET `created_at`='2019-11-13'";
	}
}

//v1.4.24
"ALTER TABLE `orders` DROP `date`"

//v1.4.48
"ALTER TABLE `feedback` DROP `date`",
	"ALTER TABLE users DROP INDEX date",
	"ALTER TABLE `users` DROP `date`",
	"ALTER TABLE users DROP INDEX type",
	"ALTER TABLE `user_socials` DROP `date`"

//v1.4.63
$query = "SHOW TABLES";
if ($tables = mysql_select($query,'rows')) {
	$i = 0;
	foreach ($tables as $table) {
		$t = array_shift($table);
		//mysql_fn('query',"ALTER TABLE `".$t."` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci");
		mysql_fn('query',"ALTER TABLE `".$t."` CONVERT TO CHARACTER SET utf8mb4");
	}
}
function func ($data,$str,$i=0) {
	$count	= strpos($data,$str);
	return substr($data,0,$count+$i);
}

$news = mysql_select("SELECT id,text FROM news","rows");
foreach ($news as $q) {
	$hypertext = array(
		array(
			'type'=>'html',
			'content'=>$q['text']
		)
	);
	$q['hypertext'] = json_encode($hypertext, JSON_UNESCAPED_UNICODE);
	mysql_fn('update','news',$q);

}
//список скл запросов
$queries = array(
	"ALTER TABLE `news`  DROP `text`,  DROP `imgs`,  DROP `video`"
);

$news = mysql_select("SELECT id,text FROM pages","rows");
foreach ($news as $q) {
	$hypertext = array(
		array(
			'type'=>'html',
			'content'=>$q['text']
		)
	);
	$q['hypertext'] = json_encode($hypertext, JSON_UNESCAPED_UNICODE);
	mysql_fn('update','pages',$q);

}
//список скл запросов
$queries = array(
	"ALTER TABLE `pages` ADD `hypertext` MEDIUMINT NOT NULL AFTER `video`",
	"ALTER TABLE `pages` CHANGE `hypertext` `hypertext` MEDIUMTEXT NOT NULL",
	"ALTER TABLE `pages`  DROP `text`,  DROP `imgs`,  DROP `video`",
);

//v1.4.75 убрал уникальные индексы для соли и для хеша
"ALTER TABLE users DROP INDEX salt",
	"ALTER TABLE users DROP INDEX hash"

"ALTER TABLE users DROP INDEX salt",
	"ALTER TABLE users DROP INDEX hash"

*/
