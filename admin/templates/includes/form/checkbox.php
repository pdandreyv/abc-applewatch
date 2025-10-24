<?php
$checked = $q['value']==1 ? 'checked="checked"' : '';
?>
<div class="field <?=$q['class']?>">
	<input type="hidden" name="<?=@$q['key']?>" value="0" />
	<label>
		<input type="checkbox" name="<?=@$q['key']?>" value="1" <?=$checked?> <?=$q['attr']?> />
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
			<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
</div>