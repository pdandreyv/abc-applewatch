<?php
$img = get_img('gallery',$q,'img','p-');
if ($i==1) echo '<div class="inner_container"><div class="row">'; ?>
<div class="gallery_list _list col-xs-6 col-sm-6 col-md-4">
	<a href="<?=get_url('gallery',$q)?>" title="<?=htmlspecialchars($q['name'])?>"><img src="<?=$img?>" alt="<?=htmlspecialchars($q['img'])?>"/></a>
	<?=$q['name']?>
</div>
<?php
if (fmod($i,3)==0) echo '<div class="clearfix visible-md"></div>';
if (fmod($i,2)==0) echo '<div class="clearfix visible-xs visible-sm"></div>';
if ($i==$num_rows) echo '</div></div>';
?>