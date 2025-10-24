<?php
global $module,$get;
?>
<div class="hypertext_item col-xl-12" data-n="<?=$q['n']?>">
	<?=html_render('form/hypertext_add',$q)?>
	<div class="form-row" style="padding: 0 5px;">
		<?php
		$item = array(
			'id'=>$get['id'],
			$q['key'].'/'.$q['n']=>$q['img']
		);
		echo html_array('form/file', array(
			'img'=>get_img($module['table'],$item,$q['key'].'/'.$q['n']),
			'name'=>'',
			'type'=>'file',
			'is_file'=>$q['img']?1:0,
			'key'=>$q['key'].'['.$q['n'].'][img]',
			//'item'=>$q,
			//'sizes'=>'',//$param['sizes'],
			'module'=>$module['table'],
			'file'=>$q['img']
		));
		?>
	</div>
	<div class="form-group tinymce">
		<input type="hidden" name="<?=$q['key']?>[<?=$q['n']?>][type]" value="img">
		<div>
			<textarea class="hypertext" id="<?=$q['key'].$q['n']?>" cols="1" rows="1" name="<?=$q['key']?>[<?=$q['n']?>][content]"><?=$q['content']?></textarea>
		</div>
		<div class="clear"></div>
	</div>
</div>