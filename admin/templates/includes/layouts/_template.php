<?php
$style = (isset($_COOKIE['a_style']) AND in_array($_COOKIE['a_style'],array('a','b','c','g'))) ? $_COOKIE['a_style'] : 'g';
$size = (isset($_COOKIE['a_size']) AND in_array($_COOKIE['a_size'],array('b','m','s'))) ? $_COOKIE['a_size'] : 'm';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Панель управления сайтом</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow">
<?=html_sources('return','admin')?>
</head>
<body class="<?=$style?>-style <?=$size?>-size <?=$module['one_form']?'one_form':''?>">
<table id="body" cellspacing="0" cellpadding="0">
	<tr>
		<td class="col"><div class="header"></div><div class="menu_parent gradient"></div></td>
		<td class="main_col" nowrap="nowrap">

			<div class="header">
				<div class="abc"><a href="#" class="a">a</a><a href="#" class="b">b</a><a href="#" class="c">c</a></div>
				<div class="cms">Content<br />Management<br />System</div>
				<a class="sprite settings2" href="#"></a>
				<div class="login"><?=$user['email']?> &nbsp; <a href="?m=login&u=exit">[<?=a18n('profile_exit')?>]</a></div>
				<div class="settings">
					<b><?=a18n('template_size')?></b>
					<div class="size">
						<a href="#" class="b"><?=a18n('template_big')?></a><br />
						<a href="#" class="m"><?=a18n('template_medium')?></a><br />
						<a href="#" class="s"><?=a18n('template_small')?></a>
					</div>
					<b><?=a18n('template_color')?></b>
					<div class="color">
						<a href="#" class="a"></a>
						<a href="#" class="b"></a>
						<a href="#" class="c"></a>
						<a href="#" class="g"></a>
					</div>
				</div>
			</div>

			<?=html_array('layouts/menu',$modules_admin)?>

			<div id="wrapper">
				<?=$content?>
				<?php
				if (is_array($filter)) {
					?>
				<div id="filter">
					<?php
					foreach ($filter as $k=>$v) {
						echo is_array($v) ? call_user_func_array('filter', $v) : $v;

					}
					?>
					<div class="clear"></div></div>
					<?php
				}
				if (@$table) {
					echo table($table,$query);
					//v1.2.130 - чекбоксы для админки
					if (isset($table['_check'])) {
						?>
						<form method="post" class="table_check" action="" style="padding:5px 0 0">
							<input type="hidden" name="_check[ids]" />
							<?php
							//операции в виде кнопок
							if (isset($table['_check']['buttons']) AND is_array($table['_check']['buttons'])) {
								foreach ($table['_check']['buttons'] as $k=>$v) {
									?>
									<input type="submit" name="_check[<?=$k?>]" value="<?=$v?>" />
									<?php
								}
							}
							//операции в виде селекта и кнопки применить
							if (isset($table['_check']['select']) AND is_array($table['_check']['select'])) {
								?>
								<select name="_check[select]"><?=select('',$table['_check']['select'],'')?></select>
								<input type="submit" value="Применить" />
								<?php
							}
							?>
						</form>
						<?php
					}
				}
				?>

<?php
if (!in_array($get['m'],array('backup','restore'))) {
	?>
			</div>
			<?php
			//загружаем внутрь таблицы толлко если одна форма
			if (/*$get['id'] AND */isset($form) AND $module['one_form']==true)
				require_once(ROOT_DIR . $config['style'].'/includes/layouts/form.php');
			?>
			<div id="footer">
				<div><?=date('Y')?> &copy; abc-cms.com<br><span><a href="/_documentation" target="_blank">v.<?=$config['cms_version']?></a></span></div>
				<a href="/" target="_blank" title="<?=a18n('go_to_site')?>"><?=a18n('go_to_site')?></a>
			</div>
		</td>
		<td class="col"><div class="header"></div><div class="menu_parent gradient"></div></td>
	</tr>
</table>
<div id="dialog">
	<div class="dialog_data">
		<div class="dialog_text">Подтвердите удаление!</div>
		<a class="button green" href="#"><span>Отменить</span></a>
		<a class="button red" href="#"><span>Удалить</span></a>
	</div>
</div>
<div id="overlay"<?=($get['id'] AND isset($form)) ? ' class="display"' : ''?>></div>
	<?php
	if ($get['id'] AND isset($form) AND $module['one_form']==false) require_once(ROOT_DIR . $config['style'].'/includes/layouts/form.php');
	//отключил контекстное меню
	//if (isset($table) && is_array($table)) require_once(ROOT_DIR . $config['style'].'/includes/common/contextmenu.php');
	echo html_sources('footer');
	?>
</body>
</html>
	<?php
}
?>