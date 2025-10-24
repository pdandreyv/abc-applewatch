<?php
$text = template_img('news',$q);
$text = template_video($text,$q['video']);
?>
<div class="news_text">
	<div class="date"><?=date2($q['date'],'%d.%m.%y')?></div>
	<div><?=$text?></div>
	<?php if ($abc['news_prev']) {
		?>
		<a style="float:left" href="<?=get_url('news',$abc['news_prev'],'amp')?>">« <?=$abc['news_prev']['name']?></a>
		<?php
	}
	if ($abc['news_next']) {
		?>
		<a style="float:right" href="<?=get_url('news',$abc['news_next'],'amp')?>"><?=$abc['news_next']['name']?> »</a>
		<?php
	}
	?>
</div>
