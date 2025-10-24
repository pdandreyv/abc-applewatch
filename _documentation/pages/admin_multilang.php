В CMS реализовано два вида мультиязычности
<br>
<a class="label label-danger" data-toggle="collapse" href="#delete">Независимый</a><br>
<div id="delete" class="panel-collapse collapse bg-info">
	Языки не зависят друг от друга, в таблице есть колонка language в которой указан ИД языка.
	<br>Запись создается для одного языка и в каждом языке может быть разное количество записей.
	<br>Такой способ уже реализован в дереве сайта <code>/admin/modules/pages.php</code>.
	<br>В модуле админки для древовидных модулей нужно обязательно добавить фильтр по языку и скрытое поле языка в форму
<pre>//только если многоязычный сайт
if ($config['multilingual']) {
	$languages = mysql_select("SELECT id,name FROM languages ORDER BY `rank` DESC", 'array');
	$get['language'] = (isset($_REQUEST['language']) && intval($_REQUEST['language'])) ? $_REQUEST['language'] : key($languages);
	if ($get['language'] == 0) $get['language'] = key($languages);
	$query = "
		SELECT pages.*
		FROM pages
		WHERE pages.language = '".$get['language']."'
	";
	$filter[] = array('language', $languages);
	$form[] = '<input name="language" type="hidden" value="'.$get['language'].'" />';
}</pre>
	Для простых модулей (не древовидных) фильтр можно выводить с дефолным значением
	<pre>$filter[] = array('language', $languages,'-языки-');</pre>
	А поле языка добавить селектом в форме
	<pre>$form[] = array('select td3','language',array(true,$languages);</pre>

	<br>На сайте в запросы добавляем ИД языка
	<pre>SELECT * FROM pages WHERE language={$lang['id']}</pre>
	Так же на сайте в модулях и шаблонах где идет выборка по независимым данным добавить к выборке условие
	<pre>AND language=".$lang['id']."</pre>
</div>

<a class="label label-primary" data-toggle="collapse" href="#edit">Зеркальный</a><br>
<div id="edit" class="panel-collapse collapse bg-info">
	Языки зависят друг от друга.
	В таблице создаются копии колонок name2,name3 для разных языков
	(колонки name1 нет, так как по умолчанию основной язык 1 и для него используется колонка без индекса языка).
	<br>При создании нового языка в словаре нужно во все таблицы добавлять нужные колонки с индексами.
	<br>Все настройки можно сделать в одном общем файле /admin/config_multilingual.php
	<br><code>$config['lang_fields']</code> - в данной переменной описываются зеракльные поля для разных модулей админки
	<br><code>$config['lang_tables']</code> - в данной переменной описываются поля, которые будут иметь зеркальные значения в других языках (name -> name2,name3)

	<br>
	<br>В исключительных случаях если через вышеупомянутые переменные нельзя добится нужного результата
	то можно в словаре вот таким образом просписать условия для создания/удаления полей при добавлении/удалении языка
	<pre>if ($get['u']=='edit') {
	if ($config['multilingual']) {
		if ($get['id'] == 'new') {
			$max = mysql_select("SELECT id FROM languages ORDER BY id DESC LIMIT 1",'string');
			$get['id'] = mysql_fn('insert', $get['m'], $post);
			mysql_fn('query',"ALTER TABLE `shop_products` ADD `name".$get['id']."` VARCHAR( 255 ) NOT NULL AFTER `name".$max."`");
			mysql_fn('query',"ALTER TABLE `shop_products` ADD `text".$get['id']."` TEXT NOT NULL AFTER `text".$max."`");
		}
	}
}</pre>
	<br>В исключительных случаях когда нужно нестандартно размещать зеркальные поля то можно добавить исключения в самих модулях
<pre>if ($config['multilingual']) {
	$config['languages'] = mysql_select("SELECT id,name FROM languages ORDEr BY display DESC, `rank` DESC",'rows');
	if ($get['u']=='edit') {
		//перезапись названия в основной язык
		$k = $config['languages'][0]['id'];
		$post['name'.$k] = $post['name'];
		$post['text'.$k] = $post['text'];
	}
	//вкладку с главным языком не показываем
	foreach ($config['languages'] as $k => $v) if ($k>0) {
		//вкладки
		$tabs['1' . $v['id']] = $v['name'];
		//поля
		$form['1' . $v['id']][] = array('input td12', 'name' . $v['id'], @$post['name' . $v['id']], array('name' => $a18n['name']));
		$form['1' . $v['id']][] = array('tinymce td12', 'text' . $v['id'], @$post['text' . $v['id']], array('name' => $a18n['text']));
	}
}</pre>

	<br>На сайте в sql запросы добавлять алиасы
	<pre>SELECT *,name{$lang['id']} as name, text{$lang['id']} as text FROM shop_products</pre>
	Либо внутри шаблонов делаем
	<pre>$q['name'] = $q['name'.$lang['i']];</pre>
</div>

<br>Чтобы многоязычность заработала на сайте, нужно сделать две вещи:
<br>
<br>1.В в файле /_config2.php поставить значение
<br>
<pre>$config['multilingual'] = true;</pre>
<br>2. Раскомментировать строку в .htaccess, чтобы у нас была переменная <code>$u[0]</code>
<pre>RewriteRule ^([^/]*)/?([^/]*)/?([^/]*)/?([^/]*)/?$ index.php?u[0]=$1&u[1]=$2&u[2]=$3&u[3]=$4&u[5]=$5 [L,QSA]</pre>

<div class="bs-callout bs-callout-danger">
За все урл на сайте отвечает функция get_url которая находится в основном файле настроек /_config2.php
</div>

<br>3. в файле /admin/config_multilingual.php настроить все зеркальные данные (name,name2...)
<br>На сайте в шаблонах там где будет выводится контент из зеркальных данных для зеркальных полей добавить код
<pre>$q['name'] = $q['name'.$lang['i']];</pre>
На сайте в модулях и шаблонах где идет выборка по независимым данным добавить к выборке условие
<pre>AND language=".$lang['id']."</pre>

