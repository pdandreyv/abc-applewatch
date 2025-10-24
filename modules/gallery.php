<?php

$abc['galleries'] = mysql_select("
		SELECT *
		FROM gallery
		WHERE display = 1 AND img!=''
		ORDER BY `rank` DESC,name
	",'rows');
//одна запись
if ($u[2] OR count($abc['galleries'])==1) {
	if ($u[2]) {
		$id = intval(explode2('-',$u[2]));  //echo $id;
		$query = "
			SELECT *
			FROM gallery
			WHERE id = '".$id."' AND display = 1
			LIMIT 1
		";
		$gallery = mysql_select($query,'row');  // print_r($page);
		//переадреация на корректный урл
		if ($gallery AND $u[2]!=$gallery['id'].'-'.$gallery['url']) {
			die(header('location: /' . $modules['gallery'] . '/' . $page['id'] . '-' . $page['url'] . '/'));
		}
	}
	else $gallery = $abc['galleries'][0];
	if ($gallery) {
		$abc['page'] = $gallery;
		$abc['layout'] = 'gallery_text';
		$abc['breadcrumb'][] = array(
			'name'=>$gallery['name'],
			'url'=>get_url('gallery',$gallery)
		);
	}
	else $error++;
}