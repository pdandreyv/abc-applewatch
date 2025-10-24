
<div id="table">
	<div class="pagination corner_top"><?=html_render('pagination/default',$q)?></div>
	<div class="clear"></div>
	<table cellspacing="1" cellpadding="0" class="table <?=$q['type']?>" data-module="<?=$q['module']?>">
		<?=html_array('table/thead',$q) ?>
		<tbody>
			<?=html_array('table/row',$q) ?>
		</tbody>
	</table>
	<div class="pagination corner_bottom"><?=html_render('pagination/default',$q)?></div>
</div>