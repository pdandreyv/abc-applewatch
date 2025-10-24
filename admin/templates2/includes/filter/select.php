<div class="filter form-group col-xl-2">
	<select class="form-control" name="<?=$q['key']?>" onchange="top.location='admin.php?<?=$q['url']?>&<?=$q['key']?>='+this.value;">
		<?=select($q['value'], $q['query'], $q['default'])?>
	</select>
</div>
		