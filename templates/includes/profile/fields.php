<?php
//print_r($q);
if (is_array($q)) $fields=$q;
else $fields = unserialize($q);

$data = mysql_select("
	SELECT *
	FROM user_fields
	WHERE display = 1
	ORDER BY `rank` DESC
",'rows',60*60*24);
if (is_array($data)) foreach($data as $key=>$f) {
	$f['name'] = $f['name'.$lang['i']];
	$attr = $f['required']==1 ? ' required' : '';
	if (!isset($fields[$f['id']][0])) $fields[$f['id']][0]='';
	if ($editable=editable('user_fields|name|'.$f['id'])) $name = '<span'.$editable.'>'.$f['name'].'</span>';
	else $name = $f['name'];
	//input
	if ($f['type']==1) {
		echo html_array('form/input',array(
			'caption'	=>	$name,
			'name'	=>	'fields['.$f['id'].'][]',
			'value'	=>	$fields[$f['id']][0],
			'attr'	=>	$attr
		));

	//select
	} elseif ($f['type']==2) {
		$values = $f['values'] ? unserialize($f['values']) : '';
		echo html_array('form/select',array(
			'name'	=>	'fields['.$f['id'].'][]',
			'caption'	=>	$name,
			'select'=>	select($fields[$f['id']][0],$values,''),
			'attr'	=>	$attr
		));
	//textarea
	} elseif ($f['type']==3) {
		echo html_array('form/textarea',array(
			'caption'	=>	$name,
			'name'	=>	'fields['.$f['id'].'][]',
			'value'	=>	$fields[$f['id']][0],
			'attr'	=>	$attr
		));
	}
}

/*
?>
<div class="field avatar">
	<label>Загрузить аватар</label>
	<input name="avatar" type="file" />
<?php
if (@$q['avatar']!='' && is_file(get_img('users',$q,'avatar'))) {
	echo '<img align="right" src="'.get_img('users',$q,'avatar').'?'.date('s').'" />';
	echo '<br /><input name="del_avatar" type="checkbox" value="1" /> удалить текущий аватар';
}
?>
</div>
/* */?>