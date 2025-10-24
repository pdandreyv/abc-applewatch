<?php

//обработка операций над записями
if (@$_POST['_check'] AND @$_POST['_check']['ids']) {
	require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
	$check = form_smart(array('ids'=>'string_int'),$_POST['_check']);
	if ($check['ids']) {
		$ids = explode(',',$check['ids']);

		//display_on - включить
		if (@$_POST['_check']['display_on'] OR @$_POST['_check']['select'] == 'display_on') {
			foreach ($ids as $k=>$v) {
				//вносим изменения
				if (mysql_fn('update',$module['table'],array('id'=>$v,'display'=>1))) {
					//логирование действия
					mysql_fn('insert', 'logs', array(
						'user' => $user['id'],
						'date' => $config['datetime'],
						'parent' => $v,
						'module' => $module['table'],
						'type' => 2,
						'ip' => get_ip(),
						'fields' => 'display'
					));
				}
			}
		}

		//display_off - отключить
		if (@$_POST['_check']['display_off'] OR @$_POST['_check']['select'] == 'display_off') {
			foreach ($ids as $k=>$v) {
				//вносим изменения
				if (mysql_fn('update',$module['table'],array('id'=>$v,'display'=>0))) {
					//логирование действия
					mysql_fn('insert', 'logs', array(
						'user' => $user['id'],
						'date' => $config['datetime'],
						'parent' => $v,
						'module' => $module['table'],
						'type' => 2,
						'ip' => get_ip(),
						'fields' => 'display'
					));
				}
			}
		}

		//delete - удалить
		if (@$_POST['_check']['delete'] OR @$_POST['_check']['select'] == 'delete') {
			foreach ($ids as $k=>$v) {
				//вносим изменения
				if (mysql_fn('delete',$module['table'],$v)) {
					$path = ROOT_DIR.'files/'.$module['table'].'/'.$v.'/';
					delete_all($path);
					//логирование действия
					mysql_fn('insert', 'logs', array(
						'user' => $user['id'],
						'date' => $config['datetime'],
						'parent' => $v,
						'module' => $module['table'],
						'type' => 3,
						'ip' => get_ip()
					));
				}
			}
		}

		//move - переместить в другую категорию
		if (@$_POST['_check']['move'] OR @$_POST['_check']['select'] == 'move') {
			$category = intval(@$_POST['category']);
			if ($category) {
				foreach ($ids as $k => $v) {
					//вносим изменения
					if (mysql_fn('update', $module['table'], array('id' => $v, 'category' => $category))) {
						//логирование действия
						mysql_fn('insert', 'logs', array(
							'user' => $user['id'],
							'date' => $config['datetime'],
							'parent' => $v,
							'module' => $module['table'],
							'type' => 2,
							'ip' => get_ip(),
							'fields' => 'category'
						));
					}
				}
			}
		}
	}
}
//подстановка списка категорий

$table = array(
	'_check' => array(
		//в виде кнопок
		'buttons'=>array(
			'display_on'    => 'Включить',
		),
		//в виде селекта
		'select'=>array(
			//'display_on'    => 'Включить',
			'display_off'    => 'Отключить',
			'move'          => 'Переместить',
			'delete'        => 'Удалить'
		)
	)
) + $table;

//список категорий
$template['categories'] = '<select name="category">'.select('',$categories).'</select>';

//шаблон
$content.= '<div style="display:none">';
$content.= '<textarea id="template_categories">'.htmlspecialchars($template['categories']).'</textarea>';
$content.= '</div>';

//скрипт для вставки списка категорий
$content.= '
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	//добавляем/удаляем селект категорий
	$(document).on("change",".table_check select[name^=_check]",function(){
		if ($(this).val()=="move") {
			var content = $("#template_categories").val();
			$(content).insertAfter($(this));
		}
		else {
			$(".table_check select[name=category]").remove();
		}
	});
});
</script>';
