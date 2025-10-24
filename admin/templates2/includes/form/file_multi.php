<div class="form-group files col-xl-12 <?=$q['type']?>" data-i="<?=$q['key']?>">
	<div class="name"><?=$q['name']?></div>
	<ul class="sortable clearfix">
	<?php
	if ($q['photos']) foreach ($q['photos'] as $k=>$v) {
		$img = '';
		if ($q['type']=='file_multi') {
			$img = get_img($q['module'], $q['item'], $q['key'] . '/' . $k);
		}
		//v1.4.59 - hypertext
		elseif ($q['type']=='file_multi file_hypertext') {
			$img = get_img($q['module'], $q['item'], $q['field'] . '/' . $q['n'].'_'.$k);
		}
		//file_multi_db
		else {
			$img = get_img($q['module'], $v, 'img');
		}
		if (@$v['file']) {
			$explode = explode('.',$v['file']);
			$exc = end($explode);
			if (in_array($exc,array('png','gif','svg','jpg','jpeg','bmp'))) {
				$preview =  '/_imgs/100x100'.$img;
			}
			else {
				$preview = '/admin/templates/icons/blank.png';
				if (in_array($exc,array('sql','txt','doc','docx')))	$preview = '/admin/templates/icons/doc.png';
				elseif (in_array($exc,array('xls','xlsx')))		$preview = '/admin/templates/icons/xls.png';
				elseif (in_array($exc,array('pdf')))			$preview = '/admin/templates/icons/pdf.png';
				elseif (in_array($exc,array('zip','rar')))		$preview = '/admin/templates/icons/zip.png';
			}
			$q['file'] = $v;
			$q['file']['i'] = $k;
			$q['file']['preview'] = $preview;
			$q['file']['img'] = $img;
			echo html_render('form/file_multi_item',$q);
		}
	}
	?>
	</ul>
	<div class="data">
		<a class="add_file" title="Выбрать файлы">
			Перетащите файлы сюда или нажмите, чтобы загрузить
			<input type="file" multiple="multiple" title="выбрать файл" />
		</a>
	</div>
	<template>
		<?=html_render('form/file_multi_item',array(
			'file'=>array(
				'i'=>'{i}',
				'display'=>1,
				'img'=>'{img}',
				'preview'=>'{preview}',
				'file'=>'{file}',
			),
			'key'=>$q['key'],
			'fields'=>$q['fields']
		))?>
	</template>
</div>