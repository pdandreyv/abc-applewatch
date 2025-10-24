<div class="field <?=$q['class']?>">
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
		<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<div <?=$q['attr']?>>
	<?php
	$value = isset($q['value']) ? $q['value'] : $user['id'];
	if ($value AND $usr = mysql_select("SELECT u.id,u.email login FROM users u WHERE u.id=".intval($value),'row')) {
		?>
		<a href="?m=users&id=<?=$usr['id']?>">[<?=$usr['login']?>]</a>
	<?php
	}
	else {
		?>не указан<?php
	}
	?>
	<input name="<?=$q['key']?>" type="hidden" value="<?=$value?>" />
	</div>
</div>