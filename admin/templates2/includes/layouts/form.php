<div id="window" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form id="form<?=$get['id']?>" class="form" method="post" enctype="multipart/form-data" action="<?=setUrlParams($_SERVER['REQUEST_URI'],array('u'=>'edit','id'=>false))?>">
				<div class="modal-header">
					<h5 class="modal-title">
						ID:<span data-name="id"><?=$get['id']?></span>
						<?php
						require_once(ROOT_DIR.$config['style'].'/includes/layouts/form_params.php');
						?>
					</h5>
					<?php if ($module['one_form']==false) {?>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					<?php } ?>
				</div>
				<div class="modal-body">
					<?php
					require_once(ROOT_DIR.$config['style'].'/includes/layouts/form_body.php');
					?>
					<input name="nested_sets[on]" type="hidden" value="0" />
				</div>
				<div class="modal-footer">
					<?php
					require_once(ROOT_DIR.$config['style'].'/includes/layouts/form_footer.php');
					?>
				</div>
			</form>
		</div>
	</div>
</div>


