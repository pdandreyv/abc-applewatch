<div class="news_text">
	<div class="date"><?=$q['_date']?></div>
	<div><?=$q['_text']?></div>
	<?php if ($abc['news_prev']) {
		?>
		<a style="float:left" href="<?=get_url('news',$abc['news_prev'])?>">« <?=$abc['news_prev']['name']?></a>
		<?php
	}
	if ($abc['news_next']) {
		?>
		<a style="float:right" href="<?=get_url('news',$abc['news_next'])?>"><?=$abc['news_next']['name']?> »</a>
		<?php
	}
	?>
</div>
