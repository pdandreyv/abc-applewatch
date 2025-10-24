Обработку данных формы можно начинать когда у нас есть массив $_POST
<pre xmlns="http://www.w3.org/1999/html">if (count($_POST)>0) {
  ..
}</pre>

Для работы с формами нужно подключить библиотеку
<pre>require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами</pre>

Дальше определить маску значений (массив), тип данных и обязательные поля
<pre>//определение значений формы
$fields = array(
  'email'		=>	'required email',
  'name'		=>	'required text',
  'text'		=>	'required text',
  'captcha'		=>	'required captcha2'
);</pre>

Пропускаем массив данных из формой через функции - в массиве $post будут данные на основе маски $fields
<pre>//создание массива $post
$post = form_smart($fields,stripslashes_smart($_POST)););
</pre>

<strong>Допустимые значения для маски form_smart()</strong><br>
<span class="label label-warning">text</span> - текст без html тегов<br>
<span class="label label-default">html</span> - html код<br>
<span class="label label-primary">int</span> - целое не отрицательное число<br>
<span class="label label-primary">ceil</span> - целое число<br>
<span class="label label-primary">decimal</span> - дробное число 12.02<br>
<span class="label label-danger">date</span> - дата<br>
<span class="label label-success">email</span> - email<br>
<span class="label label-primary">boolean</span> - 1 или 0<br>
<span class="label label-info">string_int</span> - числа через запятую 1,2,3<br>
<span class="label label-danger">min_max</span> - два числа через дефис - 12-56<br>
<br>

Также можем пропустить массив данных формы и маску значений для определения валидности полей
<pre>$message = form_validate($fields,$post);</pre>

<strong>Допустимые значения для проверки валидации form_validate()</strong><br>
<span class="label label-danger">required</span> - обязательное поле<br>
<span class="label label-warning">login</span> - [-A-Za-z0-9_]<br>
<span class="label label-success">email</span> - валидный емейл<br>
<span class="label label-default">password</span> - валидный пароль - больше 5 символов<br>
<span class="label label-primary">captcha</span> - видимая каптча<br>
<span class="label label-info">captcha2</span> - скрытая каптча<br>

<div class="bs-callout bs-callout-danger">
	Значения валидации должны стоять впереди значений маски
	<pre>'name'		=>	'required text',</pre>
</div>

В массиве $message у нас будут сообщения с ошибками заполнения формы, если он пустой значит ошибок нет
<pre>if (count($message)==0) {
	..
}</pre>