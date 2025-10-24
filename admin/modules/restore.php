<?php

//восстановление базы данных
/*
 * история изменений
 * v1.1.20 - исправление ошибки в дампере
 * v1.4.0 - html_render в админке
 */

$connect = mysql_connect_db();

require_once(ROOT_DIR . $config['style'].'/includes/layouts/_template.php');

// Путь и URL к файлам бекапа
define('PATH', 'admin/backup/');
define('URL',  'admin/backup/');
// Максимальное время выполнения скрипта в секундах
// 0 - без ограничений
define('TIME_LIMIT', 600);
// Ограничение размера данных доставаемых за одно обращения к БД (в мегабайтах)
// Нужно для ограничения количества памяти пожираемой сервером при дампе очень объемных таблиц
define('LIMIT', 1);
// mysql сервер
define('DBHOST', 'localhost');
// Базы данных, если сервер не разрешает просматривать список баз данных,
// и ничего не показывается после авторизации. Перечислите названия через запятую
//define('DBNAMES', '');
// Кодировка соединения с MySQL
// auto - автоматический выбор (устанавливается кодировка таблицы), cp1251 - windows-1251, и т.п.
define('CHARSET', 'auto');
// Кодировка соединения с MySQL при восстановлении
// На случай переноса со старых версий MySQL (до 4.1), у которых не указана кодировка таблиц в дампе
// При добавлении 'forced->', к примеру 'forced->cp1251', кодировка таблиц при восстановлении будет принудительно заменена на cp1251
// Можно также указывать сравнение нужное к примеру 'cp1251_ukrainian_ci' или 'forced->cp1251_ukrainian_ci'
define('RESTORE_CHARSET', 'cp1251');
// Включить сохранение настроек и последних действий
// Для отключения установить значение 0
define('SC', 1);
// Типы таблиц у которых сохраняется только структура, разделенные запятой
define('ONLY_CREATE', 'MRG_MyISAM,MERGE,HEAP,MEMORY');
// Глобальная статистика
// Для отключения установить значение 0
define('GS', 0);


// Дальше ничего редактировать не нужно

$is_safe_mode = ini_get('safe_mode') == '1' ? 1 : 0;
if (!$is_safe_mode && function_exists('set_time_limit')) set_time_limit(TIME_LIMIT);

$timer = array_sum(explode(' ', microtime()));
ob_implicit_flush();
error_reporting(E_ALL);

$auth = 1;
$error = '';

if (!file_exists(PATH) && !$is_safe_mode) {
	mkdir(PATH, 0777) || trigger_error("Не удалось создать каталог для бекапа", E_USER_ERROR);
}

$SK = new dumper();
define('C_DEFAULT', 1);
define('C_RESULT', 2);
define('C_ERROR', 3);
define('C_WARNING', 4);

if (isset($_GET['delete']) && $_GET['delete']) {
	$file = stripslashes_smart($_GET['delete']);
	$file = preg_replace('~[^-a-z0-9_.]+~u', '', $file);
	if (is_file(ROOT_DIR.'admin/backup/'.$file)) {
		if (unlink(ROOT_DIR.'admin/backup/'.$file))
			$content.= '<b>файл удален!</b>';
		else $content.= '<b>нет доступа к файлу!</b>';
	}
	else $content.= '<b>нет такого файла!</b>';
}

if (isset($_POST['file'])) {
	//1.2.104 - лог восстановлений бекапов
	log_add('restore.txt',$config['datetime'].'; user:'.$user['id']. '; ip:'.get_ip().'; '.$_POST['file']);
	$SK->restore();
}
else $SK->main();

mysqli_close($connect);

echo "<SCRIPT>document.getElementById('timer').innerHTML = '" . round(array_sum(explode(' ', microtime())) - $timer, 4) . " сек.'</SCRIPT>";

class dumper {
	function __construct() {
		global $connect;
		if (file_exists(PATH . "dumper.cfg.php")) {
			include(PATH . "dumper.cfg.php");
		}
		else{
			$this->SET['last_action'] = 0;
			$this->SET['last_db_backup'] = '';
			$this->SET['tables'] = '';
			$this->SET['comp_method'] = 2;
			$this->SET['comp_level']  = 7;
			$this->SET['last_db_restore'] = '';
		}
		$this->tabs = 0;
		$this->records = 0;
		$this->size = 0;
		$this->comp = 0;

		// Версия MySQL вида 40101
		preg_match("/^(\d+)\.(\d+)\.(\d+)/", mysqli_get_server_info($connect), $m);
		$this->mysql_version = sprintf("%d%02d%02d", $m[1], $m[2], $m[3]);

		$this->only_create = explode(',', ONLY_CREATE);
		$this->forced_charset  = false;
		$this->restore_charset = $this->restore_collate = '';
		if (preg_match("/^(forced->)?(([a-z0-9]+)(\_\w+)?)$/", RESTORE_CHARSET, $matches)) {
			$this->forced_charset  = $matches[1] == 'forced->';
			$this->restore_charset = $matches[3];
			$this->restore_collate = !empty($matches[4]) ? ' COLLATE ' . $matches[2] : '';
		}
	}

	function restore(){
		global $connect;
		if (!isset($_POST)) {$this->main();}
		set_error_handler("SXD_errorHandler");
		$buttons = '';//"<INPUT ID=back TYPE=button VALUE='Вернуться' DISABLED onClick=\"history.back();\">";
		echo tpl_page(tpl_process("Восстановление БД из резервной копии"), $buttons);

		$this->SET['last_action']	= 1;
		$this->SET['last_db_restore'] = isset($_POST['db_restore']) ? $_POST['db_restore'] : '';
		$file						= isset($_POST['file']) ? $_POST['file'] : '';
		$this->fn_save();
		$db = "data_base";//$db = $this->SET['last_db_restore'];

		/*if (!$db) {
			echo tpl_l("ОШИБКА! Не указана база данных!", C_ERROR);
			echo tpl_enableBack();
			exit;
		}*/
		echo tpl_l("Подключение к БД `{$db}`.");
		//mysql_select_db($db) or trigger_error ("Не удается выбрать базу данных.<BR>" . mysqli_error($connect), E_USER_ERROR);

		// Определение формата файла
		if(preg_match("/^(.+?)\.sql(\.(bz2|gz))?$/", $file, $matches)) {
			if (isset($matches[3]) && $matches[3] == 'bz2') {
				$this->SET['comp_method'] = 2;
			}
			elseif (isset($matches[2]) &&$matches[3] == 'gz'){
				$this->SET['comp_method'] = 1;
			}
			else{
				$this->SET['comp_method'] = 0;
			}
			$this->SET['comp_level'] = '';
			if (!file_exists(PATH . "/{$file}")) {
				echo tpl_l("ОШИБКА! Файл не найден!", C_ERROR);
					echo tpl_enableBack();
				exit;
  			}
			echo tpl_l("Чтение файла `{$file}`.");
			$file = $matches[1];
		}
		else{
			echo tpl_l("ОШИБКА! Не выбран файл!", C_ERROR);
			echo tpl_enableBack();
			exit;
		}
		echo tpl_l(str_repeat("-", 60));
		$fp = $this->fn_open($file, "r");
		$this->file_cache = $sql = $table = $insert = '';
		$is_skd = $query_len = $execute = $q =$t = $i = $aff_rows = 0;
		$limit = 300;
		$index = 4;
		$tabs = 0;
		$cache = '';
		$info = array();

		// Установка кодировки соединения
		if ($this->mysql_version > 40101 && (CHARSET != 'auto' || $this->forced_charset)) { // Кодировка по умолчанию, если в дампе не указана кодировка
			mysqli_query($connect,"SET NAMES '" . $this->restore_charset . "'") or trigger_error ("Неудается изменить кодировку соединения.<BR>" . mysqli_error($connect), E_USER_ERROR);
			echo tpl_l("Установлена кодировка соединения `" . $this->restore_charset . "`.", C_WARNING);
			$last_charset = $this->restore_charset;
		}
		else {
			$last_charset = '';
		}
		$last_showed = '';
		while(($str = $this->fn_read_str($fp)) !== false){
			if (empty($str) || preg_match("/^(#|--)/", $str)) {
				if (!$is_skd && preg_match("/^#SKD101\|/", $str)) {
					$info = explode("|", $str);
					//v1.1.20 - ошибка деления на 0, если дамп без данных
					if ($info[4]>0) echo tpl_s(0, $t / $info[4]);
					$is_skd = 1;
				}
				continue;
			}
			$query_len += strlen($str);

			//v1.1.20 - исправление бага когда менжу INSERT и INTO могут быть еще значения
			if (!$insert && preg_match("/^(INSERT .*INTO `?([^` ]+)`? .*?VALUES)(.*)$/i", $str, $m)) {
				if ($table != $m[2]) {
					$table = $m[2];
					$tabs++;
					$cache .= tpl_l("Таблица `{$table}`.");
					$last_showed = $table;
					$i = 0;
					if ($is_skd)
						echo tpl_s(100 , $t / $info[4]);
				}
				$insert = $m[1] . ' ';
				$sql .= $m[3];
				$index++;
				$info[$index] = isset($info[$index]) ? $info[$index] : 0;
				$limit = round($info[$index] / 20);
				$limit = $limit < 300 ? 300 : $limit;
				if ($info[$index] > $limit){
					echo $cache;
					$cache = '';
					echo tpl_s(0 / $info[$index], $t / $info[4]);
				}
			}
			else{
				$sql .= $str;
				if ($insert) {
					$i++;
					$t++;
					if ($is_skd && $info[$index] > $limit && $t % $limit == 0){
						echo tpl_s($i / $info[$index], $t / $info[4]);
					}
				}
			}

			if (!$insert && preg_match("/^CREATE TABLE (IF NOT EXISTS )?`?([^` ]+)`?/i", $str, $m) && $table != $m[2]){
				$table = $m[2];
				$insert = '';
				$tabs++;
				$is_create = true;
				$i = 0;
			}
			if ($sql) {
				if (preg_match("/;$/", $str)) {
					$sql = rtrim($insert . $sql, ";");
					if (empty($insert)) {
						if ($this->mysql_version < 40101) {
							$sql = preg_replace("/ENGINE\s?=/", "TYPE=", $sql);
						}
						elseif (preg_match("/CREATE TABLE/i", $sql)){
							// Выставляем кодировку соединения
							if (preg_match("/(CHARACTER SET|CHARSET)[=\s]+(\w+)/i", $sql, $charset)) {
								if (!$this->forced_charset && $charset[2] != $last_charset) {
									if (CHARSET == 'auto') {
										mysqli_query($connect,"SET NAMES '" . $charset[2] . "'") or trigger_error ("Неудается изменить кодировку соединения.<BR>{$sql}<BR>" . mysqli_error($connect), E_USER_ERROR);
										$cache .= tpl_l("Установлена кодировка соединения `" . $charset[2] . "`.", C_WARNING);
										$last_charset = $charset[2];
									}
									else{
										$cache .= tpl_l('Кодировка соединения и таблицы не совпадает:', C_ERROR);
										$cache .= tpl_l('Таблица `'. $table .'` -> ' . $charset[2] . ' (соединение '  . $this->restore_charset . ')', C_ERROR);
									}
								}
								// Меняем кодировку если указано форсировать кодировку
								if ($this->forced_charset) {
									$sql = preg_replace("/(\/\*!\d+\s)?((COLLATE)[=\s]+)\w+(\s+\*\/)?/i", '', $sql);
									$sql = preg_replace("/((CHARACTER SET|CHARSET)[=\s]+)\w+/i", "\\1" . $this->restore_charset . $this->restore_collate, $sql);
								}
							}
							elseif(CHARSET == 'auto'){ // Вставляем кодировку для таблиц, если она не указана и установлена auto кодировка
								$sql .= ' DEFAULT CHARSET=' . $this->restore_charset . $this->restore_collate;
								if ($this->restore_charset != $last_charset) {
									mysqli_query($connect,"SET NAMES '" . $this->restore_charset . "'") or trigger_error ("Неудается изменить кодировку соединения.<BR>{$sql}<BR>" . mysqli_error($connect), E_USER_ERROR);
									$cache .= tpl_l("Установлена кодировка соединения `" . $this->restore_charset . "`.", C_WARNING);
									$last_charset = $this->restore_charset;
								}
							}
						}
						if ($last_showed != $table) {$cache .= tpl_l("Таблица `{$table}`."); $last_showed = $table;}
					}
					elseif($this->mysql_version > 40101 && empty($last_charset)) { // Устанавливаем кодировку на случай если отсутствует CREATE TABLE
						mysqli_query($connect,"SET $this->restore_charset '" . $this->restore_charset . "'") or trigger_error ("Неудается изменить кодировку соединения.<BR>{$sql}<BR>" . mysqli_error($connect), E_USER_ERROR);
						echo tpl_l("Установлена кодировка соединения `" . $this->restore_charset . "`.", C_WARNING);
						$last_charset = $this->restore_charset;
					}
					$insert = '';
					$execute = 1;
				}
				if ($query_len >= 65536 && preg_match("/,$/", $str)) {
					$sql = rtrim($insert . $sql, ",");
					$execute = 1;
				}
				if ($execute) {
					$q++;
					mysqli_query($connect,$sql) or trigger_error ("Неправильный запрос.<BR>" . mysqli_error($connect), E_USER_ERROR);
					if (preg_match("/^insert/i", $sql)) {
						$aff_rows2 = mysqli_affected_rows($connect);
						$aff_rows += $aff_rows2;
						//1.2.104 - лог восстановлений бекапов
						log_add('restore.txt',$table. ' '.$aff_rows2);
					}
					$sql = '';
					$query_len = 0;
					$execute = 0;
				}
			}
		}
		echo $cache;
		echo tpl_s(1 , 1);
		echo tpl_l(str_repeat("-", 60));
		echo tpl_l("БД восстановлена из резервной копии.", C_RESULT);
		if (isset($info[3])) echo tpl_l("Дата создания копии: {$info[3]}", C_RESULT);
		echo tpl_l("Запросов к БД: {$q}", C_RESULT);
		echo tpl_l("Таблиц создано: {$tabs}", C_RESULT);
		echo tpl_l("Строк добавлено: {$aff_rows}", C_RESULT);

		$this->tabs = $tabs;
		$this->records = $aff_rows;
		$this->size = filesize(PATH . $this->filename);
		$this->comp = (int)$this->SET['comp_method'] * 10 + (int)$this->SET['comp_level'];
		echo "<SCRIPT>document.getElementById('back').disabled = 0;</SCRIPT>";
		// Передача данных для глобальной статистики
		if (GS) echo "<SCRIPT>document.getElementById('GS').src = 'http://sypex.net/gs.php?r={$this->tabs},{$this->records},{$this->size},{$this->comp},108';</SCRIPT>";

		$this->fn_close($fp);
	}

	function main(){
		$this->comp_levels = array('9' => '9 (максимальная)', '8' => '8', '7' => '7', '6' => '6', '5' => '5 (средняя)', '4' => '4', '3' => '3', '2' => '2', '1' => '1 (минимальная)','0' => 'Без сжатия');

		if (function_exists("bzopen")) {
			$this->comp_methods[2] = 'BZip2';
		}
		if (function_exists("gzopen")) {
			$this->comp_methods[1] = 'GZip';
		}
		$this->comp_methods[0] = 'Без сжатия';
		if (count($this->comp_methods) == 1) {
			$this->comp_levels = array('0' =>'Без сжатия');
		}

		$dbs = $this->db_select();
		$this->vars['db_backup']	= $this->fn_select($dbs, $this->SET['last_db_backup']);
		$this->vars['db_restore']   = $this->fn_select($dbs, $this->SET['last_db_restore']);
		$this->vars['comp_levels']  = $this->fn_select($this->comp_levels, $this->SET['comp_level']);
		$this->vars['comp_methods'] = $this->fn_select($this->comp_methods, $this->SET['comp_method']);
		$this->vars['tables']	= $this->SET['tables'];
		$this->vars['files']		= $this->fn_select($this->file_select(), '');
		$buttons = '';//"<INPUT TYPE=submit VALUE=Применить>";//<INPUT TYPE=button VALUE=Выход onClick=\"location.href = 'dump_db.php?reload'\">
		echo tpl_page(tpl_main(), $buttons);
	}

	function db_select(){
		global $connect;
		/*if (DBNAMES != '') {
			$items = explode(',', trim(DBNAMES));
			foreach($items AS $item){
  			if (mysql_select_db($item)) {
  				$tables = mysql_query("SHOW TABLES");
  				if ($tables) {
  					$tabs = mysql_num_rows($tables);
  					$dbs[$item] = "{$item} ({$tabs})";
  				}
  			}
			}
		}
		else {
  		$result = mysql_query("SHOW DATABASES");
  		$dbs = array();
  		while($item = mysql_fetch_array($result)){
  			if (mysql_select_db($item[0])) {*/
  				$tables = mysqli_query($connect,"SHOW TABLES");
  				if ($tables) {
  					$tabs = mysqli_num_rows($tables);
  					$dbs[0] = "data_base ({$tabs})"; //{$item[0]}
  				}
  			/*}
  		}
		}*/
		return $dbs;
	}

	function file_select(){
		$files = array('' => ' ');
		if (is_dir(PATH) && $handle = opendir(PATH)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/^.+?\.sql(\.(gz|bz2))?$/", $file)) {
					$files[$file] = $file;
				}
			}
			closedir($handle);
		}
		ksort($files);
		return $files;
	}

	function fn_open($name, $mode){
		if ($this->SET['comp_method'] == 2) {
			$this->filename = "{$name}.sql.bz2";
			return bzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		elseif ($this->SET['comp_method'] == 1) {
			$this->filename = "{$name}.sql.gz";
			return gzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
		}
		else{
			$this->filename = "{$name}.sql";
			return fopen(PATH . $this->filename, "{$mode}b");
		}
	}

	function fn_write($fp, $str){
		if ($this->SET['comp_method'] == 2) {
			bzwrite($fp, $str);
		}
		elseif ($this->SET['comp_method'] == 1) {
			gzwrite($fp, $str);
		}
		else{
			fwrite($fp, $str);
		}
	}

	function fn_read($fp){
		if ($this->SET['comp_method'] == 2) {
			return bzread($fp, 4096);
		}
		elseif ($this->SET['comp_method'] == 1) {
			return gzread($fp, 4096);
		}
		else{
			return fread($fp, 4096);
		}
	}

	function fn_read_str($fp){
		$string = '';
		$this->file_cache = ltrim($this->file_cache);
		$pos = strpos($this->file_cache, "\n", 0);
		if ($pos < 1) {
			while (!$string && ($str = $this->fn_read($fp))){
  			$pos = strpos($str, "\n", 0);
  			if ($pos === false) {
  				$this->file_cache .= $str;
  			}
  			else{
  				$string = $this->file_cache . substr($str, 0, $pos);
  				$this->file_cache = substr($str, $pos + 1);
  			}
  		}
			if (!$str) {
				if ($this->file_cache) {
					$string = $this->file_cache;
					$this->file_cache = '';
					return trim($string);
				}
				return false;
			}
		}
		else {
			$string = substr($this->file_cache, 0, $pos);
			$this->file_cache = substr($this->file_cache, $pos + 1);
		}
		return trim($string);
	}

	function fn_close($fp){
		if ($this->SET['comp_method'] == 2) {
			bzclose($fp);
		}
		elseif ($this->SET['comp_method'] == 1) {
			gzclose($fp);
		}
		else{
			fclose($fp);
		}
		@chmod(PATH . $this->filename, 0666);
		$this->fn_index();
	}

	function fn_select($items, $selected){
		$select = '';
		foreach($items AS $key => $value){
			$select .= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
		}
		return $select;
	}

	function fn_save(){
		if (SC) {
			$ne = !file_exists(PATH . "dumper.cfg.php");
			$fp = fopen(PATH . "dumper.cfg.php", "wb");
			fwrite($fp, "<?php\n\$this->SET = " . fn_arr2str($this->SET) . "\n?>");
			fclose($fp);
			if ($ne) @chmod(PATH . "dumper.cfg.php", 0666);
			$this->fn_index();
		}
	}

	function fn_index(){
		if (!file_exists(PATH . 'index.html')) {
			$fh = fopen(PATH . 'index.html', 'wb');
			fwrite($fh, tpl_backup_index());
			fclose($fh);
			@chmod(PATH . 'index.html', 0666);
		}
	}
}

function fn_int($num){
	return number_format($num, 0, ',', ' ');
}

function fn_arr2str($array) {
	$str = "array(\n";
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			$str .= "'$key' => " . fn_arr2str($value) . ",\n\n";
		}
		else {
			$str .= "'$key' => '" . str_replace("'", "\'", $value) . "',\n";
		}
	}
	return $str . ")";
}

// Шаблоны

function tpl_page($content = '', $buttons = ''){
	$html = $content;
	$html.= $buttons;
	$html.= '</div>
<div id="footer">
	<div>'.date('Y').' &copy; abc-cms.com</div>
	<a href="/" target="_blank" title="перейти на сайт">перейти на сайт</a>
</div>
</td>
<td class="col"><div class="header"></div><div class="menu_parent gradient"></div></td>
</tr>
</table>
</body>
</html>';
	return $html;
}

function tpl_main(){
	global $SK;
	/*$content = '<br /><h2>Восстановление БД из резервной копии</h2>';
	$content.= '<form method="post" action="/admin.php?m=restore" >';
	$content.= '<div class="field select td4"><label><span>Файл</span></label><div><select name="file">'.$SK->vars['files'].'</select></div></div>';
	$content.= '<div class="clear"></div><input type="submit" value="Восстановить из резервной копии" />';
	$content.= '</form>';
	$content.= '
<SCRIPT>
document.skb.action['.$SK->SET['last_action'].'].checked = 1;
</SCRIPT>';  */
	$content = '
	<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		$(".delete2").click(function(){
			if (!confirm("подтверите удаление резервной копии БД!!")) return false;
		});
		$(".restore").click(function(){
			if (!confirm("подтверите восстановление БД из резервной копии!!")) return false;
		});
	});
	</script><br />
	<div id="table">
	<table class="table" cellspacing="0" cellpadding="1" style="width:700px; min-width:500px">';
	$root = ROOT_DIR . 'admin/backup/';
	$files = scandir($root,1);
	foreach ($files as $file) {
		if (substr($file, -3)=='php' OR substr($file, -4)=='html' OR $file=='..' OR $file=='.') continue;
		$content.= '<tr>';
		$content.= '<td><a target="_blank" href="?m=backup&download='.$file.'">'.$file.'</a></td>';
		$content.= '<td>'.date ("Y-m-d H:i:s", filemtime($root.$file)).'</td>';
		$content.= '<td>'.intval(filesize($root.$file)/1024).'kb</td>';
		$content.= '<td><form method="post" action="/admin.php?m=restore" class="button green"><input type="hidden" name="file" value="'.$file.'"><input type="submit" title="восстановить резервную копию" value="Восстановить" /></form></td>';
		$content.= '<td><a class="delete2 button red" href="?m=restore&delete='.$file.'" title="удалить резервную копию"><span>Удалить</span></a></td>';
		$content.= '</tr>';
	}
	$content.= '</table></div>';
	return $content;
}

function tpl_process($title){
	$content = '<br /><h2>Восстановление БД из резервной копии</h2>';
	$content.= '<div id="logarea" style="float:left; width:400px; border-right: 1px solid #333; "></div>';
	$content.= '<div style="margin:0 0 0 420px">';
	$content.= 'Статус таблицы:';
	$content.= '<div id="st_tab" style="border-right: 1px solid #AAAAAA; width:1px; height:12px; background:#5555CC"></div>';
	$content.= 'Общий статус:';
	$content.= '<div id="so_tab" style="border-right: 1px solid #AAAAAA; width:1px; height:12px; background:#00AA00"></div>';
	$content.= '<div id="timer"></div>';
	$content.= '</div>';
	$content.= <<<HTML
<SCRIPT>
var WidthLocked = false;
function s(st, so){
	if (st) $('#st_tab').css('width',st+'px');
	if (so) $('#so_tab').css('width',so+'px');
}
function l(str, color){
	switch(color){
		case 2: color = 'navy'; break;
		case 3: color = 'red'; break;
		case 4: color = 'maroon'; break;
		default: color = 'black';
	}
	with(document.getElementById('logarea')){
		if (!WidthLocked){
			style.width = clientWidth;
			WidthLocked = true;
		}
		str = '<FONT COLOR=' + color + '>' + str + '</FONT>';
		innerHTML += innerHTML ? "<BR>\\n" + str : str;
		scrollTop += 14;
	}
}
</SCRIPT>
HTML;
	return $content;
}

function tpl_auth($error){
return <<<HTML
<SPAN ID=error>
<FIELDSET>
<LEGEND>Ошибка</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR>
<TD>Для работы Sypex Dumper Lite требуется:<BR> - Internet Explorer 5.5+, Mozilla либо Opera 8+ (<SPAN ID=sie>-</SPAN>)<BR> - включено выполнение JavaScript скриптов (<SPAN ID=sjs>-</SPAN>)</TD>
</TR>
</TABLE>
</FIELDSET>
</SPAN>
<SPAN ID=body STYLE="display: none;">
{$error}
<FIELDSET>
<LEGEND>Введите логин и пароль</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR>
<TD WIDTH=41%>Логин:</TD>
<TD WIDTH=59%><INPUT NAME=login TYPE=text CLASS=text></TD>
</TR>
<TR>
<TD>Пароль:</TD>
<TD><INPUT NAME=pass TYPE=password CLASS=text></TD>
</TR>
</TABLE>
</FIELDSET>
</SPAN>
<SCRIPT>
document.getElementById('sjs').innerHTML = '+';
document.getElementById('body').style.display = '';
document.getElementById('error').style.display = 'none';
var jsEnabled = true;
</SCRIPT>
HTML;
}

function tpl_l($str, $color = C_DEFAULT){
$str = preg_replace("/\s{2}/", " &nbsp;", $str);
return <<<HTML
<SCRIPT>l('{$str}', $color);</SCRIPT>

HTML;
}

function tpl_enableBack(){
return <<<HTML
<SCRIPT>document.getElementById('back').disabled = 0;</SCRIPT>

HTML;
}

function tpl_s($st, $so){
$st = round($st * 100);
$st = $st > 100 ? 100 : $st;
$so = round($so * 100);
$so = $so > 100 ? 100 : $so;
return <<<HTML
<SCRIPT>s({$st},{$so});</SCRIPT>

HTML;
}

function tpl_backup_index(){
return <<<HTML
<CENTER>
<H1>У вас нет прав для просмотра этого каталога</H1>
</CENTER>

HTML;
}

function tpl_error($error){
return <<<HTML
<FIELDSET>
<LEGEND>Ошибка при подключении к БД</LEGEND>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR>
<TD ALIGN=center>{$error}</TD>
</TR>
</TABLE>
</FIELDSET>

HTML;
}

function SXD_errorHandler($errno, $errmsg, $filename, $linenum, $vars) {
	if ($errno == 2048) return true;
	if (preg_match("/chmod\(\).+?: Operation not permitted/", $errmsg)) return true;
	$dt = date("Y.m.d H:i:s");
	$errmsg = addslashes($errmsg);

	echo tpl_l("{$dt}<BR><B>Возникла ошибка!</B>", C_ERROR);
	echo tpl_l("{$errmsg} ({$errno}) line:".$linenum, C_ERROR);
	echo tpl_enableBack();
	die();
}
