<?php
$text =  str_replace(array('href="/','src="/'),array('href="'.$config['http_domain'].'/','src="'.$config['http_domain'].'/'),$q['text']);
$top =  str_replace(array('href="/','src="/'),array('href="'.$config['http_domain'].'/','src="'.$config['http_domain'].'/'),i18n('subscribe|top'));
$bottom =  str_replace(array('href="/','src="/'),array('href="'.$config['http_domain'].'/','src="'.$config['http_domain'].'/'),i18n('subscribe|bottom'));
?>
<body style="margin:0; padding:0; font:14px/18px Arial">
<div style="margin:auto; width:800px; padding:20px">

	<div style=""><?=$top?></div>
	<div style="width:0px; height:10px; font:0px/0px Arial; clear:both"></div>

	<div style=""><?=$text?></div>
	<div style="width:0px; height:10px; font:0px/0px Arial; clear:both"></div>

	<div style=""><?=$bottom?></div>
	<div style="width:0px; height:10px; font:0px/0px Arial; clear:both"></div>

	<div style="font:11px/15px Arial">
		<?=i18n('subscribe|letter_failure_str')?>
		<a href="<?=$config['http_domain'].get_url('subscribe','unsubscribe',$q)?>"><?=i18n('subscribe|letter_failure_link')?></a>
	</div>

</div>
</body>