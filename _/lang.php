<?php

/*
 * скрипт для изменения слов в словаре
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');

//echo ROOT_DIR;

mysql_connect_db();

//добавление слов
$langs = array(
	'shop' =>	array(
		'qwert' =>	'йцуке'

	),
	'common' =>	array(
		'qwert' =>	'йцуке',
	)
);
$id = 1;
foreach ($langs as $key=>$val) {
	echo '<b>'.$key.'</b><br />';
	print_r($val);
	echo '<br /><br />';
	$lang = array();
	include(ROOT_DIR.'files/languages/'.$id.'/dictionary/'.$key.'.php');
	$lang[$key] = array_merge($lang[$key],$val);
	//print_r($lang[$key]);
	$str = '<?php'.PHP_EOL;
	$str.= '$lang[\''.$key.'\'] = array('.PHP_EOL;
	foreach ($lang[$key] as $k=>$v) {
		$str.= "	'".$k."'=>'".str_replace("'","\'",$v)."',".PHP_EOL;
	}
	$str.= ');';
	$str.= '?>';
	$fp = fopen(ROOT_DIR.'files/languages/'.$id.'/dictionary/'.$key.'.php', 'w');
	fwrite($fp,$str);
	fclose($fp);
}


/*
//из jsona c serilize
$data = mysql_select("SELECT id,dictionary FROM languages",'rows');
foreach ($data as $k=>$v) {
	$dictionary = json_decode($v['dictionary'],true);
	$v['dictionary'] = serialize($dictionary);
	mysql_fn('update','languages',$v);
}
$data = mysql_select("SELECT id,images FROM shop_products",'rows');
$data = mysql_select("SELECT id,`values` FROM shop_parameters",'rows');
$data = mysql_select("SELECT id,basket FROM orders",'rows');
$data = mysql_select("SELECT id,parameters FROM shop_categories",'rows');
$data = mysql_select("SELECT id,fields FROM users",'rows');
$data = mysql_select("SELECT id,access_admin FROM user_types",'rows');
*/


//обновление слов
/*
$data = mysql_select("SELECT id,dictionary FROM languages WHERE id=1",'row');
$dictionary = unserialize($data['dictionary'],true);
$dictionary['wrd_tutor_found']='Ученик нашел репетитора';
$data['dictionary'] = serialize($dictionary);
mysql_fn('update','languages',$data);
/**/

/*
//пересохранение словаря с массива в файлы
$data = mysql_select("SELECT id,dictionary FROM languages WHERE id=1",'row');
$dictionary = unserialize($data['dictionary']);
//print_r($dictionary);
$lang = array();
foreach ($dictionary as $k=>$v) {
	//echo '-'.$k.'-'.substr($k,5).'<br />';
	$str = substr($k,0,4);
	if ($str=='shop') {
		$lang['shop'][substr($k,5)] = $v;
	}
	elseif ($str=='revi') {
		$lang['shop'][$k] = $v;
	}
	elseif ($str=='prof') {
		$lang['profile'][substr($k,8)] = $v;
	}
	elseif ($str=='bask') {
		$lang['basket'][substr($k,7)] = $v;
	}
	elseif ($str=='subs') {
		$lang['subscribe'][substr($k,10)] = $v;
	}
	elseif ($str=='mark') {
		$lang['market'][substr($k,7)] = $v;
	}
	elseif ($str=='feed') {
		$lang['feedback'][substr($k,9)] = $v;
	}
	elseif ($str=='msg_') {
		$lang['validate'][substr($k,4)] = $v;
	}
	else $lang['common'][$k] = $v;

}
foreach ($lang as $key=>$val) {
	$str = '<?php'.PHP_EOL;
	$str.= '$lang[\''.$key.'\'] = array('.PHP_EOL;
	foreach ($val as $k=>$v) {
		$str.= "	'".$k."'=>'".str_replace("'","\'",$v)."',".PHP_EOL;
	}
	$str.= ');';
	$str.= '?>';
	$fp = fopen(ROOT_DIR.'files/languages/1/dictionary/'.$key.'.php', 'w');
	fwrite($fp,$str);
	fclose($fp);
}
print_r($lang);
/**/


?>
