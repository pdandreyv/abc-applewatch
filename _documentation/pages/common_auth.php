Авторизация делается с помощью функции user();
<pre>session_start();
$user = user('auth');</pre>
Варианты авторизации:<br>
<a class="label label-danger" data-toggle="collapse" href="#enter">enter</a> - вход через форму авторизации<br>
<div id="enter" class="panel-collapse collapse bg-info">
<pre>if(@$_POST['email'] AND @$_POST['password']) {
	$user = user('enter');
}</pre>
	Инициализация массива $user при отправке формы авторизации<br>
	должно быть отправлено два значения email и password<br>
	рекомендуется использовать captcha2 против роботов
</div>

<a class="label label-warning" data-toggle="collapse" href="#remind">remind</a> - вход через урл<br>
<div id="remind" class="panel-collapse collapse bg-info">
<pre>if(@$_GET['email']) AND @$_GET['hash']) {
	$user = user('remind');
}</pre>
	Инициализация массива $user при авторириазции по ссылке<br>
	должно быть отправлено два значения email и hash
</div>

<a class="label label-primary" data-toggle="collapse" href="#auth">auth</a> - авторизация по сессии или кукам<br>
<div id="auth" class="panel-collapse collapse bg-info">
	<pre>$user = user('auth')</pre>
	Инициализация массива $user, в нем будут все данных пользователя<br>
	Авторизация происходит либо по сессии либо по кукам
</div>

<a class="label label-info" data-toggle="collapse" href="#re-auth">re-auth</a> - переавторизация для обновления данных текущей сессии<br>
<div id="re-auth" class="panel-collapse collapse bg-info">
	<pre>$user = user('re-auth')</pre>
	Переиниализация $user, делается для обновления данных сессии<br>
	Например, если у нас была вызвана сложная функция оплаты, которая могла изменить количество денег на счету $user, то нужно сделать переавторизацию
</div>

<a class="label label-success" data-toggle="collapse" href="#update">update</a> - обновление данных в базе и в текущей сессии<br>
<div id="update" class="panel-collapse collapse bg-info">
<pre>$user['param_1'] = 1;
$user['param_2'] = 1;
$user = user('update','param_1 param_2')</pre>
	Обновление данных пользователя в базе
</div>

<h2>Права доступа</h2>
Для определения прав доступа используем функцию access()<br>
Права доступа разделяем по группам, например:
<ul>
	<li>Проверка авторизации для свершения операции
<pre>if (access('user auth')) {
..
}</pre>
	</li>
	<li>Проверка есть ли у пользователя доступ к товарам в админке
<pre>if (access('admin shop_products')) {
..
}</pre>
	</li>
	<li>Есть ли право на просмотр конкретного заказа, где $order - массив с данными заказа
<pre>if (access('order veiw',$order)) {
..
}</pre>
	</li>

</ul>
