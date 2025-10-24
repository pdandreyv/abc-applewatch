<?=html_render('common/slider',$abc['slider'])?>

<?=i18n('common|txt_index') ? '<div class="content">'.i18n('common|txt_index',true).'</div><div class="clear"></div>' : ''?>

<h2><?=i18n('shop|new',true)?></h2>

<?=html_render('shop/product_list',$abc['products_index'])?>

<div class="clear"></div>

<?=@$abc['page']['text'] ? '<div class="content" '.editable('pages|text|'.$abc['page']['id']).'>'.$abc['page']['text'].'</div>' : ''?>
