<?php
	$nbsp = '';
	for ($i = 1; $i<=$q['level']; $i++) {
		$nbsp.= ' &nbsp;';
	}
?>
	<div>
		<?=$nbsp?> :.. <a href="<?=get_url('page',$q)?>"><?=$q['name']?></a>
	</div>