<?php
$count = isset($_SESSION['basket']['count']) ? $_SESSION['basket']['count'] : 0;
$total = isset($_SESSION['basket']['total']) ? $_SESSION['basket']['total'] : 0;
?>
<div id="basket_info">
	<i class="opancart  icon-shopping-cart"></i>
	<a href="<?=get_url('basket')?>"></a>
	<div class="full" <?=$count>0 ? '' : ' style="display:none"' ?>>
		<a href="<?=get_url('basket')?>"><?=i18n('basket|product_count')?> <span class="count"><?=$count?></span>
		<br /><?=i18n('basket|product_summ')?> <span class="total"><?=price_format($total)?></span> <?=i18n('shop|currency')?></a>
	</div>
	<?php if ($count==0) {?>
	<div class="empty">
		<?=i18n('basket|empty',true)?>
	</div>
	<?php } ?>
</div>


<div class="modal fade" id="basket_message">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<?=i18n('basket|product_added',true)?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal" title="<?=i18n('basket|go_next')?>"><?=i18n('basket|go_next')?></button>
				<a class="btn btn-primary" href="<?=get_url('basket')?>"><?=i18n('basket|go_basket')?></a>
			</div>
		</div>
	</div>
</div>