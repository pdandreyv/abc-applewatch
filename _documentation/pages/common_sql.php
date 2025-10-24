Основная функция для выполнения select запросов - mysql_select()<br>
<pre>$data = mysql_select("SELECT * FROM news WHERE id=1",'row',3600);</pre>
<ol>
<li>sql запрос</li>
<li>тип возвращаемых данных<br>

<a class="label label-success" data-toggle="collapse" href="#string">string</a> - строка<br>
<div id="string" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT name FROM news WHERE id=1",'string')</pre>
	Вернет только значение одного поля name
	<pre>название новости</pre>
</div>

<a class="label label-default" data-toggle="collapse" href="#num_rows">num_rows</a> - количество записей<br>
<div id="num_rows" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT id FROM news ",'num_rows')</pre>
	Вернет количество записей
	<pre>12</pre>
</div>

<a class="label label-warning" data-toggle="collapse" href="#row">row</a> - один ряд<br>
<div id="row" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT * FROM news WHERE id=1 LIMIT 1",'row')</pre>
	Вернет ряд с одной записью
<pre>array(
	'id'=>'12',
	'name'=>'Название',
	'text'=>'текст'
)</pre>
</div>

<a class="label label-primary" data-toggle="collapse" href="#rows">rows</a> - массив из row<br>
<div id="rows" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT * FROM news LIMIT 10",'rows')</pre>
	Вернет все выбранные ряды
<pre>array(
	array(
		'id'=>'12',
		'name'=>'Название',
		'text'=>'текст'
	),
	array(
		'id'=>'14',
		'name'=>'Название',
		'text'=>'текст'
	),
	array(
		'id'=>'17',
		'name'=>'Название',
		'text'=>'текст'
	)
)</pre>
</div>

<a class="label label-info" data-toggle="collapse" href="#rows_id">rows_id</a> массив из row где ключом будет id<br>
<div id="rows_id" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT id FROM news",'rows_id')</pre>
	Вернет все выбранные ряды
<pre>array(
	12=>array(
		'id'=>'12',
		'name'=>'Название',
		'text'=>'текст'
	),
	14=>array(
		'id'=>'14',
		'name'=>'Название',
		'text'=>'текст'
	),
	17=>array(
		'id'=>'17',
		'name'=>'Название',
		'text'=>'текст'
	)
)</pre>
</div>

<a class="label label-danger" data-toggle="collapse" href="#array">array</a> - массив $k->$v - SELECT id,name .. FROM LIMIT 1 => array(1=>'значение',2=>'значение')<br>
<div id="array" class="panel-collapse collapse bg-info">
	<pre>$data = mysql_select("SELECT id,name FROM news",'array')</pre>
	Вернет простой массив
<pre>array(
	12=>'Название',
	14=>'Название',
	17=>'Название',
)</pre>
</div>
</li>
<li>время кеширования в секундах<br>
Если пусто или 0 то кеш не создается и запрос делается в базу<br>
Если значение больше 0 то текст запроса оборачивается в md5() и функция смотрит в папку cache есть ли там такой файл<br>
Если файл есть, то смотрит время создания файла<br>
Если время создания меньше времени кеша, то запрос не делается а используется файл кеша<br>
Если время создания больше времени кеша, то делается запрос в базу а файл кеша обновляется
<div class="bs-callout bs-callout-danger">
Кешировать нужно только самые частые и сложные запросы и только если начало расти количество посетителей
</div>
	</li>
</ol>


Для выполнения всех других запросов используем функцию - mysql_fn()<br>
Примеры:
<ol>
	<li>INSERT
		<pre>mysql_fn('insert','shop_products',$post);</pre>
		Все значения массива $post будут добавлены в таблицу shop_products
	</li>
	<li>UPDATE
		<pre>mysql_fn('update','shop_products',$post);</pre>
		Все значения массива $post будут обновлены в таблице shop_products
		<div class="bs-callout bs-callout-danger">
			Обязательно должно присутсвовать значение $post['id'], именно по нему идет условие обновления
		</div>
		Если нужно делать обновление с условием, то делаем так
		<pre>mysql_fn('update','shop_products',$post,' AND type=1');</pre>
		В таком случае параметр $post['id'] не обязательный
	</li>
	<li>DELETE
		<pre>mysql_fn('delete','shop_products',$post);</pre>
		$post либо число либо массив из которого будет использован только $post['id'] - ИД записи для удаления
		<br>Если удалять нужно с условием, то делаем так
		<pre>mysql_fn('delete','shop_products',false," AND type=1");</pre>
		В таком случае третий параметр с ИД записи не обязателен
		<div class="bs-callout bs-callout-danger">
			Нельзя удалить все записи из таблицы, запрос удаления без условий не будет выполнен
		</div>
	</li>
	<li>QUERY (v.1.1.3)
		<br>Лююбой запрос который не получается собрать через конструктор можно выполнить так
		<pre>mysql_fn('query','REPAIR TABLE shop_products');</pre>
		Если функция должна вернуть количество затронутых записей то нужно вызвать с параметром
		<pre>mysql_fn('query','DELETE FROM shop_products','affected_rows');</pre>
	</li>
</ol>