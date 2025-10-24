<?php

define('ROOT_DIR', dirname(__FILE__).'/');

//загузка файла во временную директорию
$file = @$_FILES['temp'];
if ($file AND is_array($file)) {
	$file['temp'] = rand(1000000,9999999);
	$file['name'] = strtolower(trunslit($file['name'])); //название файла
	$path = 'files/temp/'.$file['temp']; //папка от корня основной папки
	$root = ROOT_DIR.$path.'/';
	if (is_dir($root) || mkdir ($root,0755,true)) { //создание папок для файла
		copy($file['tmp_name'],$root.$file['name']);
		echo $file['temp'];
	}
}

//удаление старых файлов
$root = ROOT_DIR.'files/temp/';
$time = 60*60*24; //сутки
if ($handle = opendir($root)) {
	while (false !== ($dir = readdir($handle))) {
		if (strlen($dir)>2 && is_dir($root.$dir)) {
			if ((time() - $time) > filemtime($root.$dir)) delete_all($root.$dir,true);
		}
	}
}

/**
 * преобразование кирилицы в транслит
 * @param string $str - строка текста, обычно название
 * @return string - транлит
 */
function trunslit($str){
	$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
	$str = str_replace(
		array('a','o','u','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я','і','ї','є'),
		array('a','o','u','a','b','v','g','d','e','e','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','shch','','y','','e','yu','ya','i','yi','e'),
		$str
	);
	$str = preg_replace('~[^-a-z0-9_.]+~u', '-', $str);	//удаление лишних символов
	$str = preg_replace('~[-]+~u','-',$str);			//удаление лишних -
	$str = trim($str,'-');								//обрезка по краям -
	$str = trim($str,'.');
	return $str;
}

?>