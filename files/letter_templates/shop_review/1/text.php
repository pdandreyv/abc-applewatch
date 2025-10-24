Для товара <a href="<?=$config['http_domain'].get_url('shop_product',$q['product'])?>"><?=$q['product']['name']?></a> добавлен <a href="<?=$config['http_domain']?>/admin.php?m=shop_reviews&id=<?=$q['id']?>">отзыв</a>
<br />
<?=$q['email']?>
<br /><?=$q['name']?> <?=date2($q['date'],'%d.%m.%Y')?>
<br /><?=$q['text']?>