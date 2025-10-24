Шаблон - это страница с нтмл кодом, куда подставляются нужные значения и на выходе имеем данные из базы данных обернутые в нтмл.<br>
Все шаблоны находятся в папке /templates/includes/.<br>
Вообще весь нтмл код сайта находится только в шаблонах.<br>
<br>
Для работы с шаблонами у нас есть две функии html_array() и html_render
<br>html_query() считается устарелой<br>
Шаблоны подключаются в модулях<br>
<pre>$html['content'] = html_array('news/text',$page);</pre>
Так же шаблоны могут подключатся и в других шаблонах
<pre>&lt;div id="header">
	&lt;?=html_array('order/basket_info')?>
	&lt;?=html_array('profile/login_form')?>
&lt;/div></pre>

<h3>Логика размещения шаблонов и использования в них css классов</h3>
<ol>
	<li>шаблоны группируем по папкам по названию модуля, так все шаблоны модуля shop будут находится в /templates/includes/shop/</li>
	<li>если шаблон отвечает за вывод списка записи, то мы его называем list, например /templates/includes/news/list.php</li>
	<li>если шаблон отвечает за вывод страницы записи, то мы его называем text, например /templates/includes/news/text.php</li>
	<li>основной класс для шаблона мы берем из его пути, например:<br>
		/templates/includes/news/text.php - .news_text<br>
		/templates/includes/shop/product_list.php - .shop_product_list</li>
	<li>в css классы шаблонов пишем а алфавитном порядке</li>
</ol>

<h3>Пример использования html_array()</h3>
Функция html_array принимает два параметра - путь к шаблону и массив данных<br>
В примере будет подключен шаблон /templates/includes/<strong>news/text</strong>.php и в него передан массив с данными одной новости
<pre>$page = mysql_select("SELECT * FROM news WHERE id=1",'row');
$html['content'] = html_array('news/text',$page);</pre>

<h3>Пример использования html_render()</h3>
Функция html_render принимает два параметра - путь к шаблону и массив данных<br>
В примере будет подключен шаблон /templates/includes/<strong>news/text</strong>.php и в него передан массив с данными одной новости
<pre>$abc['news'] = mysql_select("SELECT * FROM news",'rows');
...
echo html_render('news/text',$abc['news']);</pre>
<br>
Пример использования с пагинатором
<pre>$abc['news'] = mysql_data(
	"SELECT * FROM news WHERE display = 1 ORDER BY date DESC",
	false,
	10,
	@$_GET['n']
);
...
echo html_render('pagination/data',$abc['news']);
echo html_render('news/list',$abc['news']['list']);
echo html_render('pagination/data',$abc['news']);</pre>

<h3>Пример использования html_query()</h3>
Функция устарела<br>
Функция html_query принимает два параметра - путь к шаблону и запрос к базе<br>
В примере будет подключен шаблон /templates/includes/<strong>news/list</strong>.php и в него передан массив с данными с несколькими новостями<br>
Это было бы тоже самое если бы мы через foreach пропустили функцию html_array для каждой новости
<pre>$html['content'] = html_query('news/list',"SELECT * FROM news");</pre>

<h3>Пример шаблона</h3>
в шаблоне доступны все глобальные переменные $config,$lang,$pages,$modules,$html<br>
Так же там есть три внутренних переменных<br>
$q - массив данных<br>
$q['_*'] - переменные которые начинаются на нижнее подчеркивание формируются в колбекфункциях<br>
$i - номер по порядку (только в случае с html_render())<br>
$num_rows - количество результатов в выборке (только в случае с html_render())<br>
<pre>&lt;div class="news_list">
	&lt;div class="date">&lt;?=$q['_date']?>&lt;/div>
	&lt;div class="name">
		&lt;a href="&lt;?=$q['_url']?>">&lt;?=$q['name']?>&lt;/a>
	&lt;/div>
	&lt;div class="text">
		&lt;?=$q['text']?>
	&lt;/div>
	&lt;div class="next">
		&lt;a href="&lt;?=$q['_url']?>">&lt;?=i18n('common|wrd_more')?>&lt;/a>
	&lt;/div>
&lt;/div></pre>
используя сравнение переменных $i и $num_rows мы можем:<br>
1) оборачивать всю выборку в обертку - if ($i==1) и if ($i==$num_rows)<br>
<pre>&lt;?php if ($i==1) {?>
&lt;div id="news">
&lt;?php } ?>
	&lt;div class="news_list">
	...
	&lt;/div>
&lt;?php if ($i==$num_rows) {?>
&lt;/div>
&lt;?php } ?>	
&lt;/div></pre>
на выходе в нас будет
<pre>&lt;div id="news">
	&lt;div class="news_list">
	...
	&lt;/div>
	&lt;div class="news_list">
	...
	&lt;/div>
	&lt;div class="news_list">
	...
	&lt;/div>
&lt;/div></pre>
2) добавлять нужный нтмл код (или класс для блока) в определенном месте (например, каждый третий if (fmod($i,3)==0)<br>
<pre>&lt;div class="shop_product_list">
	..
&lt;/div>
&lt;?php if (fmod($i,3)==0) {?>
&lt;div class="line">&lt;/div>	
&lt;?php } ?>
</pre>
на выходе в нас будет
<pre>&lt;div class="shop_product_list">
	...
&lt;/div>
&lt;div class="shop_product_list">
	...
&lt;/div>
&lt;div class="shop_product_list">
	...
&lt;/div>
&lt;div class="line">&lt;div>
&lt;div class="shop_product_list">
	...
&lt;/div>
&lt;div class="shop_product_list">
	...
&lt;/div>
&lt;div class="shop_product_list">
	...
&lt;/div>
&lt;div class="line">&lt;div>
</pre>
