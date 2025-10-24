<?php
$url = '/admin.php?' . $_SERVER['QUERY_STRING'];
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
	if ($k == '_check') $content .= '<th style="text-align:center; padding:0px"><input type="checkbox" name="_check" /></th>';
	elseif ($k == '_tree') $content .= '<th class="colspan" style="padding:0 0 0 10px"><span class="sprite tree" title="дерево вложенности"></span></th>';
	elseif ($k == '_sorting') $content .= '<th class="colspan"><span class="sprite sorting" title="сортировка"></span></th>';
	elseif ($k == '_edit') {
		if ($v === 'edit') {
			$content .= '<th style="padding:0; text-align:center"></th>';
		}
		else {
			$content .= '<th style="padding:0; text-align:center"><a class="sprite plus2 open" href="/admin.php?' . $_SERVER['QUERY_STRING'] . '&id=new" title="добавить новую запись"></a></th>';
		}
	}
	elseif ($k == '_view') {
		$content .= '<th width="20px"></th>';
	}
	elseif ($k == '_delete') $content .= '<th width="20px"></th>';
	elseif ($k == 'display') $content .= '<th></th>';
	elseif ($v == 'boolean') $content .= '<th></th>';
	elseif ($v == 'img') $content .= '<th></th>';
	else {
		global $get;

		//$fieldset[$k]  = isset($fieldset[$k]) ? $fieldset[$k] : $k; //если нет $fieldset называем ключом
		$content .= '<th>';
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
			$content .= '<a class="sort' . ($q['order'] == $k ? ' active' : '') . '" href="' . $url . '&o=' . $k . '&s=' . $s . '"><span class="sprite ' . $a . '"></span>' . a18n($k) . '</a>';
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
