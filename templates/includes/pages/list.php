<?php if ($i==1) { ?>
<ul class="pages_list">
<?php } ?>
	<li><a href="<?=get_url('page',$q)?>">&ndash; <?=$q['name']?></a></li>
<?php if ($i==$num_rows) { ?>
</ul>
<?php } ?>