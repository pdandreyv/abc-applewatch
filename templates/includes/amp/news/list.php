<?php
$q['text'] = strip_tags($q['text']);
//$text = mb_strlen($q['text'], "UTF-8")>30 ? mb_substr($q['text'],0,30,"UTF-8").'..' : $q['text'];
$text = truncate($q['text'],300,'..',false);
$title = filter_var($q['name'],FILTER_SANITIZE_STRING);
$url = get_url('news',$q,'amp');
?>
<div class="news_list">
	<div class="date"><?=date2($q['date'],'%d.%m.%y')?></div>
	<div class="name">
		<a href="<?=$url?>" title="<?=$title?>"><?=$q['name']?></a>
	</div>
	<div class="text">
		<?=$text?>
	</div>
	<div class="next">
		<a href="<?=$url?>"><?=i18n('common|wrd_more')?></a>
	</div>
</div>