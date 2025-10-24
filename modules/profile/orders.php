<?php

if ($u[3]) {//одна запись
	$query = "
		SELECT o.*,ot.name".$lang['i']." ot_name,ot.text".$lang['i']." ot_text
		FROM orders o
		LEFT JOIN order_types ot ON ot.id = o.type
		WHERE o.user=".$user['id']." AND o.id = '".intval($u[3])."'
		LIMIT 1
	";
	if ($order = mysql_select($query,'row')) {
		$abc['page'] = array_merge($abc['page'],$order);
		$abc['page']['name'] = $abc['page']['title'] = $abc['page']['description'] = i18n('basket|order_name').' № '.$order['id'];
		$abc['breadcrumb'][] = array(
			'name'=>$abc['page']['name'],
			'url'=>get_url('order',$abc['page'])
		);

		$html['layout'] = 'order';
	}
	else $error++;
}
else {//список записей
	$query = "
		SELECT o.*,ot.name".$lang['i']." ot_name
		FROM orders o
		LEFT JOIN order_types ot ON ot.id = o.type
		WHERE o.user = '".$user['id']."'
		ORDER BY o.created_at DESC
	"; //echo $query
	$abc['orders'] = mysql_data(
		$query,
		false,
		10,
		@$_GET['n']
	);
}