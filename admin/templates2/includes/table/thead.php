<?php
/*
 * v1.4.56 - права добавления/редактирования
 */
$url = '/admin.php?' . $_SERVER['QUERY_STRING'].'&';
?>

<thead>
<tr data-id="new" class="head">
<?
if (!isset($q['table']['_edit'])) $q['table'] = array_merge(array('_edit'=>true),$q['table']);
elseif ($q['table']['_edit']==false) unset($q['table']['_edit']);
if (!isset($q['table']['_delete'])) $q['table']['_delete'] = true;
elseif ($q['table']['_delete']==false) unset($q['table']['_delete']);


$content = '';
foreach ($q['table'] as $k=>$v) {
//v1.2.130 - чекбоксы для админки
	if ($k == '_check') $content .= '<th class="table_checkbox" style="text-align:center; padding:0px"><input type="checkbox" name="_check" /></th>';
	elseif ($k == '_tree') $content .= '<th class="level"><i data-feather="align-right" title="дерево вложенности"></i></th>';
	elseif ($k == '_sorting') $content .= '<th><span class="sprite sorting" title="сортировка"></span></th>';
	elseif ($k == '_edit') {
		//v1.4.56 - права добавления/редактирования
		if ($v===true OR $v=='add') {
			$content .= '<th style="text-align:center"><a class="open" href="' . $url . 'id=new" title="'.a18n('add').'" data-toggle="tooltip"><i data-feather="plus-circle"></i></a></th>';
		}
		else {
			$content .= '<th style="padding:0; text-align:center"></th>';
		}

	}
	elseif ($k == '_view') {
		$content .= '<th width="20px"></th>';
	}
	elseif ($k == '_delete') $content .= '<th width="20px"></th>';
	elseif ($k == 'display' || $v == 'boolean') {
		// делаем кликабельным заголовок, если поле доступно для сортировки
		$label = a18n($k);
		if (isset($q['sort_array']) && array_key_exists($k, $q['sort_array'])) {
			if ($q['order'] == $k) {
				if ($get['s']) $s = ($get['s'] == 'desc') ? 'asc' : 'desc';
				else $s = $q['sort_array'][$k];
				$a = $s == 'asc' ? ' desc' : ' asc';
			}
			else {
				$s = $q['sort_array'][$k];
				$a = ' none ' . $s;
			}
			$content.= '<th><a class="sort' . ($q['order'] == $k ? ' active' : '') . '" href="' . $url . 'o=' . $k . '&s=' . $s . '">' . $label;
			if ($a==' asc') {
				$content.= '<i data-feather="chevron-down"></i>';
			}
			elseif ($a==' desc') {
				$content.= '<i data-feather="chevron-up"></i>';
			}
			else {
				$content.= '<i data-feather="bar-chart-2"></i>';
			}
			$content.= '</a></th>';
		} else {
			$content .= '<th>'.$label.'</th>';
		}
	}
	elseif ($v == 'img') $content .= '<th></th>';
	else {
		global $get;
		//$fieldset[$k]  = isset($fieldset[$k]) ? $fieldset[$k] : $k; //если нет $fieldset называем ключом
		if ($k=='id')		{
			$content.= '<th style="text-align:right">';
		}
		else $content .= '<th>';
		//скрытый селект для быстрого редактирования
		if (is_array($v) AND substr($k, -1) == ':') {
			$content .= '<select name="' . $k . '">' . select('', $v) . '</select>';
		}
		$k = trim($k, ':'); //удаляем двоеточие от селекта
		if (isset($q['sort_array']) && array_key_exists($k, $q['sort_array'])) {
			if ($q['order'] == $k) {
				if ($get['s']) $s = ($get['s'] == 'desc') ? 'asc' : 'desc';
				else $s = $q['sort_array'][$k];
				$a = $s == 'asc' ? ' desc' : ' asc';
			}
			else {
				$s = $q['sort_array'][$k];
				$a = ' none ' . $s;
			}
			$content.= '<a class="sort' . ($q['order'] == $k ? ' active' : '') . '" href="' . $url . 'o=' . $k . '&s=' . $s . '">' . a18n($k);
			if ($a==' asc') {
				$content.= '<i data-feather="chevron-down"></i>';
			}
			elseif ($a==' desc') {
				$content.= '<i data-feather="chevron-up"></i>';
			}
			else {
				$content.= '<i data-feather="bar-chart-2"></i>';
			}
			$content.= '</a>';
		}
		else $content .= a18n($k);
		$content .= '</th>';
	}
}
//
echo $content;
$content = '';
?>
</tr>
</thead>
