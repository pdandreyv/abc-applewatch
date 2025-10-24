<?=html_render('form/feedback')?>

<?php
if($abc['branches']) {
	echo html_render('shop/branch_map');
}