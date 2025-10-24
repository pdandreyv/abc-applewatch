<?php

//динамические параметры
/*
 * v1.2.19 - добавил множественный выбор для параметров
 * v1.4.16 - $delete удалил confirm
 * v1.4.17 - сокращение параметров form
 */

$config['shop_parameters']['type'] = array(
	1 => 'выбор из вариантов',
	4 => 'множественный выбор',//v1.2.19
	2 => 'число',
	3 => 'чекбокс',
	5 => 'строка',
);

$decimal = array(0,1,2,3);

$post['type'] = array_key_exists(isset($post['type']) ? $post['type'] : 0,$config['shop_parameters']['type']) ? $post['type'] : 1;
if ($get['u']=='edit') {
	if (in_array($post['type'],array(1,4))) {
		if (isset($post['values']['select'])) {
			if (is_array($post['values']['select']))
				foreach ($post['values']['select'] as $k=>$v) if ($v=='') unset($post['values']['select'][$k]);
			$post['values'] = serialize($post['values']['select']);
		}
		else $post['values'] = '';
	}
	elseif (in_array($post['type'],array(2))) {
		$post['values'] = isset($post['values']['decimal']) ? intval($post['values']['decimal']) : 0;
		$post['values'] = in_array($post['values'],$decimal) ? $post['values'] : 0;
	}
	elseif (in_array($post['type'],array(3)))
		$post['values'] = (isset($post['values']['checkbox']) && $post['values']['checkbox']) ? serialize($post['values']['checkbox']) : '';
	else $post['values'] = '';

}

//v1.4.16 - $delete удалил confirm
function event_change_shop_parameters($q) {
	if (@$_GET['id']=='new') {
		if (in_array($q['type'],array(1,3)))
			mysql_fn('query','ALTER TABLE  `shop_products` ADD  `p'.$q['id'].'` INT UNSIGNED NOT NULL, ADD INDEX (  `p'.$q['id'].'` )');
		elseif($q['type']==2)
			mysql_fn('query','ALTER TABLE  `shop_products` ADD  `p'.$q['id'].'` DECIMAL( 10,'.$q['values'].') NOT NULL, ADD INDEX (  `p'.$q['id'].'` )');
		elseif(in_array($q['type'],array(4,5))) //v1.2.19 v1.2.76
			mysql_fn('query','ALTER TABLE  `shop_products` ADD  `p'.$q['id'].'`  VARCHAR( 255 ) NOT NULL, ADD INDEX (  `p'.$q['id'].'` )');
	}
	else {
		if (in_array($q['type'],array(1,3)))
			mysql_fn('query','ALTER TABLE  `shop_products` CHANGE  `p'.$q['id'].'` `p'.$q['id'].'` INT UNSIGNED NOT NULL');
		elseif($q['type']==2)
			mysql_fn('query','ALTER TABLE  `shop_products` CHANGE  `p'.$q['id'].'`  `p'.$q['id'].'` DECIMAL( 10,'.$q['values'].') NOT NULL');
		elseif(in_array($q['type'],array(4,5))) //v1.2.19 v1.2.76
			mysql_fn('query','ALTER TABLE  `shop_products` CHANGE  `p'.$q['id'].'`  `p'.$q['id'].'` VARCHAR( 255 ) NOT NULL');
	}
}

//v1.4.16 - $delete удалил confirm
function event_delete_shop_parameters($q) {
	mysql_fn('query','ALTER TABLE `shop_products` DROP `p' . $q['id'] . '`', 'ALTER TABLE shop_products DROP INDEX p' . $q['id'] . '');
}

$a18n['type']			= 'тип';
$a18n['import']			= 'использовать при синхронизации';

$table = array(
	'id'	=>	'rank:desc name id',
	'name'	=>	'',
	'units'	=> '',
	'rank'	=>	'',
	'type'	=>	$config['shop_parameters']['type'],
	'import' => 'boolean',
	'display' => 'display'
);

$form[] = array('input td6','name');
$form[] = array('input td2','units');
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');

$form[] = array('select td6','type',array(
	'value'=>array(true,$config['shop_parameters']['type'])
));
$form[] = array('checkbox','import');

$form[] = 'clear';

$template['select'] = '
<li class="field input">
	<a href="#" class="sprite delete"></a>
	<input name="values[select][{i}]" value="{value}">
</li>
';
$template['checkbox'] = '
<div class="field input td2">
	<label>Да</label>
	<div><input name="values[checkbox][1]" value="{yes}"></div>
</div>
<div class="field input td2">
	<label>Нет</label>
	<div><input name="values[checkbox][2]" value="{no}"></div>
</div>

';
if ($get['u']=='form' OR $get['id']>0) {
	$values = (isset($post['values']) && $post['values']) ? unserialize($post['values']) : array();
	if(!is_array($values)) $values=array();
	//выбор из вариантов
	$form[] = '<div data-type="select" class="parameter_values"'.(in_array($post['type'],array(1,4)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">В товаре можно будет выбирать значения из указанных здесь вариантов.</div>';
	$form[] = '<b>Значения параметров:</b> &nbsp; ';
	$form[] = '<input name="values[select][0]" type="hidden" value="" />'; //индекс 0 по умолчанию пустой чтобы не создавался
	$form[] = '<a href="#" class="plus button green"><span><span class="sprite plus"></span>добавить вариант</span></a>';
	$form[] = '<ul class="sortable">';
	foreach ($values as $k=>$v) $form[] = template($template['select'],array('i'=>$k,'value'=>$v));
	if(in_array($values,array(1,4))) for ($i=count($values); $i<2; $i++) $form[] = template($template['select'],array('i'=>'','value'=>''));
	$form[] = '</ul>';
	$form[] = '</div>';

	$form[] = '<div data-type="checkbox" class="parameter_values"'.(in_array($post['type'],array(3)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">Укажите варинаты да/нет для товара (например, есть/нет, присутсвует/отсутсвует и т.д.)</div>';
	$form[] = template($template['checkbox'],array('yes'=>isset($values[1]) ? $values[1] : '','no'=>isset($values[2]) ? $values[2] : ''));
	$form[] = '</div>';

	$form[] = '<div data-type="decimal" class="parameter_values"'.(in_array($post['type'],array(2)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">Данный параметр будет чисельный и в фильтре поиска товаров будет возможность фильтровать товары от минимального до максимального значения данного параметра</div>';
	$form[] = array('select td3','values[decimal]',array(
		'value'=>array(isset($post['values']) ? $post['values'] : '',$decimal),
		'name'=>'количество нулей после запятой'
	));
	$form[] = '</div>';

	$form[] = '<div data-type="string" class="parameter_values"'.(in_array($post['type'],array(5)) ? '' : ' style="display:none"').'>';
	$form[] = '<div style="padding:0 0 5px">Данный параметр будет обычтной строкой, потому в фильтре поиска не может использоваться</div>';
	$form[] = '</div>';
}

$content = '
<div>Здесь настраиваются только сами параметры. Настройка отображения и сортировки параметров настраиваются в <a href="/admin.php?m=shop_categories">разделах каталога</a>.</div>
<div style="display:none">
<textarea id="template_select">'.htmlspecialchars($template['select']).'</textarea>
</div>
<style>
.parameter_values li {padding:2px 13px; float:none;}
.parameter_values li.field input {width:830px}
.parameter_values li.field {min-height:auto;}
.parameter_values li.field a {float:right; margin:2px 0 0}
</style>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	$(document).on("change","select[name=\'type\']",function(){
		$(".parameter_values").hide();
		var type = $(this).val();
		if (type==1) $(".parameter_values[data-type=\'select\']").show();
		if (type==2) $(".parameter_values[data-type=\'decimal\']").show();
		if (type==3) $(".parameter_values[data-type=\'checkbox\']").show();
		if (type==4) $(".parameter_values[data-type=\'select\']").show();
		if (type==5) $(".parameter_values[data-type=\'string\']").show();
		return false;
	});
	$(document).on("click",".parameter_values .plus",function(){
		var content = $("#template_select").val();
		content = content.replace(/{[^}]*}/g,"");
		$(this).next("ul").append(content);
		$("ul.sortable").sortable();
		return false;
	});
	$(document).on("click",".parameter_values .delete",function(){
		$(this).parent("li").remove();
		return false;
	});
});
</script>
';