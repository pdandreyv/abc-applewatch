<?php
$table = 'style="font:14px/16px Arial; border-collapse:collapse; border-spacing:0;"';
$th = 'style="text-align:left; padding:3px; font-weight:normal"';
$td = 'style="padding:3px;"';
?>
<table <?=$table?>>
	<tr>
		<th <?=$th?>>Имя:</th><td <?=$td?>><?=$q['name']?></td>
	</tr>
	<tr>
		<th <?=$th?>>Email:</th><td <?=$td?>><?=$q['email']?></td>
	</tr>
	<tr valign="top">
		<th <?=$th?>>Сообщение:</th><td <?=$td?>><?=preg_replace("/\n/","<br />",$q['text'])?></td>
	</tr>
</table>