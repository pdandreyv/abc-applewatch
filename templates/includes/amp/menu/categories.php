<? if ($i==1) { ?>
	<ul class="menu_categories">
<? } ?>
	<li>
		<a href="<?= get_url('shop_category',$q,'amp') ?>" title="<?= $q['title'] ?>"><?= $q['name'] ?></a>
	</li>
<? if ($i==$num_rows) { ?>
	</ul>
<? } ?>