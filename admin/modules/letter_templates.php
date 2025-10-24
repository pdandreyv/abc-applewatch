<?php

/**
 * v1.4.14 - event_func
 * v1.4.17 - сокращение параметров form
 * v1.4.71 - косяк при быстром редактировании
 */

$a18n['name'] = '';

//варианты шаблона письма /templates/includes/letter_templates/
$template = array(
	1 => 'основной шаблон'
);

$tabs = mysql_select("SELECT id,name FROM languages ORDER BY `rank` DESC",'array');

if ($get['u']=='edit') {
	foreach ($tabs as $k=>$v) {
		unset($post['subject'.$k],$post['text'.$k]);
	}
}

//v1.4.14 - event_func
function event_change_letter_templates($q) {
	global $tabs;

	$path = ROOT_DIR.'files/letter_templates/'.$q['name'].'/';
	if (is_dir($path) || mkdir($path,0755,true)) {
		foreach ($tabs as $k=>$v) {
			//v1.4.71 исключение для быстрого редактирования
			if (@$_POST['text'.$k]) {
				if (is_dir($path . $k) || mkdir($path . $k, 0755, true)) {
					$fp = fopen($path . $k . '/subject.php', 'w');
					fwrite($fp, @$_POST['subject' . $k]);
					fclose($fp);
					$fp = fopen($path . $k . '/text.php', 'w');
					fwrite($fp, @$_POST['text' . $k]);
					fclose($fp);
				}
			}
		}
	}
}

$table = array(
	'id'		=>	'name id',
	//'template'	=>	$template,
	'name'		=>	'{name}',
	'sender_name'	=>	'',
	'sender'	=>	'',
	'receiver'	=>	'',
	'description'	=>	''
);

$query = "
	SELECT *,
	IF (sender_name='','".$config['sender_name']."',sender_name) sender_name,
	IF (sender='','".$config['sender']."',sender) sender,
	IF (receiver='','".$config['receiver']."',receiver) receiver
	FROM letter_templates
	WHERE 1
";


if (($get['u']=='form' OR $get['u']=='') AND $get['id']>0) {
	if (@$post['name']) {
		foreach ($tabs as $k => $v) {
			$path = ROOT_DIR . 'files/letter_templates/' . $post['name'] . '/';
			$post['subject' . $k] = '';
			if (is_file($path . $k . '/subject.php')) {
				$handle = @fopen($path . $k . '/subject.php', "r");
				if ($handle) {
					while (($buffer = fgets($handle, 4096)) !== false) $post['subject' . $k] .= $buffer;
					fclose($handle);
				}
			}
			$post['text' . $k] = '';
			if (is_file($path . $k . '/text.php')) {
				$handle = @fopen($path . $k . '/text.php', "r");
				if ($handle) {
					while (($buffer = fgets($handle, 4096)) !== false) $post['text' . $k] .= $buffer;
					fclose($handle);
				}
			}
		}
	}
}

$form[1][] = array('input td3','name',);
//$form[1][] = array('select td3','template',array('value'=>array(true,$template,'')));
$form[1][] = array('input td6','description');
$form[1][] = array('input td3','sender_name',array('help'=>'имя отправителя письмо с сервера','attr'=>'placeholder="'.$config['sender_name'].'"'));
$form[1][] = array('input td3','sender',array('help'=>'email отправителя письмо с сервера','attr'=>'placeholder="'.$config['sender'].'"'));
$form[1][] = array('input td3','receiver',array('help'=>'email получателя письма, если он не задан в модуле','attr'=>'placeholder="'.$config['receiver'].'"'));
$form[1][] = 'clear';

foreach ($tabs as $k=>$v) {
	$form[$k][] = array('input td12','subject'.$k,array('name'=>'тема письма'));
	$form[$k][] = '<div>Настройка текста в шапке и подвале письем в <a target="_blank" href="?m=languages#6">словаре</a></div>';
	$form[$k][] = array('textarea td12','text'.$k,array('name'=>'текст письма','attr'=>'style="height:400px"'));
}

$content = '<br />Здесь можно указать индивидуально отправителя и получателя письма, глобальная настройка <a href="/admin.php?m=config">тут</a>';