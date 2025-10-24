<div class="field <?=$q['class']?>">
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
		<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<div>
		<textarea cols="1" rows="1" name="<?=$q['key']?>" <?=$q['attr']?>><?=htmlspecialchars($q['value'])?></textarea>
	</div>
</div>