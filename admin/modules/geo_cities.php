<?php

/*
 *  v1.4.17 - сокращение параметров form
 */

$a18n['name2'] = 'name';

$countries = mysql_select("
	SELECT id, name
	FROM geo_countries
	ORDER BY `rank` DESC,name
","array");
$regions = array();
if (@$_GET['country'] OR @$post['country']) {
	$country = @$_GET['country'] ? intval($_GET['country']) : intval($post['country']);
}
else $country = key($countries);
$regions = mysql_select("
	SELECT id, name
	FROM geo_regions
	WHERE country=" . $country . "
	ORDER BY `rank` DESC,name
", "array");


$table = array(
	'id'		=>	'rank:desc name id iso',
	'name'		=>	'',
	'name2'		=>	'',
	'uid'       =>	'',
	'region'    => $regions,
	//'country'   => $countries,
	'rank'      => '',
	'display'	=> 'boolean'
);

$filter[] = array('country',$countries,NULL,true);
if($regions) $filter[] = array('region',$regions,'-'.a18n('geo_regions').'-');
$filter[] = array('search');

if (@$_GET['country']) $where.= " AND geo_cities.country=".intval($_GET['country']);
else $where.= " AND geo_cities.country=".key($countries);
if (@$_GET['region']) $where.= " AND geo_cities.region=".intval($_GET['region']);
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(geo_cities.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(geo_cities.name2) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT *
	FROM geo_cities
	WHERE 1 $where
";

$form[] = array('input td5','name');
$form[] = array('input td1','rank');
$form[] = array('checkbox','display');

$form[] = array('input td5','name2');
$form[] = array('input td1','uid');
$form[] = array('select td3','country',array('value'=>array(true,$countries)));
$form[] = array('select td3','region',array('value'=>array(true,$regions)));

$form[] = array('google_map','');

$form[] = array('seo','seo url');

html_sources('footer','google_map');

$content .= '<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	$(document).on("change","form select[name=country]",function(){
		var box = $("form select[name=region]");
		$.post(
			"admin.php?m=geo_cities&u=get_regions",
			{"country":$(this).val()},
			function(response){
				$(box).html("");
				if (response.regions) {
					$(box).html(response.regions);
				}
			}
		);
		return false;
	});
})	
</script>';
