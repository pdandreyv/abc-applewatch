<br>Рассмотрим на конкретном примере.
<br>Нам нужно сделать чтобы один товар был привязан одновременно к нескольким категориям.
<br>Таблица товаров <span class="label label-primary">shop_products</span>
с полями <span class="label label-primary">id</span> и <span class="label label-info">categories</span> (тут будут хранится ID категорий через запятую)
<br>Еще у нас есть таблица категорий <span class="label label-success">shop_categories</span> с полем <span class="label label-success">id</span>
<br>Создаем таблицу связей <span class="label label-warning">shop_products-categories</span> с полями
<br>- <span class="label label-warning">id</span> - ИД связи
<br>- <span class="label label-primary">child</span> - равно shop_products.id
<br>- <span class="label label-success">parent</span> - равно shop_categories.id
<br>

<br>в файле модуля товаров админки добавляем мультивыбор категорий
<br><pre>$form[] = array('multicheckbox td3','categories',array(true,'SELECT id,name,level FROM shop_categories ORDER BY left_key'));</pre>
<br>в /admin/config.php инициализировать переменную
<pre>
$config['depend'] = array(
  'shop_products'=>array('categories'=>'shop_products-categories'),
);
</pre>
где
<br>- <span class="label label-primary">shop_products</span> - таблица товаров
<br>- <span class="label label-info">categories</span> - поле shop_products.categories для хранения ИД категорий через запятую
<br>- <span class="label label-warning">shop_products-categories</span> - таблица связей


