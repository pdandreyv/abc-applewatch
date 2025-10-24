<?php

/*
 * поиск подозрительных файлов
 */

define('ROOT_DIR', dirname(__FILE__).'/../');

if (@$_GET['path']) {
	$path = base64_decode(@$_GET['path']);
	if (@$_GET['action']=='delete') {
		unlink(ROOT_DIR . $path);
		//echo 'удалено';
	}
	if(@$_POST['content']) {
		$content = $_POST['content'];
		$fp = fopen(ROOT_DIR.$path, 'w');
		fwrite($fp,$content);
		fclose($fp);
	}
	if (file_exists(ROOT_DIR . $path)) {
		$string = file_get_contents(ROOT_DIR . $path);
	}
	else echo 'ФАЙЛ УДАЛЕН<br>';
	?>
	<strong><?= $path ?></strong>
	<form method="post" action="?path=<?= $_GET['path'] ?>">
		<textarea name="content" style="width:100%; height:90%"><?= htmlspecialchars(@$string) ?></textarea>
		<a style="float:right" href="?path=<?= $_GET['path'] ?>&action=delete">удалить</a>
		<button>Сохранить</button>
	</form>
	<?php

}
else {
	?>
	<style>
		.script {
			color: #000
		}

		.error {
			color: darkred;
			font-weight: bold
		}

		.error span {
			color: #000;
			font-weight: normal
		}

		.error a {
			font-weight: normal
		}

		ul {
			padding: 0 0 0 15px;
			list-style: none;
			margin: 0;
			display: none
		}

		li {
			padding: 0;
			list-style: none;
			margin: 0
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('.error').parents('ul').show();
			$('.plus').click(function () {
				$(this).next().slideToggle();
				return false;
			});
		})
	</script>
	<ul style="font:11px Arial; color:#999">
		<?= get_file() ?>
	</ul>
<?php
}

function search($path) {
	$search = array(
		'                                                                 ',
		'																		',
		'base64',
		'eval',
		'posix_',
		'SERVER_SOFTWARE',
		'chr(',
		'$GLOBALS',
		'$_FILES',
		'assert',
		'str_rot13',
		'\x'
	);
	//echo '<br>'.$path;
	$string = file_get_contents($path);
	foreach ($search as $k=>$v) {
		if (strpos($string, $v)) {
			return true;
		}
	}
}

function get_file ($dir = '') {
	$content = '';
	if ($handle = opendir(ROOT_DIR.$dir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file=='.'||$file=='..') continue;
			if (is_dir(ROOT_DIR.$dir.'/'.$file)) $folders[] = $file;
			else $files[] = $file;
		}
		closedir($handle);
	}
	if (isset($folders)) {
		sort($folders, SORT_LOCALE_STRING);
		foreach ($folders as $k=>$v) {
			$content.= '<li>:.. '.$v.'/ <a class="plus" href="#">+</a>';
			$content.= '<ul>'.get_file ($dir.'/'.$v).'</ul>';
			$content.= '</li>';

		}
	}
	if (isset($files)) {
		sort($files, SORT_LOCALE_STRING);
		$array = array('php','tml');
		foreach ($files as $k=>$v) {
			$class = in_array(substr($v, -3),$array) ? 'script' : '';
			if ($class) {
				$class = search(ROOT_DIR.$dir.'/'.$v) ? 'error':$class;
			}
			$content.= '<li'.($class?' class="'.$class.'"':'').'>:.. '.$v;
			if ($class=='error') $content.= ' <a target="_blank" href="?path='.base64_encode($dir.'/'.$v).'">посмотреть</a>';
			$content.='</li>';
		}
	}
	return $content;
}