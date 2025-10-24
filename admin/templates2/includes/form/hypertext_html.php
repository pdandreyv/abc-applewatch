<div class="hypertext_item col-xl-12" data-n="<?=$q['n']?>">
	<?=html_render('form/hypertext_add',$q)?>
	<div class="form-group tinymce">
		<input type="hidden" name="<?=$q['key']?>[<?=$q['n']?>][type]" value="html">
		<div>
			<textarea class="hypertext" id="<?=$q['key'].'-'.$q['n']?>" cols="1" rows="1" name="<?=$q['key']?>[<?=$q['n']?>][content]"><?=$q['content']?></textarea>
		</div>
		<div class="clear"></div>
	</div>
</div>