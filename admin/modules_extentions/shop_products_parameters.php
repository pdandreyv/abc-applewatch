<?php

//динамические параметры для товара

//ответ аджакса со списком параметров товара при изменении категории
if ($get['u']=='shop_parameters') {
	$parameters = mysql_select("SELECT parameters FROM shop_categories WHERE id=".intval(isset($get['category']) ? $get['category'] : 0),'string');
	$parameters = $parameters ? unserialize($parameters) : array();
	$shop_parameters = mysql_select("SELECT id,name,type,`values`,units FROM shop_parameters ORDER BY `rank` DESC",'rows_id');
	foreach ($parameters as $k=>$v) if (isset($v['display']) && $v['display']==1){
		$name = $shop_parameters[$k]['name'].($shop_parameters[$k]['units'] ? ' ('.$shop_parameters[$k]['units'].')' : '');
		if (array_key_exists($k,$shop_parameters)) {
			if (!isset($post['p'.$k])) $post['p'.$k] = '';
			if (in_array($shop_parameters[$k]['type'],array(1,3))) {
				echo form('select td3', 'p' . $k, array(
					'value'=>array($post['p' . $k], unserialize($shop_parameters[$k]['values']), ''),
					'name' => $name
				));
			}
			//multicheckbox v1.2.19
			elseif ($shop_parameters[$k]['type']==4) {
				echo form('multicheckbox td3', 'p' . $k, array(
					'value'=>array($post['p' . $k], unserialize($shop_parameters[$k]['values']), ''),
					'name' => $name
				));
			}
			else {
				echo form('input td3','p'.$k,array('name'=>$name));
			}
		}
	}
	die();
}

//таб
$tabs[3] = 'Параметры';

//форма
$form[3][] = '';
if (in_array($get['u'],array('form','edit')) OR ($get['id']>0 AND $get['u']=='')) {
	$parameters = mysql_select("SELECT parameters FROM shop_categories WHERE id=".intval(isset($post['category']) ? $post['category'] : 0),'string');
	$parameters = $parameters ? unserialize($parameters) : array();
	$shop_parameters = mysql_select("SELECT id,name,type,`values`,units FROM shop_parameters ORDER BY `rank` DESC",'rows_id');
	$form[3][] = '<div class="col-xl-12">';
	$form[3][] = 'Параметры добавляются и редактируются в разделе <a href="?m=shop_parameters"><u>параметры</u></a>.';
	$form[3][] = '<br />Настройка сортировки и отображения параметров на сайте редактируется в разделе <a href="?m=shop_categories"><u>категории</u></a>.
	<br />Здесь редактируются только значения параметров товара.<br /><br />
	</div>';
	$form[3][] = '<div id="shop_parameters" class="col-xl-12"><div class="form-row">';
	foreach ($parameters as $k=>$v) if (isset($v['display']) && $v['display']==1){
		$name = $shop_parameters[$k]['name'].($shop_parameters[$k]['units'] ? ' ('.$shop_parameters[$k]['units'].')' : '');
		if (array_key_exists($k,$shop_parameters)) {
			if (!isset($post['p'.$k])) $post['p'.$k] = '';
			if (in_array($shop_parameters[$k]['type'],array(1,3))) {
				$form[3][] = array('select td3', 'p' . $k, array(
					'name' => $name,
					'value'=>array($post['p' . $k], unserialize($shop_parameters[$k]['values']), '')
				));
			}
			//multicheckbox v1.2.19
			elseif ($shop_parameters[$k]['type']==4) {
				$form[3][] = array('multiple td12', 'p' . $k, array(
					'value'=>array($post['p' . $k], unserialize($shop_parameters[$k]['values'])),
					'name' => $name
				));
			}
			else {
				$form[3][] = array('input td3','p'.$k,array('name'=>$name));
			}
		}
	}
	$form[3][] = '</div></div>';
}


$content.= '
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {

	//замена параметров при смене категории
	$(document).on("change",".form select[name=category]",function(){
		var category = $(this).val(),
			id = $(".form").prop("id").replace(/[^0-9]/g,"");
		$.get(
			"/admin.php",
			{"m":"shop_products","u":"shop_parameters","category":category,"id":id},
			function(data){$("#shop_parameters").html(data)}
		);
	});

});
</script>';