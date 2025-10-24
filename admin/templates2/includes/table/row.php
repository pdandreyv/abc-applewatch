<?php
/*
 * v1.4.56 - права добавления/редактирования
 */
$url = '/admin.php?' . $_SERVER['QUERY_STRING'].'&';
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
	//новая запись + v1.4.44 сохранить как
	if (@$_GET['u']=='edit' AND (@$_GET['id']==$row['id'] OR @$_GET['save_as'])) echo 'class="is_open"';
	?> data-id="<?=$row['id']?>">
<?
foreach ($q['table'] as $k=>$v) {
	//v1.4.56 - если строка
	if ($v && is_string($v)) {
		preg_match_all('/{(.*?)}/',$v,$matches,PREG_PATTERN_ORDER);
		foreach($matches[1] as $key=>$val) $matches[1][$key] = isset($row[$val]) ? $row[$val] : '';
		$v = str_replace($matches[0],$matches[1],$v);
	}
	//v1.2.130 - чекбоксы для админки
	if ($k=='_check')		$content.= '<td class="table_checkbox"><input type="checkbox" name="_check" value="'.$row['id'].'"/></td>';
	elseif ($k=='_edit')		{
		//v1.4.56 - права добавления/редактирования
		if ($v===true OR $v=='edit') {
			$content .= '<td align="center"><a href="' . $url . 'id=' . $row['id'] . '" class="open" data-toggle="tooltip" title="' . a18n('edit') . '"><i data-feather="edit-2"></i></a></td>';
		}
		else {
			$content .= '<td></td>';
		}
	}
	elseif ($k=='_view') {
		$content.= '<td><a target="_blank" href="'.get_url($v,$row).'"><i data-feather="search"></i></a></td>';
	}
	elseif ($k=='_tree')	{
		$content.= '<td class="level">';
		for ($n=1; $n<=$row['level']; $n++) {
			$content.='<i data-feather="chevron-right"></i>';
		}
		$content.='</td>';
	}
	elseif ($k=='_sorting')	$content.= '<td><span class="sprite sorting"></span></td>';
	elseif ($k=='_delete')	{
		$content .= '<td align="center"><a class="delete" href="#"  title="'.a18n('delete').'" data-toggle="tooltip"><i data-feather="x-circle"></i></a></td>';
	}
	elseif ($k=='id')		$content.= '<td align="right">'.$row[$k].'</td>';
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
			$content.= '<td>'.$str.'</td>';
		}
	}
	elseif ($v=='date')		$content.= '<td data-name="'.$k.'" class="post">'.$row[$k].'</td>';
	elseif ($v=='date_smart')		$content.= '<td title="'.$row[$k].'">'.date2($row[$k],'smart').'</td>';
	elseif ($v=='boolean' OR $v=='display') {
		$content .= '<td data-name="' . $k . '">
			<div class="custom-control custom-switch custom-checkbox-warning" title="'.a18n($k).'" data-toggle="tooltip">
				<input type="checkbox" class="js_boolean custom-control-input" id="'.$k.'-'.$row['id'].'" ' .($row[$k] == 1 ? 'checked' : '0') . '>
				<label class="custom-control-label" for="'.$k.'-'.$row['id'].'"></label>
			</div>
		</td>';
	}
	elseif ($v=='right')	$content.= '<td data-name="'.$k.'" align="right" class="post">'.$row[$k].'</td>';
	elseif ($v=='text')		{
		$content.= '<td data-name="'.$k.'"><span>'.$row[$k].'</span></td>';
	}
	elseif ($v=='img')		{
		//v1.2.115 заменил пути на get_img
		$img =  get_img($q['module'],$row,$k,'');
		$content .= '<td align="center" data-name="' . $k . '">' . ($row[$k] ? '<a class="image-popup" href="' . $img . '"><img class="img" src="/_imgs/100x100' . $img . '" /></a>' : '') . '</td>';

	}
	elseif ($v=='imgs')		{
		$imgs =  get_imgs($q['module'],$row,$k);
		$img = '';
		if ($imgs) foreach ($imgs as $i)  {
            $img = $img?$img:$i['_'];
		}
		$content .= '<td align="center" data-name="' . $k . '">' . ($row[$k] ? '<a class="image-popup" href="' . $img . '"><img class="img" src="/_imgs/100x100' . $img . '" /></a>' : '') . '</td>';
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
