<?php

/*
 * скрипт для выполнения прямых запросов к базе
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');


$ip = get_ip();
$ip = '217.113.120.51';

$data = geo_data ($ip);

echo $ip;
dd($data);
dd(@$config['queries']);

/*
$config['mysql_database'] = '5legko';
mysql_connect_db();

$cities = mysql_select("SELECT id, name1_1 FROM location_regions",'rows');
mysql_close_db();

$config['mysql_database'] = 'abc';
mysql_connect_db();

foreach($cities as $k=>$v) {
	$data = array(
		'id'=>$v['id'],
		'name_where'=>$v['name1_1']
	);
	mysql_fn('update','geo_regions',$data);
}
*/


