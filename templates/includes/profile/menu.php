<ul class="profile_menu">
<?php foreach ($q as $k=>$v) { ?>
<li><a href="<?=get_url('profile',$k)?>"><i class="icon-caret-right"></i> <?=$v?></a></li>
<?php } ?>
</ul>
