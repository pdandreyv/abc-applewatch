<?php

//сопутсвующие товары для товара
/*
 * v1.4.25 - сокращение параметров form
 */

//поиск сопуствующих товаров
if ($get['u']=='similar_search') {
	$search = stripslashes_smart(@$_GET['value']);
	if ($i=intval($search)) $where = " id=".$i." ";
	else $where = " LOWER(name) LIKE '%".mysql_res(mb_strtolower($search,'UTF-8'))."%' OR LOWER(article) LIKE '%".mysql_res(mb_strtolower($search,'UTF-8'))."%' ";
	$query = "SELECT * FROM shop_products WHERE ".$where." LIMIT 10";
	if ($products = mysql_select($query,'rows')) {
		foreach ($products as $k=>$v) {
			$img = get_img('shop_products',$v,'img');
			echo '<li data-id="'.$v['id'].'" title="Перетащите в правую колонку для сохранения">';
			echo $v['img'] ? '<img src="/_imgs/100x100'.$img.'" />' : '<div></div>';
			echo '<b>'.$v['article'].'</b><br />';
			echo $v['name'].'<br />';
			echo $v['price'].' руб.';
			echo '</li>';
		}
	}
	die();
}

//добавляем там
$tabs[4] = 'Сопутсвующие товары';

//форма
$form[4][] = array('input td6','',array('name'=>'Поиск товаров по названию, артикулу, ID','attr'=>'id="similar_search"'));
$form[4][] = array('input td6','similar',array('name'=>'ID сопутсвующих товаров через запятую'));
$form[4][] = '<ul id="similar_results" class="product_list"></ul>';
$form[4][] = '<ul id="similar" class="product_list">';
if (@$post['similar']) {
	$query2 = "SELECT * FROM shop_products WHERE id IN (".$post['similar'].") LIMIT 10";
	if ($products = mysql_select($query2,'rows_id')) {
		$similar = explode(',',$post['similar']);
		foreach ($similar as $k=>$v) if (isset($products[$v])) {
			$img = get_img('shop_products',$products[$v],'img');
			$form[4][] = '<li data-id="'.$products[$v]['id'].'" title="Перетащите в правую колонку для сохранения">';
			$form[4][] = $products[$v]['img'] ? '<img src="/_imgs/100x100'.$img.'" />' : '<div></div>';
			$form[4][] = '<b>'.$products[$v]['article'].'</b><br />';
			$form[4][] = $products[$v]['name'].'<br />';
			$form[4][] = $products[$v]['price'].' руб.';
			$form[4][] = '</li>';
		}
	}
}
$form[4][] = '</ul>';

//стили
$content.= '
<style>
.product_list {float:left; min-height:300px; width:431px; background:#d6d6d6;}
#similar_results {margin:0 13px 0 0;}
.product_list li {clear:both; padding:5px; height:50px; cursor:move}
.product_list li img,
.product_list li div {width:50px; height:50px; float:left; margin:0 5px 0 0}
.product_list li:hover {background:#FFFEDF}
</style>';

$content.= '
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {

	//поиск сопуствующих товаров
	$(document).on("keyup","#similar_search",function(e) {
		var value	= $(this).val();
		$.get(
			"/admin.php?m=shop_products&u=similar_search",
			{"value":value},
			function(data){
				$("#similar_results").html(data);
			}
		).fail(function() {
			alert("Нет соединения!");
		});
	});

	similar_results();
	$(document).on("form.open",".form",function(){
		similar_results();
	});

	//сортировка товаров
	function similar_results () {
		$("#similar_results, #similar" ).sortable({
			connectWith: ".product_list",
			stop: function() {
				var similar = [];
				$("#similar li").each(function(){
					similar.push($(this).data("id"));
				});
				$("input[name=similar]").val(similar);
			}
		}).disableSelection();
	}

});
</script>';