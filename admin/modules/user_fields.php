<?php

//параметры пользователя
/*
 * v1.4.17 - сокращение параметров form
 */

$config['user_fields']['type'] = array(
	1 => 'строка',
	2 => 'выбор из вариантов',
	3 => 'текстовое поле',
);
//если многоязычность то вырубаем выбор из вариантов
//todo - добавить для многоязычности выбор из вариантов
if ($config['multilingual']==true) unset($config['user_fields']['type'][2]);

if ($get['u']=='edit') {
	if (in_array($post['type'],array(2))) {
		if (isset($post['values']['select'])) {
			if (is_array($post['values']['select']))
				foreach ($post['values']['select'] as $k=>$v) if ($v=='') unset($post['values']['select'][$k]);
			$post['values'] = serialize(@$post['values']['select']);
		}
		else $post['values'] = '';
	}
	else $post['values'] = '';
}
$a18n['type']		= 'тип';
$a18n['required']	= 'обязательное';
$a18n['hint']		= 'описание';

$table = array(
	'id'		=>	'rank:desc name id',
	'name'		=>	'',
	'hint'		=>	'',
	'type'		=>	$config['user_fields']['type'],
	'rank'		=>	'',
	'required'	=>	'boolean',
	'display'	=>	'display',
);

$tabs = array(
	1=>'Общее',
);

$template['select'] = '
<li class="field input">
	<a href="#" class="sprite delete"></a>
	<input name="values[select][{i}]" value="{value}">
</li>
';

$form[1][] = array('input td2','name');
$form[1][] = array('input td4','hint');
$form[1][] = array('input td2','rank');
$form[1][] = array('checkbox','display');
$form[1][] = array('checkbox','required');
$form[1][] = array('select td6','type',array('value'=>array(true,$config['user_fields']['type'])));
$form[1][] = 'clear';

if ($get['u']=='form') {
  	//выбор из вариантов
	$form[1][] = '<div data-type="select" class="parameter_values"'.(in_array($post['type'],array(2)) ? '' : ' style="display:none"').'>';
	$form[1][] = '<div style="padding:0 0 5px">В параметрах пользователя можно будет выбирать значения из указанных здесь вариантов.</div>';
	$form[1][] = '<b>Значения параметров:</b> &nbsp; ';
	$form[1][] = '<input name="values[select][0]" type="hidden" value="" />'; //индекс 0 по умолчанию пустой чтобы не создавался
	$form[1][] = '<a href="#" class="plus button green"><span><span class="sprite plus"></span>добавить вариант</span></a>';
	$form[1][] = '<ul class="sortable">';
	//$values = (isset($post['values']) AND is_array($post['values'])) ? unserialize($post['values']) : array();
	$values = (isset($post['values']) AND is_array($vals = unserialize($post['values']))) ? $vals : array();
	if(!is_array($values)) $values=array();
	foreach ($values as $k=>$v) $form[1][] = template($template['select'],array('i'=>$k,'value'=>$v));
	if(count($values)<2) for ($i=count($values); $i<2; $i++) $form[1][] = template($template['select'],array('i'=>'','value'=>''));
	$form[1][] = '</ul>';
	$form[1][] = '</div>';
}

$content = '
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
	$(document).on("change",".form select[name=\'type\']",function(){
		$(".parameter_values").hide();
		var type = $(this).val();
		if (type==2) $(".parameter_values[data-type=\'select\']").show();
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