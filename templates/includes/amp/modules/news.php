<div class="content">
	<h1><?=@$abc['page']['h1']?$abc['page']['h1']:$abc['page']['name']?></h1>
	<?php
	//список новостей
	if (@$page['results']) {
		echo html_array('pagination/data',$page['results']);
		echo html_query('news/list',$page['results']['list'],'');
		echo html_array('pagination/data',$page['results']);
	}
	//страница одной новости
	else {
		echo html_array('news/text',$abc['page']);
	}
	?>
	<div class="clear"></div>
</div>