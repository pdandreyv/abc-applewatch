Переменные админпанели используемые в настройках админки /admin/config.php
<br>
<br><span class="label label-default">$config['admin_lang']</span> - язык админпанели, по умолчанию <code>ru</code>,
для настройки и расширения списка языков используем папку /admin/language/
<br><span class="label label-default">$config['multilingual']</span> - <a href="?page=admin_multilang">многоязычный сайт</a>
<br><span class="label label-default">$config['depend']</span> - <a href="?page=admin_depend">многие ко многим</a>
<br><span class="label label-default">$config['mirrors']</span> - массив модулей, которые используют другие таблицы а не одноименные, например
<pre>
//зеркальные модули
$config['mirrors'] = array(
	'articles'=>'news',
);</pre>
будет означать что модуль админки /admin/modules/articles.php будет использовать таблицу news а не articles
<br><span class="label label-default">$config['boolean']</span> - массив значений доступный для ключа boolean переменной $table, при расширении значений нужно добавлять
нужные классы в файл стилей, например
<pre>.sprite.market_0 {width:22px; height:22px; background-position:-88px -104px}
.sprite.market_1 {width:22px; height:22px; background-position:-110px -104px}</pre>
<span class="label label-default">$modules_admin</span> - массив всех модулей в админке