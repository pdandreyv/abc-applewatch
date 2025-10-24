<?=html_sources('footer','jquery_validate.js')?>
<h2 style="padding:10px 0 0px"><?=i18n('shop|review_add',true)?></h2>
<noscript><?=i18n('validate|not_valid_captcha2')?></noscript>
<form id="shop_review_form" class="form validate ajax" data-action="/api/review_add/" method="post">
	<div class="review_rating">
		<div><?php for ($n=1; $n<6; $n++) echo '<span data-n="'.$n.'" class="active"></span>'; ?></div>
		<input name="rating" type="hidden" value="5" />
	</div>
	<input name="language" type="hidden" value="<?=$lang['id']?>" />
	<?php
	echo html_array('form/input',array(
		'caption'	=>	i18n('shop|review_email',true),
		'name'		=>	'email',
		'attr'		=>	' required email',
	));
	echo html_array('form/input',array(
		'caption'	=>	i18n('shop|review_name',true),
		'name'		=>	'name',
		'attr'		=>	' required ',
	));
	echo html_array('form/textarea',array(
		'name'		=>	'text',
		'caption'	=>	i18n('shop|review_text',true),
		'attr'		=>	' required ',
	));
	echo html_array('form/captcha2');//скрытая капча
	echo html_array('form/button',array(
		'name'	=>	i18n('shop|review_send'),
	));
	?>
	<input name="product" type="hidden" value="<?=$q['id']?>">
</form>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		$('#shop_review_form .review_rating span').hover(
			function(){
				var n = $(this).data('n');
				for (var i=1; i<=n; i++) {
					$('#shop_review_form .review_rating span[data-n="'+i+'"]').addClass('hover');
				}
			},
			function(){
				$('#shop_review_form .review_rating span').removeClass('hover');
			}
		);
		$('#shop_review_form .review_rating span').click(function(){
			$('#shop_review_form .review_rating span').removeClass('active');
			var n = $(this).data('n');
			for (var i=1; i<=n; i++) {
				$('#shop_review_form .review_rating span[data-n="'+i+'"]').addClass('active');
			}
			$('#shop_review_form .review_rating input').val(n);
		});
	});
</script>

