<div class="filter form-group col-xl-3">
	<input name="<?=$q['key']?>" type="text" class="form-control" placeholder="<?=a18n('search')?>" value="<?=htmlspecialchars(stripslashes_smart(isset($_GET[$q['key']]) ? $_GET[$q['key']] : ''))?>">
	<a href="admin.php?<?=$q['url']?>&<?=$q['key']?>=" class="sprite search">
		<i data-feather="search"></i>
	</a>
</div>