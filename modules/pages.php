<?php

//404 если есть $u[2]
if ($u[2]) {
	$error++;
}
else {
	//список внутренних страниц
	$abc['children'] = mysql_select("
		SELECT * FROM pages
		WHERE parent = '" . $abc['page']['id'] . "' AND display = 1
		ORDER BY left_key
	",'rows'); //echo $query;

	//список страниц этого же уровня
	$abc['siblings'] = mysql_select("
		SELECT * FROM pages
		WHERE parent = '" . $abc['page']['parent'] . "' AND display = 1
		ORDER BY left_key
	",'rows',''); //echo $query;

	//v1.4.66 - hypertext
	$abc['page']['text'] = hyppertext($abc['page'],'pages');
}
