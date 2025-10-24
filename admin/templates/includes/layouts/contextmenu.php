<?php
if (is_array($form)) {
?>
	<div id="contextmenu">
		<a href="#" class="edit"><b class="sprite edit"></b><span>редактировать</span></a>
		<?php
	if (isset($tabs) && is_array($tabs)) {
		foreach ($tabs as $key=>$val) {
?>
		<a href="#<?=$key?>" class="tabs"><b></b><span><?=$val?></span></a>
			<?php
		}
	}
?>
		<div class="spacer"></div>
<?php
	foreach ($table as $key=>$val) {
		if ($val==='boolean' OR $val==='display') {
			$k = in_array($key,$config['boolean']) ? $key : 'boolean';
?>
		<a href="#" class="boolean" data-name="<?=$key?>" data-key="<?=$k?>"><b class="sprite js_boolean"></b><span><?=a18n($key)?></span></a>
			<?php
		}
	}
?>
		<div class="spacer"></div>
		<a href="#" class="delete"><b class="sprite delete"></b><span>удалить</span></a>
	</div>
	<?php
}
?>
