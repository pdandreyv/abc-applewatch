<?php

//бекап базы данных
//todo - в новом дизайне не используется

/*
 * v1.1.20 - исправление ошибки в дампере
 * v1.4.0 - html_render в админке
 * v.4.17 - сокращение параметров form
 */

$connect = mysql_connect_db();

//скачиваем файл бекапа
if (@$_GET['download']) {
	$file = realpath(ROOT_DIR.'admin/backup/'.$_GET['download']);
	if (file_exists($file)) {
		if (ob_get_level()) {
			ob_end_clean();
		}
		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($file));
		// читаем файл и отправляем его пользователю
		readfile($file);
		exit;
	}
	else echo 'not found';
	die();
}

require_once(ROOT_DIR . $config['style'].'/includes/layouts/_template.php');
/***************************************************************************\
| Sypex Dumper Lite				version 1.0.8a								|
| (c)2003-2006 zapimir			zapimir@zapimir.net	http://sypex.net/		|
| (c)2005-2006 BINOVATOR		info@sypex.net								|
|---------------------------------------------------------------------------|
|	created: 2003.09.02 19:07			modified: 2006.10.17 20:45			|
|---------------------------------------------------------------------------|
| This program is free software; you can redistribute it and/or				|
| modify it under the terms of the GNU General Public License				|
| as published by the Free Software Foundation; either version 2			|
| of the License, or (at your option) any later version.					|
|																			|
| This program is distributed in the hope that it will be useful,			|
| but WITHOUT ANY WARRANTY; without even the implied warranty of			|
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the				|
| GNU General Public License for more details.								|
|																			|
| You should have received a copy of the GNU General Public License			|
| along with this program; if not, write to the Free Software				|
| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,USA. |
\***************************************************************************/

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
define('INSERT_IGNORE', FALSE); // устанавливаем, нужно ли игнорировать ошибки вставки (дубль уникальных ключей или не оответсвие в структуре). Имхо, правильней оставлять выключенным. Но добавил на всякий случай, вдруг где-то вылезет необходимость


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

if (count($_POST)>0) $SK->backup();
else $SK->main();

mysqli_close($connect);

echo "<script type=\"text/javascript\">document.getElementById('timer').innerHTML = '" . round(array_sum(explode(' ', microtime())) - $timer, 4) . " сек.'</script>";

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

	function backup() {
		global $connect;
		if (!isset($_POST)) {$this->main();}
		set_error_handler("SXD_errorHandler");
		$buttons = "<A ID=save HREF='' STYLE='display: none; padding:10px 0 0 20px; font-weight:bold;'>Скачать файл</A>";
		echo tpl_page(tpl_process("Создается резервная копия БД"), $buttons);
		//echo $buttons;

		$this->SET['last_action']	= 0;
		$this->SET['last_db_backup']= isset($_POST['db_backup']) ? $_POST['db_backup'] : '';
		$this->SET['tables_exclude']= 0;//!empty($_POST['tables']) && $_POST['tables']{0} == '^' ? 1 : 0;
		$this->SET['tables']		= (isset($_POST['tables']) AND is_array($_POST['tables'])) ? implode(',',$_POST['tables']) : '';
		$this->SET['comp_method']	= isset($_POST['comp_method']) ? intval($_POST['comp_method']) : 0;
		$this->SET['comp_level']	= isset($_POST['comp_level']) ? intval($_POST['comp_level']) : 0;
		$this->fn_save();

		$this->SET['tables']		= explode(",", $this->SET['tables']);
		if (!empty($_POST['tables'])) {
			foreach($this->SET['tables'] AS $table){
				//v1.3.21 - php7.3 preg_replace
				$table = preg_replace("/[^\w\-*?^]/", "", $table);
				$pattern = array( "/\?/", "/\*/");
				$replace = array( ".", ".*?");
				$tbls[] = preg_replace($pattern, $replace, $table);
  			}
		}
		else{
			$this->SET['tables_exclude'] = 1;
		}

		if ($this->SET['comp_level'] == 0) {
			$this->SET['comp_method'] = 0;
		}
		$db = "data_base";//$this->SET['last_db_backup'];

		/*if (!$db) {
			echo tpl_l("ОШИБКА! Не указана база данных!", C_ERROR);
			echo tpl_enableBack();
			exit;
		}*/
		echo tpl_l("Подключение к БД `{$db}`.");
		//mysql_select_db($db) or trigger_error ("Не удается выбрать базу данных.<BR>" . mysqli_error($connect), E_USER_ERROR);
		$tables = array();
		$result = mysqli_query($connect,"SHOW TABLES");
		$all = 0;
		while($row = mysqli_fetch_array($result)) {
			$status = 0;
			if (!empty($tbls)) {
				foreach($tbls AS $table){
					$exclude = preg_match("/^\^/", $table) ? true : false;
					if (!$exclude) {
						if (preg_match("/^{$table}$/i", $row[0])) {
							$status = 1;
						}
						$all = 1;
					}
					if ($exclude && preg_match("/{$table}$/i", $row[0])) {
						$status = -1;
					}
				}
			}
			else {
				$status = 1;
			}
			if ($status >= $all) {
  			$tables[] = $row[0];
  			}
		}

		$tabs = count($tables);
		// Определение размеров таблиц
		$result = mysqli_query($connect,"SHOW TABLE STATUS");
		$tabinfo = array();
		$tab_charset = array();
		$tab_type = array();
		$tabinfo[0] = 0;
		$info = '';
		while($item = mysqli_fetch_assoc($result)){
			//print_r($item);
			if(in_array($item['Name'], $tables)) {
				//$item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
				//v1.2.119 косяк дампера
				if (empty($item['Rows'])) {
					$item['Rows'] = mysql_select("SELECT COUNT(*) FROM `{$item['Name']}`", 'string');
				}
				$tabinfo[0] += $item['Rows'];
				$tabinfo[$item['Name']] = $item['Rows'];
				$this->size += $item['Data_length'];
				$tabsize[$item['Name']] = 1 + round(LIMIT * 1048576 / ($item['Avg_row_length'] + 1));
				if($item['Rows']) $info .= "|" . $item['Rows'];
				if (!empty($item['Collation']) && preg_match("/^([a-z0-9]+)_/i", $item['Collation'], $m)) {
					$tab_charset[$item['Name']] = $m[1];
				}
				$tab_type[$item['Name']] = isset($item['Engine']) ? $item['Engine'] : $item['Type'];
			}
		}
		$show = 10 + $tabinfo[0] / 50;
		$info = $tabinfo[0] . $info;
		$name = $db . '_' . date("Y-m-d_H-i");
		$fp = $this->fn_open($name, "w");
		echo tpl_l("Создание файла с резервной копией БД:<BR>\\n  -  {$this->filename}");
		$this->fn_write($fp, "#SKD101|{$db}|{$tabs}|" . date("Y.m.d H:i:s") ."|{$info}\n\n");
		$this->fn_write($fp, "SET FOREIGN_KEY_CHECKS=0;\n\n"); // отключаем проверку внешних ключей при импорте, т.к. последовательность при дампе не сохраняется
		$t=0;
		echo tpl_l(str_repeat("-", 60));
		$result = mysqli_query($connect,"SET SQL_QUOTE_SHOW_CREATE = 1");
		// Кодировка соединения по умолчанию
		if ($this->mysql_version > 40101 && CHARSET != 'auto') {
			mysqli_query($connect,"SET NAMES '" . CHARSET . "'") or trigger_error ("Неудается изменить кодировку соединения.<BR>" . mysqli_error($connect), E_USER_ERROR);
			$last_charset = CHARSET;
		}
		else{
			$last_charset = '';
		}
		foreach ($tables AS $table){
			// Выставляем кодировку соединения соответствующую кодировке таблицы
			if ($this->mysql_version > 40101 && $tab_charset[$table] != $last_charset) {
				if (CHARSET == 'auto') {
					mysqli_query($connect,"SET NAMES '" . $tab_charset[$table] . "'") or trigger_error ("Неудается изменить кодировку соединения.<BR>" . mysqli_error($connect), E_USER_ERROR);
					echo tpl_l("Установлена кодировка соединения `" . $tab_charset[$table] . "`.", C_WARNING);
					$last_charset = $tab_charset[$table];
				}
				else{
					echo tpl_l('Кодировка соединения и таблицы не совпадает:', C_ERROR);
					echo tpl_l('Таблица `'. $table .'` -> ' . $tab_charset[$table] . ' (соединение '  . CHARSET . ')', C_ERROR);
				}
			}
			echo tpl_l("Обработка таблицы `{$table}` [" . fn_int($tabinfo[$table]) . "].");
			//sleep(1);
		// Создание таблицы
		$result = mysqli_query($connect,"SHOW CREATE TABLE `{$table}`");
		$tab = mysqli_fetch_array($result);
		$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|COLLATE=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
		$this->fn_write($fp, "DROP TABLE IF EXISTS `{$table}`;\n{$tab[1]};\n\n");
		// Проверяем нужно ли дампить данные
		if (in_array($tab_type[$table], $this->only_create)) {
				continue;
			}
		// Опредеделяем типы столбцов
			$NumericColumn = array();
			$result = mysqli_query($connect,"SHOW COLUMNS FROM `{$table}`");
			$field = 0;
			while($col = mysqli_fetch_row($result)) {
			$NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
			}
			$fields = $field;
			$from = 0;
			$limit = $tabsize[$table];
			$limit2 = round($limit / 3);
			if ($tabinfo[$table] > 0) {
				if ($tabinfo[$table] > $limit2) {
					echo tpl_s(0, $t / $tabinfo[0]);
				}
				$i = 0;
				$this->fn_write($fp, "INSERT ".(INSERT_IGNORE ? 'IGNORE ' :'')."INTO `{$table}` VALUES");
				while(($result = mysqli_query($connect,"SELECT * FROM `{$table}` LIMIT {$from}, {$limit}")) && ($total = mysqli_num_rows($result))){
					while($row = mysqli_fetch_row($result)) {
						$i++;
						$t++;

						for($k = 0; $k < $fields; $k++){
							if ($NumericColumn[$k])
								$row[$k] = isset($row[$k]) ? $row[$k] : "NULL";
							else
								$row[$k] = isset($row[$k]) ? "'" . mysql_res($row[$k]) . "'" : "NULL";
						}

						$this->fn_write($fp, ($i == 1 ? "" : ",") . "\n(" . implode(", ", $row) . ")");
						if ($i % $limit2 == 0)
							echo tpl_s($i / $tabinfo[$table], $t / $tabinfo[0]);
					}
					mysqli_free_result($result);
					if ($total < $limit) {
						break;
					}
					$from += $limit;
				}

				$this->fn_write($fp, ";\n\n");
				echo tpl_s(1, $t / $tabinfo[0]);
			}
		}
		$this->tabs = $tabs;
		$this->records = $tabinfo[0];
		$this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
		echo tpl_s(1, 1);
		echo tpl_l(str_repeat("-", 60));
		$this->fn_close($fp);
		echo tpl_l("Резервная копия БД `{$db}` создана.", C_RESULT);
		echo tpl_l("Размер БД:	" . round($this->size / 1048576, 2) . " МБ", C_RESULT);
		$filesize = round(filesize(PATH . $this->filename) / 1048576, 2) . " МБ";
		echo tpl_l("Размер файла: {$filesize}", C_RESULT);
		echo tpl_l("Таблиц обработано: {$tabs}", C_RESULT);
		echo tpl_l("Строк обработано:   " . fn_int($tabinfo[0]), C_RESULT);
		echo "<SCRIPT>with (document.getElementById('save')) {style.display = ''; innerHTML = 'Скачать файл ({$filesize})'; href = '?m=backup&download=" . $this->filename . "'; }document.getElementById('back').disabled = 0;</SCRIPT>";
		// Передача данных для глобальной статистики
		//if (GS) echo "<SCRIPT>document.getElementById('GS').src = 'http://sypex.net/gs.php?b={$this->tabs},{$this->records},{$this->size},{$this->comp},108';</SCRIPT>";

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
			$select .= '<option value="'.$key.'" '.($key==$selected ? 'selected="selected"' : '').'>'.$value.'</option>';
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
	global $SK,$connect;
	$query = "SHOW TABLES";
	$result = mysqli_query($connect,$query);
	$tables = array();
	while ($q = mysqli_fetch_array($result)) {
		$tables[] = array('id'=>$q[0],'name'=>$q[0]);
	};
	$content = '<br /><h2>Создание резервной копии БД</h2>';
	$content.= '<form method="post" action="/admin.php?m=backup" >';
	//$content.= form('input td8','tables',$SK->vars['tables'],array('name'=>'Фильтр таблиц'));
	//echo $SK->vars['tables'];
	$content.= form('multicheckbox td4 tr4','tables',array(
		'name' =>'Фильтр таблиц',
		'value' => array($SK->vars['tables'],$tables)
	));

	//$content.= '<div class="clear"></div>';
	$content.= '<div class="field select td4"><label><span>Метод сжатия</span></label><div><select name="comp_method">'.$SK->vars['comp_methods'].'</select></div></div>';
	$content.= '<div style="height:57px;"></div>';
	$content.= '<div class="field select td4"><label><span>Степень сжатия</span></label><div><select name="comp_level">'.$SK->vars['comp_levels'].'</select></div></div>';
	$content.= '<div style="height:57px;"></div>';
	$content.= ' <input type="submit" value="Создать резервную копию" />';
	$content.= '</form>';
	return $content;
}

function tpl_process($title){
	$content = '<br /><h2>Создается резервная копия БД</h2>';
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

	echo tpl_l("{$dt}<BR><B>Возникла ошибка!</B>".$filename.'-'.$linenum, C_ERROR);
	echo tpl_l("{$errmsg} ({$errno})", C_ERROR);
	echo tpl_enableBack();
	die();
}
