В базе данных за создание лендинга отвечает только две таблицы
<br>- <span class="label label-danger">landing</span> - одна запись это один экран, по аналогии с pages где каждая страница может быть каким-то модулем
запись в таблице landing привязана к типу шаблона template
<br>- <span class="label label-warning">landing_items</span> - элементы внутри одного экрана лендинга (тизеры, блоки, фото и т.д.)
<br>
<br>Для включения модулей лендинга в админпанели нужно:
<br>
<br>1. в файле <code>/admin/config.php</code> расскоментировать/добавить строки
<pre>
$modules_admin =>array(
	..
	'gallery' => array(
		'landing'   => 'landing',
		'о нас'     => 'landing_items1',
		'услуги'    =>'landing_items2',
		'стоимости работ' =>'landing_items3'
	),
	...
);</pre>
а так же добавить настройку чтобы разные модули админки работали только с одной таблицей landing_items
<pre>
//зеркальные модули
$config['mirrors'] = array(
	...
	'landing_items1'=>'landing_items',
	'landing_items2'=>'landing_items',
	'landing_items3'=>'landing_items',
);</pre>
Таким образом для каждого экрана лендинга если внутри его есть дополнительные элементы (тизеры, блоки, фото) мы делаем в админке раздле для настройки этих элементов.
<br>
<br>2. В файле <code>/admin/modules/landing.php</code> описать все виды экранов
<pre>//типы страниц лендинга (экраны)
$templates = array(
	1	=> 'О нас',
	2	=> 'Услуги',
	3	=> 'Стоимость работ',
);</pre>
Настроить нужно количество разделов админки <code>/admin/modules/landing_items*.php</code>
<br>
<br>3. В файле <code>/modules/index.php</code> расскоментрировать/добавить код чтобы выводились старницы лендинга
<pre>
//лендинг
$query = "
	SELECT *
	FROM landing
	WHERE display = 1
	ORDER BY `rank` DESC
";
$html['content'] = html_query ('landing/landing',$query);
</pre>

<br>4. В папке с шаблонами лендингов <code>/templates/includes/landing/</code> настроить отбражения каждого экрана лендинга <code>landing*.php</code> (п.2)