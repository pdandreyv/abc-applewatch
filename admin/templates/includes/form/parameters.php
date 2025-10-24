<?php
$parameters = isset($q['value']) ? unserialize($q['value']) : array();
$shop_parameters = mysql_select("SELECT id,name FROM shop_parameters ORDER BY `rank` DESC",'array');
foreach ($parameters as $k=>$v) {
	if (array_key_exists($k,$shop_parameters)) {
		$parameters[$k]['name'] = $shop_parameters[$k];
		unset($shop_parameters[$k]);
	}
	else unset($parameters[$k]);
}
foreach ($shop_parameters as $k=>$v) $parameters[$k] = array('name'=>$v);
?>

Параметры добавляются и редактируются в разделе <a href="?m=shop_parameters"><u>параметры</u></a>
<br />Здесь настраивается только сортировка и отображдение параметров на сайте<br /><br />
<div style="float:left; padding:0 15px 0 155px">в фильтре поиска <a href="#" title="показывать поле поиска по параметру на сайте в фильтре поиска товаров" class="sprite question"></a></div>
<div style="float:left; width:170px;">на странице товара <a href="#" title="показывать параметр на странице товара" class="sprite question"></a></div>
<div style="float:left; width:100px;">показывать <a href="#" title="включить/отключить показ везде" class="sprite question"></a></div>
<ul class="sortable">
<?php
foreach ($parameters as $k=>$v) {
	?>
		<li title="для изменения сортировки переместите в нужное место и сохраните">
		<div style="float:left; width:200px"><?=$v['name']?></div>
		<?=form('checkbox line td2','parameters['.$k.'][filter]',isset($parameters[$k]['filter']) ? $parameters[$k]['filter'] : '',array('name'=>' '));?>
		<?=form('checkbox line td2','parameters['.$k.'][product]',isset($parameters[$k]['product']) ? $parameters[$k]['product'] : '',array('name'=>' '));?>
		<?=form('checkbox line td2','parameters['.$k.'][display]',isset($parameters[$k]['display']) ? $parameters[$k]['display'] : '',array('name'=>' '));?>
		</li>
	<?php
}
?>
</ul>