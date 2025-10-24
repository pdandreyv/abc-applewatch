<?php

$query = "
	SELECT name,level,url,module
	FROM pages
	WHERE display = 1 AND noindex=0
	ORDER BY left_key
";

$abc['sitemap'] = mysql_select($query,'rows');

$abc['content'] = html_render('common/sitemap',$abc['sitemap']);