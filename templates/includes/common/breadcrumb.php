<?php if ($i==1) { ?>
<div class="col-xl-12">
	<div class="breadcrumb">
		<ul class="breadcrumb_inset" itemscope itemtype="http://schema.org/BreadcrumbList">
<?php } ?>

<?php if ($i==1) { ?>
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a title="<?=filter_var($q['name'],FILTER_SANITIZE_STRING)?>" itemprop="item" href="<?=$q['url']?>"><span itemprop="name"><i class="icon-home"></i></span></a>
				<meta itemprop="position" content="<?=$i?>" />
				<span class="navigation-pipe">&gt;</span>
			</li>
<?php } elseif ($i<$num_rows) { ?>
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a title="<?=filter_var($q['name'],FILTER_SANITIZE_STRING)?>" itemprop="item" href="<?=$q['url']?>"><span itemprop="name"><?=$q['name']?></span></a>
				<meta itemprop="position" content="<?=$i?>" />
				<span class="navigation-pipe">&gt;</span>
			</li>
<?php } ?>

<?php if ($i==$num_rows) {?>
			<li><span class="navigation_page"><?=$q['name']?></span></li>
		</ul>
	</div>
</div>
<?php } ?>