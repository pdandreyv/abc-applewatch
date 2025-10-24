<?php if ($i==1) {?>
<ul>
<?php } ?>

<?php if ($q['_active']==1) {?>
	<li class="active"><span class="a"><?=$q['name']?></span></li>
<?php } else {?>
	<li><a href="<?=$q['_url']?>" title="<?=$q['_title']?>"><?=$q['name']?></a></li>
<?php } ?>

<?php if ($i==$num_rows) {?>
</ul>
<?php } ?>