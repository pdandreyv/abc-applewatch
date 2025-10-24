<?php
if ($i==1) echo '<div class="inner_container"><div class="row">';
?>
<div class="shop_product_list col-xs-12 col-sm-6 col-md-4">
	<div class="border clearfix">
		<div class="img">
			<div>
				<a href="<?=$q['_url']?>" title="<?=$q['_title']?>">
					<?php if ($q['img']) {?>
						<?php /* * ?>
						<img class="lazyload"
							data-srcset="/_imgs/_200x200<?=$q['_img']?> 320w,
									/_imgs/_300x300<?=$q['_img']?> 480w,
									/_imgs/150x150<?=$q['_img']?> 800w"
							data-sizes="auto"
							data-src="/_imgs/150x150<?=$q['_img']?>"
							alt="<?=$q['_title']?>">
						<?php /* */ ?>
						<picture>
							<source media="(max-width: 420px)" data-srcset="/_imgs/_200x200<?=$q['_img']?>">
							<source media="(max-width: 766px)" data-srcset="/_imgs/300x300<?=$q['_img']?>">
							<img class="lazyload" alt="<?=$q['_title']?>" data-src="/_imgs/150x150<?=$q['_img']?>">
						</picture>
 						<?php /* */?>
					<?php } else {?>
						<img src="/templates/images/no_img.svg" alt="<?=$q['_title']?>" />
					<?php } ?>
				</a>
			</div>
		</div>
		<a class="name" href="<?=$q['_url']?>" title="<?=$q['_title']?>"><?=$q['name']?></a>
		<?php if ($q['price']>0) {?>
		<div class="price">
			<span><?=$q['_price']?></span> <?=$q['_currency']?>
			<?php if ($q['_price2']) {?>
			<s><span><?=$q['_price2']?></span> <?=$q['_currency']?></s>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if ($q['price']>0) {?>
		<a class="btn btn-default pull-left" data-api="/api/basket_product_add/" data-id="<?=$q['id']?>" data-count="1" href="#" title="<?=$q['_buy']?>"><i class="icon-shopping-cart"></i> <?=$q['_buy']?></a>
		<?php } ?>
		<a class="btn btn-primary pull-right" href="<?=$q['_url']?>" title="<?=$q['_more']?>"><?=$q['_more']?></a>
	</div>
</div>
<?php
if (fmod($i,3)==0) echo '<div class="clearfix visible-md"></div>';
if (fmod($i,2)==0) echo '<div class="clearfix visible-sm"></div>';
if ($i==$num_rows) echo '</div></div>';