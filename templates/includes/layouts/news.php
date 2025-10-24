<?php
//список новостей
if (@$abc['news']) {
	if ($abc['news']['list']) {
		echo html_render('pagination/data', $abc['news']);
		echo html_render('news/list', $abc['news']['list']);
		echo html_render('pagination/data', $abc['news']);
	}
	else echo i18n('common|msg_no_results');
}
//страница одной новости
else {
	echo html_render('news/text',$abc['page']);
}