<div id="table">
	<div class="pagination"><?=html_render('pagination/default',$q)?></div>
	<table class="table <?=$q['type']?>" data-module="<?=$q['module']?>">
		<?=html_array('table/thead',$q) ?>
		<tbody>
			<?=html_array('table/row',$q) ?>
		</tbody>
	</table>
	<div class="pagination"><?=html_render('pagination/default',$q)?></div>
</div>