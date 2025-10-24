Здесь описаны не все таблицы, которые идут в стандартном наборе, а только минимально необходимые для работы сайта
<br>
<a class="label label-danger" data-toggle="collapse" href="#languages">languages</a> - языки<br>
<div id="languages" class="panel-collapse collapse bg-info">
	по умолчанию включен только один язык<br>
	из данной таблицы формируется переменная $lang<br>
<pre>CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ИД',
  `rank` smallint(5) unsigned NOT NULL COMMENT 'Рейтинг',
  `display` tinyint(1) unsigned NOT NULL COMMENT 'Показывать',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'название',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'урл',
  `localization` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'словарь',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Языки' AUTO_INCREMENT=1</pre>
</div>

<a class="label label-default" data-toggle="collapse" href="#letter_templates">letter_templates</a> - шаблоны писем<br>
<div id="letter_templates" class="panel-collapse collapse bg-info">
	данная таблица хранит информацию о шаблонах писем (feedback,registration,remind и т.д.)<br>
<pre>CREATE TABLE IF NOT EXISTS `letter_templates` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sender` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `receiver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1</pre>
</div>

<a class="label label-success" data-toggle="collapse" href="#logs">logs</a> - логи админпанели<br>
<div id="logs" class="panel-collapse collapse bg-info">
	содержат информацию какой пользователь и когда изменял таблицы (insert,update,delete)<br>
<pre>CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ИД',
  `date` datetime NOT NULL COMMENT 'дата создания лога',
  `user` int(10) unsigned NOT NULL COMMENT 'ИД юсера который вносил изменения',
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'модуль в котором внесли изменения',
  `parent` int(10) unsigned NOT NULL COMMENT 'ИД редактируемой записи',
  `type` tinyint(1) unsigned NOT NULL COMMENT 'тип действия (создание, редактирование, удаление))',
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Логи' AUTO_INCREMENT=1</pre>
</div>

<a class="label label-warning" data-toggle="collapse" href="#pages">pages</a> - дерево сайта<br>
<div id="pages" class="panel-collapse collapse bg-info">
	содержат информацию об текстовых страницах и модулях сайта<br>
<pre>CREATE TABLE IF NOT EXISTS `pages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `language` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `parent` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `left_key` int(10) unsigned NOT NULL,
  `right_key` int(10) unsigned NOT NULL,
  `level` smallint(6) DEFAULT '1',
  `display` tinyint(1) NOT NULL,
  `menu` tinyint(1) unsigned NOT NULL,
  `menu2` tinyint(1) unsigned NOT NULL,
  `module` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'pages',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `rank` (`display`),
  KEY `module` (`module`),
  KEY `left_key` (`left_key`),
  KEY `right_key` (`right_key`),
  KEY `level` (`level`),
  KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 AUTO_INCREMENT=1</pre>
</div>

<a class="label label-primary" data-toggle="collapse" href="#users">users</a> - пользователи<br>
<div id="users" class="panel-collapse collapse bg-info">
	содержат информацию о пользователях сайта<br>
	пароль в открытом виде не хранится<br>
	вместо пароля используется поле hash которое генерируется на основании логина, пароля и других данных функцией user_hash
<pre>CREATE TABLE IF NOT EXISTS `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ИД',
  `date` datetime NOT NULL COMMENT 'дата регистрации',
  `remind` datetime NOT NULL,
  `last_visit` datetime NOT NULL COMMENT 'время последней авторизации',
  `remember_me` tinyint(1) unsigned NOT NULL COMMENT 'запомнить меня',
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT 'группа пользователей',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'логин',
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'hash',
  `avatar` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'изображение',
  `fields` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'динамические характеристики',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`email`),
  KEY `type` (`type`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 COMMENT='Пользователи' AUTO_INCREMENT=1</pre>
</div>


<a class="label label-info" data-toggle="collapse" href="#user_types">user_types</a> - роли пользователей<br>
<div id="user_types" class="panel-collapse collapse bg-info">
	содержат информацию о ролях пользователей и прав доступа к разделам админпанели, быстрого редактировани и т.д.<br>
<pre>CREATE TABLE IF NOT EXISTS `user_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_admin` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_delete` tinyint(1) unsigned DEFAULT NULL,
  `access_ftp` tinyint(1) unsigned DEFAULT NULL,
  `access_editable` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `ut_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1</pre>
</div>

<h3>Типовые названия столбцов в таблицах</h3>
<span class="label label-warning">id</span> - обязательная первая колонка в каждой таблице<br>
<span class="label label-danger">rank</span> - рейтинг, используем для сортировки<br>
<span class="label label-warning">date</span> - дата создания записи<br>
<span class="label label-danger">price</span> - цена товара<br>
<span class="label label-info">category</span> - ИД категории shop_categories.id<br>
<span class="label label-success">name</span> - название/имя записи<br>
<span class="label label-info">url</span> - урл страницы, новости, товара и т.д.<br>
<span class="label label-primary">title, description</span> - метатеги в шапке нтмл страницы<br>
<span class="label label-default">text</span> - текстовое поле<br>
<span class="label label-warning">img</span> - основная картинка<br>
<span class="label label-warning">images</span> - много картинок<br>


<h3>Правила создания таблиц</h3>
<ol>
	<li>названия таблицы всегда в множественном числе - pages, letters, shop_products, news</li>
	<li>для таблиц, которые используются в одном модуле, делаем общий префикс - shop_, order_ (префикс пишем в единственном числе)</li>
	<li>для таблиц связей многие ко многим используем название двух таблиц через дефис, например shop_products-categories</li>
</ol>

<h3>Правила создания столбцов</h3>
<ol>
	<li>данные типа int и date идут в начале, а данные типа varchar и text в конце таблицы</li>
	<li>в названиях колонок используем только ключ без названия таблицы<br>правильно: products.category, pages.name<br>не правильно: products.category_id, pages.page_id</li>
	<li>в некоторых случаях, когда у нас много различных связей между таблицами, то в названии колонки можно использовать название другой таблицы
	<br>например у нас есть две таблицы - shop_categories и news_categories и товар shop_products привязан к этим двум таблицам
	<br>в данном случае делаем два поля shop_products.category (привязка к shop_categories, префикс тут можно не ставить так как у них он общий)
	<br>второе поле будет shop_products.news_category - так как имя category занято то используем префикс от связанной таблицы news_</li>
</ol>

<h3>Правила построение SQL запросов</h3>
<ol>
	<li>если мы делаем запрос на две таблицы, то для названий таблиц рекомендуется писать алиас по первым буквам таблицы sp и sc
	</li>
	<li>если мы делаем запрос на две таблицы, у которых есть два одинаковых поля name, то для колонок неосновной таблицы мы прописываем алиас category_url</li>
</ol>
<pre>SELECT sp.*,sc.name category_name,sc.url category_url
FROM shop_products sp
LEFT JOIN shop_categories sc ON sc.id=sp.category</pre>



