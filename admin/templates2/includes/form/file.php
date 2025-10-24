<div class="form-group col-xl-12 files <?=$q['type']?>" data-i="<?=$q['key']?>">
	<div class="data">
		<div class="img" data-img="<?=$q['img']?>" title="Для загрузки картинки переместите её в эту область">
			<img src="/_imgs/100x100<?=$q['img']?>" /><span>&nbsp;</span><input name="<?=$q['key']?>" type="hidden" value="<?=$q['file']?>" />
		</div>
		<div class="name"><?=$q['name']?></div>
		<div class="desc">
		<?php
		if ($q['is_file']) {
			?>
			<a href="#" class="delete"><i data-feather="trash-2"></i></a>
			<div><a href="<?=$q['img']?>" class="image-popup"><?=$q['file']?></a></div>
			<?php
		}
		?>
		</div>
		<a class="add_file btn btn-outline-secondary" title="Выбрать файл">
			выбрать
			<input type="file" title="выбрать файл" />
		</a>
	</div>
</div>