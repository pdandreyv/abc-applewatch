Описываем таблицу
<pre>
$table = array(
	'id'		=>	'date:desc name url user title id',
	'name'		=>	'',
	'category'	=>	'text',
	'url'		=>	'',
	'price'		=>	'right',
	'date'		=>	'date',
	'display'	=>	'boolean'
);
</pre>

Дополнительное действие описанное в самом модуле
<pre>if (isset($_GET['u']) && $_GET['u']=='clear') mysql_fn('query',"TRUNCATE `logs`");</pre>

Фильтр поиска
<pre>$filter = array('category','shop_categories','-категории-');</pre>

Описываем SQL запрос c переменной $where
<pre>
$query = "
	SELECT shop_products.*,sc.name category
	FROM shop_products
	LEFT JOIN shop_categories sc ON sc.id = shop_products.category
	WHERE 1 $where
";
</pre>

Описываем форму
<pre>
$form[] = array('input td7','name',true);
$form[] = array('select td3','category',array('category','shop_categories','');
$form[] = array('input td3','date',true);
$form[] = array('checkbox','display',true);
$form[] = array('tinymce td12','text',true);
$form[] = array('seo','seo url title description',true);
</pre>

Обработка входящих $_GET параметров - формирование $where
<pre>$where = '';
if (@$_$GET['category']>0) $where.= " AND shop_products.category=".intval($_GET['category']);</pre>

HTML код над таблицей
<pre>$content = '<?=htmlspecialchars('
<div style="float:right; padding:7px 0 0">
<a href="?m=shop_products&u=clear" onclick="if(confirm(\'Подтвердите\')) {} else return false;">Очистить</a>
</div>')?>';</pre>