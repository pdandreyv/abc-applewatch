<?php
$img = get_img('slider',$q,'img','p-');
$title = htmlspecialchars($q['name']);
if ($i==1) {
	?>
<div id="slider" class="carousel slide" data-ride="carousel">
	<div class="carousel-inner">
<?php
}
?>
	<div class="carousel-item <?=$i==1 ? ' active' : ''?>">
		<a <?=$q['url']?'href="'.$q['url'].'"':''?> title="<?=$title?>">
			<img class="d-block w-100" src="<?=$img?>" alt="<?=$title?>">
		</a>
	</div>
<?php
if ($i==$num_rows) {
	?>
	</div>
	<?php if ($num_rows>1) {?>
		<ol class="carousel-indicators">
			<?php
			for ($n=0; $n<$num_rows; $n++) {
				?>
				<li data-target="#slider" data-slide-to="<?=$n?>"<?=$n==0?' class="active"':''?>></li>
			<?php
			}
			?>
		</ol>
	<?php
	}
	?>
	<a class="carousel-control-prev" href="#slider" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="carousel-control-next" href="#slider" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
	</div>
<?php
}
?>

