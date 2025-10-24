Переменные сайта используются в модулях сайта /modules/***.php
<br>
<br>

<a class="label label-primary" data-toggle="collapse" href="#breadcrumb">$breadcrumb</a> - хлебные крошки<br>
<div id="breadcrumb" class="panel-collapse collapse bg-info">
	массив состоит из двух основных частей
<pre>$breadcrumb = array(
	'page'=>array(); //массив страниц таблицы pages
	'module'=>array(); //массив страниц модуля
);</pre>
	одна часть page или module так же состоит из подмассивов<br>
	1) название страницы<br>
	2) путь к странице
<pre>$breadcrumb['module'][] = array($page['name'],'/'.$modules['news'].'/'.$page['id'].'-'.$page['url'].'/');</pre>
</div>

<a class="label label-danger" data-toggle="collapse" href="#error">$error</a> - включать 404 страницу<br>
<div id="error" class="panel-collapse collapse bg-info">
	если error больше 0 значит будет показана 404 страница
<pre>if ($news = mysql_select("SELECT * FROM news WHERE id = '".$id."' AND display = 1",'row')) {
...
}
else $error++;</pre>
</div>

<a class="label label-default" data-toggle="collapse" href="#config">$config</a> - глобальный массив настроек<br>
<div id="config" class="panel-collapse collapse bg-info">
	в нем находится абсолютно разные настройки (доступа к БД, контактный емейл, настройки кеширования, плагинов и т.д.)<br>
	его так же удобно использовать в шаблонах для хранения промежуточных результатов
</div>

<a class="label label-info" data-toggle="collapse" href="#html">$html</a> - куски нтмл кода, возвращаеміе в модуле<br>
<div id="html" class="panel-collapse collapse bg-info">
	массив $html может состоять из разного количества блоков
<pre>$html = array(
  'content' => 'основной контент',
  'left_col' => 'левая колонка',
  'top_col' => 'верхний блок'
);</pre>
количество элементов $html зависит от архитектуры основного шаблона /templates/includes/template.php<br>
в большинстве случаев достаточно только $html['content'] для вывода основного блока на сайте (текстовая страница, список новостей, список товаров, корзина)<br>
другие элементы добавляются если только верстка этого требует
</div>

<a class="label label-danger" data-toggle="collapse" href="#lang">$lang</a> - массив с данными о языке<br>
<div id="lang" class="panel-collapse collapse bg-info">
	массив $lang содержит все данные о текущем языке<br>
	инициализация
<pre>$lang = lang(1);</pre>
	данные берутся из таблицы languages<br>
	для мультиязычных сайтов инициализация будет по $u[0]
<pre>lang($u[0],'url');</pre>
	так же в .htaccess нужно поправить rewrite
<pre>RewriteRule ^([^/]*)/?([^/]*)/?([^/]*)/?([^/]*)/?$ index.php?u[0]=$1&u[1]=$2&u[2]=$3&u[3]=$4&u[5]=$5 [L,QSA]</pre>
</div>

<a class="label label-success" data-toggle="collapse" href="#modules">$modules</a> - массив модулей module->url<br>
<div id="modules" class="panel-collapse collapse bg-info">
	<pre>$modules = mysql_select("SELECT url name,module id FROM pages WHERE module!='pages' AND language=".$lang['id']." AND display=1",'array')</pre>
	В шаблонах удобно использовать условие
<pre>if ($u[1]==$modules['feadback']) {
	//здесь мы знаем что находимся в обратной связи
}</pre>
	так же можно определять активный пункт меню
<pre>if ($u[1]==$modules['shop'] AND $u[2]==$page['url']) {
	//здесь мы знаем что находимся в каталоге в конкретной категории
}</pre>
</div>

<a class="label label-warning" data-toggle="collapse" href="#page">$page</a> - содержит информацию о странице (name,title,url,description,text и т.д.)<br>
<div id="page" class="panel-collapse collapse bg-info">
	в index.php у нас идет основной запрос
<pre>
$query = "
SELECT *, id AS pid
FROM pages
WHERE display=1 AND language=".$lang['id']." AND url='".mysql_res($u[1])."'
LIMIT 1
</pre>
мосле этого мы знаем какой модуль подгружать
<pre>if ($page = mysql_select($query,'row')) {
	require_once(ROOT_DIR.'modules/'.$page['module'].'.php');
}
</div>

<a class="label label-default" data-toggle="collapse" href="#u">$u</a> - массив вложенного урл<br>
<div id="u" class="panel-collapse collapse bg-info">
	если у нас урл страницы такой /shop/notebooks/acer/aspire/
	то массив $u будет такой
<pre>$u = array(
  1 => 'shop',
  2 => 'notebooks',
  3 => 'acer',
  4 => 'aspire'
);</pre>
	в случае с многоязычным сайтом еще добавляется
<pre>$u[0] = 'ru';</pre>
</div>


<a class="label label-primary" data-toggle="collapse" href="#user">$user</a> - данные пользователя<br>
<div id="user" class="panel-collapse collapse bg-info">
	инициализация переменной
<pre>$user = user('auth')</pre>
	В свою очередь в функции user при успешной авторизации у нас идет запрос
<pre>SELECT ut.*,u.*
FROM users u
LEFT JOIN user_types ut ON u.type = ut.id
WHERE u.email='{email}'
ORDER BY u.id
LIMIT 1</pre>
</div>
