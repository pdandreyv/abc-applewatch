<?php

$news = false;

//обработка урл amp-страниц
if (@$_GET['view']=='amp') {
	$config['amp'] = 1;
}

//404 если есть $u[3]
if ($u[3]) {
	$error++;
}
//одна запись
elseif ($u[2]) {
	if ($news = mysql_select("
		SELECT *
		FROM news
		WHERE url = '".mysql_res($u[2])."' AND display = 1
	",'row')) {
		$abc['page'] = array_merge($abc['page'],$news);
		//канонический урл
		$abc['page']['canonical'] = get_url('news',$news);
		$abc['page']['amp'] = get_url('news',$news,'amp');
		//предыдущая
		$abc['news_prev'] = mysql_select("
			SELECT * FROM news
			WHERE date<'".$abc['page']['date']."' AND display=1
			ORDER BY date DESC LIMIT 1
		",'row');
		//следующая
		$abc['news_next'] = mysql_select("
			SELECT * FROM news
			WHERE date>'".$abc['page']['date']."' AND display=1
			ORDER BY date LIMIT 1
		",'row');
		//хлебные крошки
		$abc['breadcrumb'][] = array(
			'name'=>$abc['page']['name'],
			'url'=>get_url('news',$abc['page'])
		);
	}
	else $error++;
}
//список записей
else {
	$abc['news'] = mysql_data(
		"SELECT * FROM news WHERE display = 1 ORDER BY date DESC",
		false,
		10,
		@$_GET['n']
	);

	//канонический урл
	$abc['page']['canonical'] = get_url('news');
	$abc['page']['amp'] = get_url('news',false,'amp');

	//v.1.2.64 пагинатор на ajax
	if (@$_POST['action']=='ajax') {
		html_render('layouts/news');
		die();
	}
}