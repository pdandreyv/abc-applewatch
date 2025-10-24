<?php
$url = '/admin.php?' . $_SERVER['QUERY_STRING'];
$content = '';
if (!isset($q['table']['_edit'])) $q['table'] = array_merge(array('_edit'=>true),$q['table']);
elseif ($q['table']['_edit']==false) unset($q['table']['_edit']);
if (!isset($q['table']['_delete'])) $q['table']['_delete'] = true;
elseif ($q['table']['_delete']==false) unset($q['table']['_delete']);

foreach ($q['list'] as $row) {?>
<tr
	<?php
	//дерево
	if (array_key_exists('_tree',$q['table'])) {
		echo 'data-parent="'.$row['parent'].'" data-level="'.$row['level'].'"';
	}
	//сортировка - не готово
	elseif (array_key_exists('_sorting',$q['table'])) {
		echo 'data-sorting="'.$row[$q['table']['_sorting']].'" data-id="'.$row['id'].'"';
	}
	//новая запись
	if (@$_GET['u']=='edit' AND @$_GET['id']==$row['id']) echo 'class="is_open"';
	?> data-id="<?=$row['id']?>">
<?
foreach ($q['table'] as $k=>$v) {
	if ($v && !is_array($v)) {
		preg_match_all('/{(.*?)}/',$v,$matches,PREG_PATTERN_ORDER);
		foreach($matches[1] as $key=>$val) $matches[1][$key] = isset($row[$val]) ? $row[$val] : '';
		$v = str_replace($matches[0],$matches[1],$v);
	}
	//v1.2.130 - чекбоксы для админки
	if ($k=='_check')		$content.= '<td><input type="checkbox" name="_check" value="'.$row['id'].'"/></td>';
	elseif ($k=='_edit')		$content.= '<td align="center"><a href="/admin.php?'.$url.'id='.$row['id'].'" class="sprite edit open"></a></td>';
	elseif ($k=='_view') {
		$content.= '<td><a class="sprite view" target="_blank" href="'.get_url($v,$row).'"></a></td>';
	}
	elseif ($k=='_tree')	$content.= '<td class="level"><span class="sprite level item"></span></td>';
	elseif ($k=='_sorting')	$content.= '<td><span class="sprite sorting"></span></td>';
	elseif ($k=='_delete')	$content.= '<td align="center"><a class="sprite delete" href="#"></a></td>';
	elseif ($k=='id')		$content.= '<td align="right"><b>'.$row[$k].'</b></td>';
	elseif (is_array($v))	{
		if (substr($k,-1)==':') {
			$k = trim($k,':');
			//$content.= '<td><select name="'.$k.'">'.select($row[$k],$v).'</select></td>';
			$str = '';
			if (isset($row[$k]) AND isset($v[$row[$k]])) {
				$str = is_array($v[$row[$k]]) ? $v[$row[$k]]['name'] : $v[$row[$k]];
			}
			$content.= '<td class="select" data-id="'.$row[$k].'" data-name="'.$k.'">'.$str.'</td>';
		}
		else {
			$str = '';
			if (isset($row[$k]) AND isset($v[$row[$k]])) {
				$str = is_array($v[$row[$k]]) ? $v[$row[$k]]['name'] : $v[$row[$k]];
			}
			$content.= '<td><b>'.$str.'</b></td>';
		}
	}
	elseif ($v=='date')		$content.= '<td data-name="'.$k.'" class="post">'.$row[$k].'</td>';
	elseif ($v=='boolean' OR $v=='display') {
		$key = in_array($k,$config['boolean']) ? $k : 'boolean';
		$content.= '<td align="center" data-name="'.$k.'" data-key="'.$key.'">';//key - клас спрайта для иконки
		$content.= '<a class="sprite '.$key.'_'.($row[$k]==1 ? '1' : '0').' js_boolean" href="#" title="'.a18n($k).'"></a>';
		$content.= '</td>';
	}
	elseif ($v=='right')	$content.= '<td data-name="'.$k.'" align="right" class="post">'.$row[$k].'</td>';
	elseif ($v=='text')		$content.= '<td data-name="'.$k.'"><b>'.$row[$k].'</b></td>';
	elseif ($v=='img')		{
		//v1.2.115 заменил пути на get_img
		$img =  get_img($q['module'],$row,$k,'');
		$preview = get_img($q['module'],$row,$k);
		$content.= '<td align="center" data-name="'.$k.'">'.($row[$k] ? '<a onclick="return hs.expand(this)" href="'.$img.'"><img class="img" src="/_imgs/100x100'.$preview.'" /></a>' : '').'</td>';
	}
	elseif ($v=='')			$content.= '<td data-name="'.$k.'" class="post">'.(isset($row[$k]) ? $row[$k] : '').'</td>';
	elseif (substr($v,0,2)=='::') {
		$function = substr($v,2);
		//v1.2.102 - добавлен второй аргумент в 'field' => '::function',
		if (function_exists($function)) $content.= $function($row,$k);
		else $content.= '<td>'.$function.'</td>';
	}
	else					$content.= '<td>'.$v.'</td>';
}
//
echo $content;
$content = '';
?>
</tr>
<?php } ?>
