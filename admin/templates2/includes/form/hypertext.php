<?php

$hypertext = $q['value'] ? json_decode($q['value'],true) : array();
if (!$hypertext) {
	$hypertext[1] = array(
		'type' => 'html',
		'n'=>1,
		'key'=>$q['key'],
		'content'=>'',
	);
}
?>
<div class="row hypertext">

<?php
foreach ($hypertext as $k=>$v) {
	$v['key'] = $q['key'];
	$v['n'] = $k;
	echo html_render('form/hypertext_'.$v['type'],$v);
}
?>
	<div class="hypertext_item col-xl-12" data-n="0">
		<?=html_array('form/hypertext_add')?>
	</div>
</div>
