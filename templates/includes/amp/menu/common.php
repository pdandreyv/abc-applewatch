<? if ($i==1) {?>
<ul class="menu_common">
<? } ?>
	<li>
		<a href="<?= get_url('page',$q,'amp')?>" title="<?= $q['title'] ?>"><?= $q['name'] ?></a>
		<? if($q['module'] == 'shop'): ?>
		<?= html_query('menu/categories', 'SELECT * FROM `shop_categories` WHERE `display` = 1 AND level=1 ORDER BY `left_key` DESC, `name` ASC', '', 60*60) ?>
		<? endif; ?>
	</li>
<? if ($i==$num_rows) { ?>
	</ul>
<? } ?>