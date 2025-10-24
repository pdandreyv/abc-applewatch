Стандартные действия - массив $_GET['u']<br>

<br>

<a class="label label-danger" data-toggle="collapse" href="#delete">delete</a> - удаление<br>
<div id="delete" class="panel-collapse collapse bg-info">
	Удаляет три вида информации
	<ol>
		<li>Одну картинку</li>
		<li>Много картинок</li>
		<li>Всю строку из базы и прикрепленные файлы к ней</li>
	</ol>
	Настройка удаление через переменную <code>$delete</code>
</div>

<a class="label label-primary" data-toggle="collapse" href="#edit">edit</a> - редактирование формы<br>
<div id="edit" class="panel-collapse collapse bg-info">
	Вносит изменения в базу после отправки формы
</div>

<a class="label label-info" data-toggle="collapse" href="#form">form</a> - загрузка формы<br>
<div id="form" class="panel-collapse collapse bg-info">
	Подгружает на страницу html-код формы
</div>

<a class="label label-default" data-toggle="collapse" href="#nested_sets">nested_sets</a> - перемещение дерева<br>
<div id="form" class="panel-collapse collapse bg-info">
	Перемещает ветки в дереве
</div>

<a class="label label-warning" data-toggle="collapse" href="#post">post</a> - быстрое редактирование<br>
<div id="post" class="panel-collapse collapse bg-info">
	Быстрое редактирование одной ячейки в таблице
</div>

<br><br>

В каждом модуле можно писать исключения для действия
<pre>if($get['u']=='edit') {
	$post['name'] = '123';
}
</pre>
А так же можно делать новые действия
<pre>if ($get['u']=='clear') {
	mysql_fn('query',"TRUNCATE `logs`");
}</pre>

