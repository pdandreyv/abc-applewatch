<?php
$images = array();
$q['images'] = $q['images'] ? unserialize($q['images']) : array();
if (is_array($q['images'])) foreach ($q['images'] as $k=>$v) if (@$v['display']) {
	$img = get_img('gallery',$q,'images/'.$k,'');
	$size = getimagesize(ROOT_DIR.$img);
	$images[] = array(
		'img'=>$img,
		'preview'=>get_img('gallery',$q,'images/'.$k,'p-'),
		'height'=>$size[1],
		'title'=>filter_var($v,FILTER_SANITIZE_STRING)
	);
}
if ($images) {
	$top = $images[1]['height']/2-24;
	?>
<div class="gallery_slider">
	<div class="img">
 		<ul style="height:<?=$images[1]['height']?>px">
			<?php
			foreach ($images as $k=>$v) {
				?>
			<li style="height:<?=$v['height']?>px;<?=$k==1 ? '" class="active' : ' display:none'?>" data-i="<?=$k?>"><img src="<?=$v['img']?>" title="<?=$v['title']?>" /></li>
				<?php
			}
		?>
		</ul>
		<a href="#" class="sprite left" style="top:<?=$top?>px" title="Предыдущая"></a>
		<a href="#" class="sprite right" style="top:<?=$top?>px" title="Следующая"></a>
	</div>
	<div class="previews">
	<table>
		<tr>
			<?php
				foreach ($images as $k=>$v) {
				?>
			<td><a href="#" data-i="<?=$k?>" title="<?=$v['title']?>"><img src="<?=$v['preview']?>" alt="<?=$v['title']?>" /></a></td>
				<?php
				}
			?>
		</tr>
	</table>
	</div>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	n = 1;
	setInterval(function () {
		if (n > 1) n++;
		if (n == 1 || n > 5) {
			n = 1;
			var current = $('.gallery_slider .img li.active');
			next(current, 'next');
		}
	}, 5000);
	$('.gallery_slider .previews a').click(function () {
		n++;
		var i = $(this).data('i');
		$('.gallery_slider .img li').fadeOut(300).removeClass('active');
		var next = $('.gallery_slider .img li[data-i="' + i + '"]');
		var height = next.height();
		$('.gallery_slider .img ul').animate({height: height + 'px'}, 500);
		var top = height / 2 - 24;
		$('.gallery_slider .img a').animate({top: top + 'px'}, 500);
		next.addClass('active').fadeIn(300);
		return false;
	});
	$('.gallery_slider .img a.right').click(function () {
		n++;
		var current = $('.gallery_slider .img li.active');
		next(current, 'next');
		return false;
	});
	$('.gallery_slider .img a.left').click(function () {
		n++;
		var current = $('.gallery_slider .img li.active');
		next(current, 'prev');
		return false;
	});

	function next(current, type) {
		current.fadeOut(300).removeClass('active');
		if (type == 'next') {
			if (current.next('li').length) var next = current.next('li');
			else var next = $('.gallery_slider .img ul li:first-child');
		} else {
			if (current.prev('li').length) var next = current.prev('li');
			else var next = $('.gallery_slider .img ul li:last-child');
		}
		var height = next.height();
		$('.gallery_slider .img ul').animate({height: height + 'px'}, 500);
		var top = height / 2 - 24;
		$('.gallery_slider .img a').animate({top: top + 'px'}, 500);
		next.addClass('active').fadeIn(300);
	}

	//Get our elements for faster access and set overlay Width
	var div = $('.gallery_slider .previews');
	ul = $('.gallery_slider .previews table');
	// unordered list's top margin
	ulPadding = 0;
	//Get menu Width
	var divWidth = div.width();
	//Find last image container
	var lastLi = ul.find('td:last-child');
	//When user move mouse over menu
	div.mousemove(function (e) {
		//As images are loaded ul Width increases,
		//so we recalculate it each time
		var ulWidth = lastLi[0].offsetLeft + lastLi.outerWidth() + ulPadding;
		var left = (e.pageX - div.offset().left) * (ulWidth - divWidth) / divWidth;
		div.scrollLeft(left);
	});
})
</script>
<?php } ?>