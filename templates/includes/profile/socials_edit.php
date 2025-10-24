<?php
//v1.2.69 - добавлена
if (@$q['message']) echo html_render('form/messages',array($q['message']));
?>
<table class="table">
<?php
foreach ($config['user_socials']['types'] as $k=>$v) {
	//урл для редиректа
	$redirect = urlencode($config['http_domain'].get_url('profile','socials'));
	$url = 'http://auth.abc-cms.com/' . $v . '/?redirect='.$redirect;
	$social = '';
	foreach ($abc['user_socials'] as $k1=>$v1) {
		if ($v1['type']==$k) {
			$social = $v1;
			//unset($socials[$k1]);
			break;
		}
	}
	?>
	<tr>
		<td><?=i18n('socials|'.$k)?></td>
		<td><?php if ($social AND $social['link']) {?>
		<a href="<?=$social['link']?>"><?=$social['link']?></a>
		<?php } ?></td>
		<?php if ($social) {?>
		<td align="right"><a onclick="if(confirm('<?=i18n('socials|confirm_delete')?>')) {} else return false" href="?delete=<?=$social['id']?>"><?=i18n('socials|off')?></a></td>
		<?php } else {?>
		<td align="right"><a href="<?=$url?>"><?=i18n('socials|on')?></a></td>
		<?php }?>
	</tr>
	<?php
}
?>
</table>
