<header>
	<div>
		<?php
		$logo = '/templates/images/logo.jpg';
		$size = getimagesize(ROOT_DIR.$logo);
		?>
		<amp-img width="<?=$size[0]?>" height="<?=$size[1]?>" src=<?= $logo?>></amp-img>

		<button class="hamburger_btn"
		        on='tap:sidebar.toggle'
		        aria-label="Click to open sidebar"
		>
			<div class="hamburger"></div>
		</button>
	</div>
</header>

<amp-sidebar id="sidebar"
             layout="nodisplay"
             side="right">
	<amp-img class="amp-close-image"
	         src="/templates/images/ic_close_white.png"
	         width="20"
	         height="20"
	         alt="close sidebar"
	         on="tap:sidebar.close"
	         role="button"
	         tabindex="0"></amp-img>
	<?= html_query('menu/common', 'SELECT * FROM `pages` WHERE `display` = 1 AND `menu` = 1 ORDER BY `left_key` ASC', '', 60*60) ?>
</amp-sidebar>