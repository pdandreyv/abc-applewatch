<div class="files <?=$q['type']?>" data-i="<?=$q['key']?>">
	<div class="data">
		<div class="img" title="Для загрузки картинки переместите её в эту область"><img src="/<?=$config['style']?>/imgs/no_img.png?2" /></div>
		<div class="name"><?=$q['name']?></div>
		<a class="add_file button green" title="Выбрать файлы">
			<span><span class="sprite plus"></span>выбрать</span>
			<input type="file" multiple="multiple" title="выбрать файл" />
		</a>
		<div class="clear"></div>
	</div>
	<ul class="sortable">
	<?php
	if ($q['photos']) foreach ($q['photos'] as $k=>$v) {
		if ($q['type']=='file_multi') {
			$img = get_img($q['module'], $q['item'], $q['key'] . '/' . $k);
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
		?>
		<li data-i="<?=$k?>" title="для изменения последовательности картинок переместите блок в нужное место">
			<div class="img"><span>&nbsp;</span><img src="<?=$preview?>">
				<input name="<?=$q['key']?>[<?=$k?>][temp]" type="hidden" value="" />
				<input name="<?=$q['key']?>[<?=$k?>][file]" value="<?=$v['file']?>" />
			</div>
			<input type="file" name="<?=$q['key']?>[<?=$k?>][file]" />
			<a href="#" class="sprite delete"></a>
			<div><a href="<?=$img?>" onclick="return hs.expand(this)"><?=$v['file']?></a></div>
			<?php
			foreach ($q['fields'] as $fname=>$ftype) {
				$n = a18n($fname);
				if ($ftype=='checkbox') {
					$checked = (isset($v[$fname]) && $v[$fname] == 1) ? ' checked="checked"' : '';
					?>
					'<br /><label><input name="<?=$q['key']?>[<?=$k?>][<?=$fname?>]" type="checkbox" value="1" <?=$checked?> /><span><?=$n?></span></label>
					<?php
				}
				//v1.1.25 - добавил селект для file_multi и simple
				elseif (is_array($ftype)) {
					?>
					<br /><span class="select"><select name="<?=$q['key']?>[<?=$k?>][<?=$fname?>]"><?=select(@$v[$fname],$ftype)?></select></span>
					<?php
				}
				else {
					?>
					<input class="input" name="<?=$q['key']?>[<?=$k?>][<?=$fname?>]" value="<?=htmlspecialchars(@$v[$fname])?>" placeholder="<?=$n?>" title="<?=$n?>" />
					<?php
				}
			}
			?>
		</li>
		<?php
		}
	}
	?>
	</ul>
	<div class="clear"></div>
</div>