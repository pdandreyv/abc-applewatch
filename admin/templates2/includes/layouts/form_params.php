<?php
if (@$get['id']!='new') {
	?>
	<button type="button" class="close" style="margin-left: 5px;" data-placement="bottom" data-toggle="popover" data-content='
	<div style="text-aling:right">
	<?php
	if (@$post['created_at']) {
		?>создано <?=date2(@$post['created_at'],'%d.%m.%y %H:%M')?><?php
	}
	?><?php if (@$post['updated_at']) {
		?><br>обновлено <?=date2(@$post['updated_at'],'%d.%m.%y %H:%M')?><?php
	}
	//v1.2.122 просмотр на сайте - _view
	if (@$table['_view'] AND @$get['id']!='new') {
		?>
		<br>
		<a target="_blank" href="<?=get_url($table['_view'],$post)?>">посмотреть на сайте</a>
		<?php
	}
	if (@$get['id']) {
		?>
		<br>
		<a target="_blank" href="/admin.php?m=logs&module=<?=$module['table']?>&parent=<?=$get['id']?>">лог изменений</a>
		<?php
	}
	$connections = table_connections($delete);
	//dd($connections);
	if ($connections) {
		?><div>Связанные записи:<?php
		foreach ($connections as $q) {
			if (@$q['url']) {
				?><br>- <a href="<?=$q['url']?>"><?=$q['name']?></a><?php
			}
			else {
				?><br>- <?=$q['name']?><?php
			}
			?></div><?php
		}
	}
	?>
	</div>
	'><i data-feather="more-vertical"></i></button>
	<?php
}