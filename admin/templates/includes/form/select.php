<div class="field <?=$q['class']?>">
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
			<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<div>
		<select name="<?=$q['key']?>" <?=$q['attr']?>><?=select(
			$q['value'][0],
			isset($q['value'][1]) ? $q['value'][1] : '',
			isset($q['value'][2]) ? $q['value'][2] : NULL
)?></select>
	</div>
</div>


