<?php
//список заказов
if ($abc['orders']['list']) {
	echo html_render('pagination/data',$abc['orders']);
	echo html_render('order/list',$abc['orders']['list']);
	echo html_render('pagination/data',$abc['orders']);
}
//если нет заказов
else {
	echo i18n('common|msg_no_results');
}
