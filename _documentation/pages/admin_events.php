Данные функции касаются не только админпанели, но могут быть применены и на сайте
<br>
По умолчанию функции находятся в файле /functions/event_func.php,
<br>но могут находится и в модулях админки, если больше нигде не используются

<h3>event_change_*</h3>
Если после редактирования объекта в админке необходимо провести ряд операций то нужно использовать данные функции
<pre>function event_change_shop_reviews ($q) {
	if ($q['product']>0) {
		$q['product'] = intval($q['product']);
		$data = array(
			'id' => $q['product'],
			'rating' => mysql_select("SELECT SUM(rating)/COUNT(id) FROM shop_reviews WHERE display=1 AND product=" . $q['product'], 'string'),
		);
		//print_r($data);
		mysql_fn('update', 'shop_products', $data);
	}
}
</pre>
В данном случае после редактирования отзыва, у товара который привязан к отзыву, будет пересчитан рейтинг

<h3>event_delete_*</h3>
<br>Если после удаления объекта в админке необходимо провести ряд операций то нужно использовать данные функции
<pre>function event_delete_shop_reviews ($q) {
	if ($q['product']>0) {
		$q['product'] = intval($q['product']);
		$data = array(
			'id' => $q['product'],
			'rating' => mysql_select("SELECT SUM(rating)/COUNT(id) FROM shop_reviews WHERE display=1 AND product=" . $q['product'], 'string'),
		);
		//print_r($data);
		mysql_fn('update', 'shop_products', $data);
	}
}
</pre>
В данном случае после удаления отзыва у товара, который привязан к отзыву, будет пересчитан рейтинг
