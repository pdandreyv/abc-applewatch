<div class="field <?=$q['class']?>">
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
		<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<div>
		<input name="<?=$q['key']?>" <?=@$q['attr']?> value="<?=htmlspecialchars($q['value'])?>" />
	</div>
</div>