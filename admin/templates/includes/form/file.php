<div class="files <?=$q['type']?>" data-i="<?=$q['key']?>">
	<div class="data">
		<div class="img" data-img="<?=$q['img']?>" title="Для загрузки картинки переместите её в эту область">
			<img src="/_imgs/100x100<?=$q['img']?>" /><span>&nbsp;</span><input name="<?=$q['key']?>" type="hidden" value="<?=$q['file']?>" />
		</div>
		<div class="name"><?=$q['name']?></div>
		<div class="desc">
		<?php
		if ($q['is_file']) {
			?>
			<a href="#" class="sprite delete"></a>
			<div><a href="<?=$q['img']?>" onclick="return hs.expand(this)"><?=$q['file']?></a></div>
			<?php
		}
		?>
		</div>
		<a class="add_file button green" title="Выбрать файл">
			<span><span class="sprite plus"></span>выбрать</span>
			<input type="file" title="выбрать файл" />
		</a>
		<div class="clear"></div>
	</div>
</div>