<?php
global $module,$get;
//dd($q['images']);
?>

<div class="hypertext_item col-xl-12" data-n="<?=$q['n']?>">
	<?=html_render('form/hypertext_add',$q)?>

	<?php
	//расскоментировать если нужны блоки разных стилей
	/*
	<div class="form-row" style="margin: 0px">
		<div class="form-group col-xl-9">
			<label>
				<span>Название</span>
			</label>
			<input class="form-control" name="<?=$q['key']?>[<?=$q['n']?>][name]" value="<?=htmlspecialchars($q['name'])?>" />
		</div>
		<div class="form-group col-xl-3">
			<label>
				<span>Стиль блока</span>
			</label>

			<select class="form-control" name="<?=$q['key']?>[<?=$q['n']?>][style]"><?=select(
					$q['style'],
					$config['hypertext_images_styles']
				)?></select>
		</div>
	</div>
 	*/ ?>


	<input type="hidden" name="<?=$q['key']?>[<?=$q['n']?>][type]" value="images">
	<?=html_render('form/file_multi',array(
		'type'=>'file_multi file_hypertext',
		'key'=>$q['key'].'['.$q['n'].'][images]',
		'n'=>$q['n'],
		'field'=>$q['key'],
		'name'=>'',
		'photos'=>@$q['images'],
		'fields'=>array('name'=>'input','title'=>'input','display'=>'checkbox'),
		'module'=>$module['table'],
		'item'=>array(
			'id' => $get['id'],
			$q['key'] => array(
				$q['n']=>array(
					'images'=>@$q['images']
				)
			),
		),
	))?>

</div>