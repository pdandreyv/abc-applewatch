<h4>Подпишитесь на наши обновления</h4>
<ul>
<?php
foreach ($q as $k=>$v) {?>
	<li><a target="_blank" href="<?=$v['url']?>"><img alt="" class="social_icon" src="/templates/src/imgs/socicon-custom/<?=$v['type']?>.png"/><?=$v['type']?></a></li>
<?php } ?>
</ul>
