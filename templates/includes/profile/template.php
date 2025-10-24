<?php
if ($html['module2']) {
	if ($u[3]) echo $html['content'];
	else {		?>
<div class="content">
	<?php if ($u[2]=='user_edit') { ?>	<h1><?=i18n('profile|user_edit',true)?></h1>
	<?php } elseif($u[2]=='orders') {?>
	<h1><?=i18n('basket|orders',true)?></h1>
	<?php } else { ?>
	<h1><?=$page['name']?></h1>
	<?php } ?>
	<?=$html['content']?>
</div>
		<?php
	}
}
else {	?>
<div class="content">
	<h1<?=editable('pages|name|'.$page['id'])?>><?=$page['name']?></h1>
	<div<?=editable('pages|text|'.$page['id'])?>><?=$page['text']?></div>
	<?=$html['profile_menu']?>
</div>
	<?php
}
?>