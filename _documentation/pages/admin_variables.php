Переменные админпанели используются в модулях админки /admin/modules/***.php
<br>
<br>

<a class="label label-default" data-toggle="collapse" href="#a_content">$content</a> - нтмл код над таблицей ($table)<br>
<div id="a_content" class="panel-collapse collapse bg-info">
	В данной переменной можно писать любой нтмл код, css и javascript
</div>

<a class="label label-danger" data-toggle="collapse" href="#a_delete">$delete</a> - настройки удаления записей<br>
<div id="a_delete" class="panel-collapse collapse bg-info">
	чтобы запись можно было всегда удалять эту переменную вообще не нужно инициализировать<br>
	Переменная состоит из двух массивов<br>
	- confirm - условия при которых невозможно будет удалить запись<br>
	- delete - правила при которых будут удаляться другие связанные записи<br>
	Примеры confirm:<br>
	1) удалит только если запрос <kbd>SELECT * FROM product WHERE category = $get['id']</kbd> вернет пустой результат
	<pre>$delete = array('product'=>'category');</pre>
	2) удалить можно будет только если запрос выдаст пустой результат
	<pre>$delete = array('shop_category'=>"SELECT id FROM product WHERE category = ".$get['id']);</pre>
	3) для нескольких проверочный запросов нужно писать так
<pre>$delete = array(
  'product' => 'category',
  'shop_category' => "SELECT id FROM product WHERE category = ".$get['id']
);</pre>

</div>

<a class="label label-primary" data-toggle="collapse" href="#a_filter">$filter</a> - фильтр поиска<br>
<div id="a_filter" class="panel-collapse collapse bg-info">
	1) пример с древовидной таблицей
<pre>$filter[] = array(
  'shop_category',	// ключ - $_GET['shop_category'] = $q['id']
  'shop_categories',	// модуль из которого построится дерево
  '-категории-'		// значение по умолчанию option value="0" -категории- /option
);</pre>
	2) пример с обычной таблицей
<pre>$filter[] = array(
  'shop_brand',		// ключ - $_GET['shop_brand'] = $q['id']
  "SELECT sb.id,sb.name FROM shop_brands sb ORDER BY sb.name", //запрос для формирования option value="{id}" {name} /option
);</pre>
	3) пример с массивом
<pre>$filter[] = array(
  'type',		// ключ - $_GET['shop_brand'] = $q['id']
  array(1=>'синий',2=>'красный'), //массив для формирования  option value="{key}" {value} /option
);</pre>
	4) 4-й параметр очищает урл если true (по умолчанию false и урл не будет очищаться, то есть фильтры будут дополнять друг на друга)
	<pre>$filter[] = array('shop_category','shop_categories','-категории-',true);</pre>
	5) поиск
	<pre>$filter[] = array('search');</pre>
</div>

<a class="label label-info" data-toggle="collapse" href="#a_form">$form</a> - форма редактирования записи<br>
<div id="a_form" class="panel-collapse collapse bg-info">
	в $form можно записывать либо строку с кодом либо массив с данными
	строка - html код
	<pre>$form[] = 'сюда можно писать любой html-код';</pre>
	если строка clear то будет добавлен блок для очистки ряда
	<pre>$form[] = 'clear'; => &lt;div class="clear"&gt;&lt;/div&gt;</pre>
	массив - данные будут обработаны функцией form()
	<pre>$form[] = array('input td7','name',true);</pre>
	примеры:<br>
	1) input
	<pre>$form[] = array('input td7','about',true);</pre>
	td7 - означает что инпут будет занимать 7/12 от всей ширины (12 это 100% ширины)<br>
	name - атрибут name="about"<br>
	true - означает что в него автоматически подставится значение $post['about'] - данные из нужной ячейки таблицы<br>
	название поля будет слово со словаря с ключом about
	<br>
	1.a) input c указанным значением, именем, атрибутом и подсказкой
<pre>$form[] = array(
  'input td7',
  'about',
  'данные',
  array(
    'name'=>'название поля',
    'attr'=>'id="about"',
    'help'=>'подсказка возле названия'
  )
);</pre>
	2) чекбокс - для чекбокса ширину td* можно не указывать
	<pre>$form[] = array('checkbox','display',true);</pre>
	2.a) чекбокс в одну строчку
	<pre>$form[] = array('checkbox line','display',true);</pre>
	3) select - 3-й параметр массив для формирования option, синтаксис аналогичен функции select
	<pre>$form[] = array('select td3','brand',array(true,"SELECT id,name FROM shop_brands ORDER BY name"));</pre>
	4) textarea c заданной высотой
	<pre>$form[] = array('textarea td12','text',true),array('attr'=>'style="height:500px"'));</pre>
	5) множественный чекбокс уплывающий вправо высотой 4 пункта
<pre>$tags = mysql_select("SELECT id,name FROM quest_tags",'rows_id');
$form[] = array('multicheckbox td4 f_right tr4','tags',array(true,$tags),array('name'=>'теги'));</pre>
	6) tinymce
	<pre>$form[] = array('tinymce td12','text',true);</pre>
	7) seo поля - 2-й параметр это перечисление всех полей
	<pre>$form[] = array('seo','seo url title description',true);</pre>
	8) parent - поле родителя и сортировка в древовидных таблицах, задаем ширину сразу двух колонок
	<pre>$form[] = array('parent td3 td4','parent',true);</pre>

	<strong>ЗАГРУЗКА ФАЙЛОВ</strong><br>
	1) один файл<br>
	img - колонка в таблице<br>
	array('m-'=>'resize 1000x1000 watermark.png 2') - создается файл /files/{модуль}/{id}/img/m-{название файла} размером 1000x1000 способом resize<br>
	способы масштабирования картинки<br>
	- resize - пропорциональное уменьшение, аналог background-size:contain<br>
	- cut - уменьшение с обрезанием лишнего, аналог background-size:cover<br>
	водяной знак<br>
	- watermark.png - наложение водяного знака /templates/images/watermark.png<br>
	- 2 - положение знака - 1|2|3|4|5 - по углам и в центре<br>
	<pre>$form[] = array(
	'file td6', //тип поля и расположение
	'img', //колонка в таблице
	'Основная картинка', //название блока
	array(''=>'resize 1000x1000','p-'=>'resize 150x150'), //размеры картинки
);</pre>
	2) много файлов - imgs колонка в таблице
	<pre>$form[] = array(
	//тип поля
	'file_multi',
	//колокнка в таблице
	'imgs',
	//название блока
	'Дополнительные картинки',
	//размеры картинок
	array(
		''=>'resize 1000x1000',//основная картинка
		'p-'=>'resize 150x150' //превью
	),
	//дополнительные параметры для картинок
	array(
		'name'=>'input', //инпут
		'type'=>array(1=>'1 тип',2=>'2 тип'), //селект v1.2.25
		'display'=>'checkbox') //чекбокс
	);
);</pre>
	данные хранятся в массиве serialize, путь к картинке /files/{модуль}/{id}/imgs/{ключ}/m-{file}
<pre>array(
	1=>array( //основной ключ
		'name'=>'весна', //название картинки
		'file'=>'spring.php' //название файла
		'display'=>1 //отображать
	)
)</pre>
	пример работы с массивом
<pre>$images = $q['imgs'] ? unserialize($q['imgs']) : false;
if (images) foreach ($images as $k=>$v) if (@$v['display']==1) {
	$title = filter_var($v['name'],FILTER_SANITIZE_STRING);
	$path = '/files/shop_products/123/imgs/'.$k.'/'.$v['file'];
}
</pre>
	3) много файлов - сохраняются в отдельной таблице
	<pre>$form[] = array('file_multi_db','shop_items','Дополнительные картинки',array(''=>'resize 1000x1000','p-'=>'resize 150x150'));</pre>
	данные хранятся в в другой таблице shop_items, путь к картинке /files/shop_items/{id}/img/{file}
	<br>вместо shop_items может быть написана любая другая таблица
	<br>обязательные поля в таблице
	<br>n - сортировка
	<br>parent - ИД родителя
	<br>img - ключ для картинки

</div>

<a class="label label-success" data-toggle="collapse" href="#a_query">$query</a> - SQL-запрос для отображения таблицы ($table)<br>
<div id="a_query" class="panel-collapse collapse bg-info">
	запрос нужно описывать только если он делается с объединением нескольких таблиц или есть фильтр<br>
	в запросе нельзя использовать алиасы для основной таблицы, а только реальные имена таблиц<br>
	так же обязательно нужно указать условие WHERE, если его не написать то будет ошибка
<pre>$where = (isset($get['type']) && $get['type']>0) ? "AND users.type = '".$get['type']."' " : "";
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(users.email) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(users.fields) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";
$query = "
	SELECT users.*
	FROM users
	WHERE 1 ".$where;
";</pre>


</div>


<a class="label label-danger" data-toggle="collapse" href="#a_module">$module</a> - настройки модуля<br>
<div id="a_module" class="panel-collapse collapse bg-info">
<pre>$module = array(
	...
);</pre>
	<ul>
		<li><code>'table'		=>'shop_products'</code> устанавливается автоматом в admin.php перед вызовом модуля благодаря переменной $config['mirrors'] в файле настроек админки,
			ключами выступают модули дубликаты, которые используют существующую таблицу и папку в files, сама таблица указывается значением для ключа.
		<br>Например, <kbd>$config['mirrors']['articles'] = 'news';</kbd> в комбинации с <kbd>$modules_admin['articles'] = 'articles';</kbd>
			посзволяют работать с новым файлом модуля админки <kbd>/admin/modules/articles.php</kbd> который будет использовать таблицу <kbd>news</kbd> и сохранять файлы в папку <kbd>/files/news/</kbd></li>
		<li><code>'save_as'		=>false</code> показать или скрыть кнопку сохранить как, по умолчанию не показыватся</li>
		<li><code>'one_form'	=>false</code> скрывает ссылку закрытия формы и кнопку сохранить и закрыть, используется для модулей где только одна форма, например настройки или словарь</li>
	</ul>
</div>



<a class="label label-warning" data-toggle="collapse" href="#a_table">$table</a> - настройка отображения колонок<br>
<div id="a_table" class="panel-collapse collapse bg-info">
<pre>$table = array(
	...
);</pre>
	<ul>
		<li><code>'_check'		=>array()</code> настройка чекбоксов и операций с записями таблицы
			<br>внизу таблицы возможны два варианта вызова действия с отмеченными записями
			<br> - в виде кнопок
			<code>'buttons'=>array('display_on' => 'Включить')</code>
			<br> - в виде селекта с вариантами и кнопки применить
			<code>'select'=>array('move' => 'Переместить')</code>
		</li>
		<li><code>'_tree'		=>true</code> только так будет выводится древовидная таблица</li>
		<li><code>'_edit'		=>true|false|edit|add</code> можно/нельзя добавлять и редактировать | только редактировать | только добавлять</li>
		<li><code>'_delete'	=></code> нет ссылки удалить</li>
		<li><code>'id'		=>'rank:desc id'</code> поля по которым идет сортировка, по умолчанию сортировка по rank (первый в списке)</li>
		<li><code>'name'		=>''</code> поле быстрого редактирования</li>
		<li><code>'name'      =>'::order_link'</code> поле, значение которого вернет пользовательская функция order_link, описанная в модуле</li>
		<li><code>'name'		=>'<?=htmlspecialchars('<a target="blank" href="/module/{id}/">{name}</a>')?>'</code> шаблон {name} будет заменено на $q['name']</li>
		<li><code>'price'		=>'right'</code> выравнивание по правому краю + редактирование</li>
		<li><code>'type'		=>'boolean'</code> да/нет класс .type будет использован как спрайт картинка, если такой нет то по умолчанию крестик и галочка</li>
		<li><code>'login'		=>'text'</code> просто текст без редактирования</li>
		<li><code>'date'		=>'date'</code> календарь (а разработке)</li>
		<li><code>'type'		=>$config['type']</code> подставление значения из любого массива, два варианта массивов:
			<br> - простой array(1=>'название')
			<br> - древовидный array(1=>array('level'=1,'name'=>'название'))
		</li>
		<li><code>'brand:'		=>$brand</code> аналогично предыдущему, но будет селект для быстрого редактирования</li>
		<li><code>'img'		=>	'img'</code> картинка</li>
	</ul>
</div>

<a class="label label-info" data-toggle="collapse" href="#a_tabs">$tabs</a> - вкладки для формы ($form)<br>
<div id="a_tabs" class="panel-collapse collapse bg-info">
	если инициализирована переменная $tabs то форма будет состоять из вкладок
<pre>$tabs = array(
  1=>'Основная вкладка',
  2=>'Сеополя',
  3=>'Загрузка файлов',
);</pre>
	но нужно формы вызывать с дополнительными индексами - ключами вкладки
<pre>$form[0][] = array();
$form[1][] = array();
$form[2][] = array();</pre>

</div>

<a class="label label-info" data-toggle="collapse" href="#modules_admin">$modules_admin</a> - настрйока меню админки<br>
<div id="modules_admin" class="panel-collapse collapse bg-info">
<pre>$modules_admin = array(
	...
);</pre>
	<ul>
		<li><code>'module'		=> string OR array</code> урл модуля или массив вложенных модулей
		</li>
		<li><code>'name'		=>'news'</code> по умолчанию берется значение из module, будет использована функция a18n</li>
		<li><code>'image'		=>'news'</code> картинка (класс спрайта) для старого дизайна</li>
		<li><code>'icon'	=></code> иконка нового дизайна</li>
	</ul>
</div>