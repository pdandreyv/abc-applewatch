<div id="landing1">
<?php
if ($items = mysql_select("
	SELECT * FROM landing_items
	WHERE template=".$q['template']." AND language=".$lang['id']."
	ORDER BY `rank` DESC",'rows'))
{
	?>
	<ul>
	<?php
	foreach ($items as $k => $v) {
		?>
		<li><?= $v['name'] ?></li>
		<?php
	}
	?>
	</ul>
	<?php
}
?>
</div>