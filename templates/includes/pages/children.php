<?php if ($i==1) { ?>
<ul class="pages_children">
<?php } ?>
	<li><a href="<?=get_url('page',$q)?>"><i class="icon-caret-right"></i> <?=$q['name']?></a></li>
<?php if ($i==$num_rows) { ?>
</ul>
<?php } ?>