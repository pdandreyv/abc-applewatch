<div id="window">
<form id="form<?=$get['id']?>" class="form" method="post" enctype="multipart/form-data" action="<?=setUrlParams($_SERVER['REQUEST_URI'],array('u'=>'edit','id'=>false))?>">
	<?php if ($module['one_form']==false) {?>
	<a class="sprite close" href="#" title="<?=a18n('close_not_save')?>"></a>
	<?php } ?>
	<div class="form_head corner_top gradient">
		ID:<span data-name="id"><?=$get['id']?></span>
		<?php
		//v1.2.122 просмотр на сайте - _view
		if (@$table['_view'] AND $get['id']!='new') {?>
		<a href="<?=get_url($table['_view'],$post)?>"><?=a18n('view')?></a>
		<?php } ?>
		<?=html_delete($delete)?>
	</div>
	<?php
	if (count($tabs)>0) {
		?>
	<ul class="bookmarks">
		<?php
		$i = key($tabs);
		foreach ($tabs as $k=>$v) {
			?>
		<li class="corner_top<?=($i==$k ? ' active' : '')?>"><a href="#<?=$k?>" data-i="<?=$k?>"><?=$v?></a></li>
			<?php
		}
		?>
	</ul>
		<?php
	}
	?>
	<div class="form_content">
	<?php
	if (is_array($form)) {
		if (count($tabs)>0) {
			foreach ($tabs as $k=>$v) if (isset($form[$k]) && is_array($form[$k])) {
				?>
				<div class="tab"  data-i="<?=$k?>">
				<?php
				foreach ($form[$k] as $k2=>$v2) {
					if (is_array($v2)) echo call_user_func_array(preg_match('/mysql|simple|file|file_multi/',$v2[0]) ? 'form_file' : 'form', $v2);
					else {
						if ($v2=='clear') echo '<div class="clear"></div>';
						else echo $v2;
					}
				}
				?>
				</div>
				<?php
			}
		} else {
			foreach ($form as $k=>$v) {
				//if (is_array($v) AND preg_match('/mysql|simple|file|file_multi/',$v[0])) dd($v);
				if (is_array($v)) echo call_user_func_array(preg_match('/mysql|simple|file|file_multi/',$v[0]) ? 'form_file' : 'form', $v);
				else {
					if ($v=='clear') echo '<div class="clear"></div>';
					else echo $v;
				}
			}
		}
	}
	?>
		<input name="nested_sets[on]" type="hidden" value="0" />
		<div class="clear"></div>
	</div>
	<div class="form_footer corner_bottom">
		<?php if ($module['one_form']==false) {?>
		<div class="button red close_form" title="Сохранить изменения и закрыть форму"><input type="submit" value="Сохранить и закрыть"/></div>
		<?php } ?>
		<?php
		if ($module['save_as']==true) {
			?>
			<div class="button red save_as" title="Сохранить как новую запись"><input type="submit" value="Сохранить как"/></div>
			<?php
		}
		?>
		<div class="button red" title="Сохранить изменения и о оставить форму"><input type="submit" value="Сохранить"/></div>
		<span class="success">изменения внесены!</span>
		<span class="error"></span>
		<div class="clear"></div>
	</div>
</form>
</div>
