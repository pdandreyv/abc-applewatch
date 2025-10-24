<?php
if ($i==1) {
	?>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu_list" aria-controls="menu_list" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="menu_list">
			<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
	<?php
}
?>
			<?php if ($q['_active']==1) {?>
			<li class="nav-item active"><span class="nav-link"><?=$q['name']?></span>
			<?php } else {?>
			<li class="nav-item"><a class="nav-link" href="<?=$q['_url']?>" title="<?=htmlspecialchars($q['name'])?>"><?=$q['name']?></a>
			<?php } ?>

			<?php if ($q['_submenu']) {?>
			<ul class="dropdown-menu">
				<?php foreach ($q['_submenu'] as $k=>$v) {?>
				<li><a href="<?=$v['_url']?>" title="<?=htmlspecialchars($v['name'])?>"><?=$v['name']?></a></li>
				<?php } ?>
			</ul>
			<?php } ?>
			</li>
<?php
if ($i==$num_rows) {
	?>
			</ul>
		</div>
	</nav>
<script type="text/javascript">
	/*
document.addEventListener("DOMContentLoaded", function () {
	$('#menu .dropdown-menu').prev('a,.a').append('<span class="caret"></span>').click(function () {
		$(this).next('.dropdown-menu').toggle();
		return false;
	});
	$('#menu .active').parents('li').addClass('active');
})
*/
</script>
	<?php
}
?>