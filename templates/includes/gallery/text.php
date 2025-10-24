<?php
if ($q['template']==1) {
	?>
<?=html_sources('footer','highslide_gallery')?>
<div class="inner_container">
	<div class="row">
	<?php
	$q['images'] = $q['images'] ? unserialize($q['images']) : array();
	$i=0;
	foreach ($q['images'] as $k=>$v) if (@$v['display']==1) {
		$i++;
		$img = get_img('gallery',$q,'images/'.$k,'p-');
		$img_origin = get_img('gallery',$q,'images/'.$k,'');
		$title = htmlspecialchars($v['name'])
		?>
		<div class="gallery_list _list col-xs-6 col-sm-6 col-md-4">
			<a onclick="return hs.expand(this, config1 )" href="<?=$img_origin?>" title="<?=$title?>"><img src="<?=$img?>" alt="<?=$title?>" /></a>
			<?=$v['name']?>
		</div>
		<?php
		if (fmod($i,3)==0) echo '<div class="clearfix visible-md"></div>';
		if (fmod($i,2)==0) echo '<div class="clearfix visible-xs visible-sm"></div>';
	}
?>
	</div>
</div>
<?php
}
else {
	echo html_array('gallery/slider',$q);
}
?>
