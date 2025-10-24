<div class="hypertext_item col-xl-12" data-n="<?=$q['n']?>">
	<?=html_render('form/hypertext_add',$q)?>
	<div class="form-group">
		<input type="hidden" name="<?=$q['key']?>[<?=$q['n']?>][type]" value="video">
		<input class="form-control" name="<?=$q['key']?>[<?=$q['n']?>][content]" value="<?=htmlspecialchars($q['content'])?>" />
	</div>
</div>