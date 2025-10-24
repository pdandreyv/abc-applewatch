<?php
$rand = rand(100000,999999);
?>
<div class="form-group <?=$q['class']?>">
	<label<?=$q['title']?' title="'.$q['title'].'"':''?>>
		<span><?=$q['name']?></span>
		<?php if ($q['help']) {?>
		<a href="#" class="sprite question" title="<?=$q['help']?>"></a>
		<?php } ?>
	</label>
	<div>
		<textarea class="default" id="<?=$rand?>" cols="1" rows="1" name="<?=$q['key']?>" <?=$q['attr']?>><?=$q['value']?></textarea>
	</div>
	<div class="clear"></div>
</div>