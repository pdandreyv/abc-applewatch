<?php

//заказы
/**
 * v1.4.9 шаблон для списка товаров
 * v1.4.17 - сокращение параметров form
 * v1.4.24 - для кавычек товара
 * v1.4.28 - автозаполнение
 * v1.4.31 - создание новой записи в автозаплнении
 */

//исключение при редактировании модуля
if ($get['u']=='edit') {
	$post['basket'] = serialize($post['basket']);
	unset($post['send']);
}
$types = mysql_select("SELECT id,name FROM order_types ORDER BY `rank`",'array');

//отправка письма
function event_change_orders($q){
	global $lang,$post;
	if ($_POST['send']) {
		$order = mysql_select("SELECT name ot_name,text ot_text FROM order_types WHERE id = '".$post['type']."' ORDER BY `rank` LIMIT 1",'row');
		$order = array_merge($order,$post);
		$order['id'] = $q['id'];
		require_once(ROOT_DIR.'functions/mail_func.php');	//функции для сайта
		mailer('basket',$lang['id'],$order,$order['email']);
	}
}

$a18n['login']	= 'пользователь';
$a18n['delivery_type']	= 'дип доставки';
$a18n['delivery_cost']	= 'стоимость';
$a18n['paid']	= 'оплачен';
$a18n['date_paid']	= 'дата оплаты';
$a18n['payment']	= 'способ оплаты';

$table = array(
	'id'	=>	'id:desc',
	'_view'      => 'basket',
	'type:'	=>	$types,
	'login'	=>	'<a href="/admin.php?m=users&id={user}">{login}</a>',
	'email'	=>	'text',
	'total'	=>	'right',
	'created_at'	=>	'date_smart',
	'payment' => $config['payments'],
	'paid'	=>	'boolean'
);

$where = (isset($get['type']) && $get['type']>0) ? ' AND orders.type='.$get['type'].' ' : '';
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(orders.email) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(orders.basket) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT orders.*,
		u.email login
	FROM orders
		LEFT JOIN users u ON  orders.user = u.id
	WHERE
		1
		$where
";

$filter[] = array('type',$types,'-статусы-');
$filter[] = array('search');

$form[] = array('select td3','type',array('value'=>array(true,$types)));
//$form[] = array('input td3','date');
$form[] = array('select td6','user',array(
	'value'=>array(true,"SELECT id,email name FROM users WHERE id='".@$post['user']."'"),
	//v1.4.28 - автозаполенние
	'attr'=>'data-url="/admin.php?m=orders&u=get_users"'
));

$form[] = array('select td3','payment',array('value'=>array(true,$config['payments'],'')));
$form[] = array('input td3 datetimepicker','date_paid');
$form[] = array('checkbox','paid');
$form[] = array('checkbox','send',array('name'=>'отправить уведомление','help'=>'Пользователю будет отправлено письмо со статусом и содержанием заказа'));


if ($config['style']!='admin/templates') {
	$form[] = array('basket',true);
}
else {
	$form[] = '<div style="clear:both; background:#E9E9E9; padding:5px 10px; width:875px; margin:0 -10px">';
	$form[] = '<table class="product_list">';
	$form[] = '<tr data-i="0">';
	$form[] = '<th>ID</th>';
	$form[] = '<th >название</th>';
	$form[] = '<th>количество</th>';
	$form[] = '<th>цена</th>';
	$form[] = '<th><a href="#" style="background:#35B374; display:inline-block; padding:2px; border-radius:10px"><span class="sprite plus"></span></a></th>';
	$form[] = '</tr>';

	$template['product'] = '
	<tr data-i="{i}">
		<td><input name="basket[products][{i}][id]" value="{id}" /></td>
		<td><input name="basket[products][{i}][name]" value="{name}" class="product_name"/></td>
		<td><input name="basket[products][{i}][count]" value="{count}" /></td>
		<td><input name="basket[products][{i}][price]" value="{price}" /></td>
		<td><a href="#" class="sprite boolean_0"></a></td>
	</tr>
';
	if (isset($post['basket'])) {
		$basket = unserialize($post['basket']); //print_r ($basket);
		if (isset($basket['products']) && is_array($basket['products'])) foreach ($basket['products'] as $key => $val) {
			$val['i'] = $key;
			//v1.4.24 - для кавычек товара
			$val['name'] = htmlspecialchars($val['name']);
			$form[] = template($template['product'], $val);
		}
	}
	$form[] = '</table>';
	$form[] = 'clear';
	$form[] = '</div>';
	$form[] = array('select td4', 'basket[delivery][type]', array(
		'value'=>array(@$basket['delivery']['type'], "SELECT od.id,od.name FROM order_deliveries od WHERE display = 1 ORDER BY od.rank"),
		'name' => 'доставка')
	);
	$form[] = array('input td4 right', 'basket[delivery][cost]', array(
		'name' => 'стомость доставки',
		'value'=>@$basket['delivery']['cost']
	));
	$form[] = array('input td4 right', 'total',);
	$form[] = 'clear';
	$form[] = array('textarea td12', 'basket[text]', array(
		'name' => 'комментарий',
		'value'=>@$basket['text']
	));

	$form[] = '<h2>Данные клиента</h2>';
	$form[] = array('input td3', 'email', true);
	if ($fields = mysql_select("SELECT * FROM user_fields WHERE display = 1 ORDER BY `rank` DESC", 'rows')) {
		foreach ($fields as $q) {
			$values = unserialize($q['values']);
			if (!isset($basket['user'][$q['id']][0])) $basket['user'][$q['id']][0] = '';
			if ($q['type'] == 1) //input
				$form[] = array('input td3', 'basket[user][' . $q['id'] . '][]', $basket['user'][$q['id']][0], array('name' => $q['name']));
			elseif ($q['type'] == 2) //select
				$form[] = array('select td3', 'basket[user][' . $q['id'] . '][]', array(
					'value'=>array($basket['user'][$q['id']][0], $values),
					'name' => $q['name']
				));
			else //textarea
				$form[] = array('textarea td12', 'basket[user][' . $q['id'] . '][]', $basket['user'][$q['id']][0], array('name' => $q['name']));
		}
	}


	//шаблоны товара используются для js
	$content = '<div style="display:none">';
	$content .= '<textarea id="template_product">' . htmlspecialchars($template['product']) . '</textarea>';
	$content .= '</div>';
	$content .= '<style type="text/css">
.form .product_list {width:100%}
.form .product_list th {text-align:left; padding:0 0 5px;}
.form .product_list td {border-top:1px solid #F3F3F3; padding:5px 0; vertical-align:top;}
.form .product_list input {text-align:right; border:1px solid gray; margin:0; padding:0 2px; height:19px; width:70px}
.form .product_list .product_name {width:550px; text-align:left;}
.form .product_list td td {border:none}
</style>';
	$content .= '<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	$(document).on("click",".product_list th a",function(){
		var i = $(this).parents("table").find("tr:last").data("i");
		i++;
		var content = $("#template_product").val();
		content = content.replace(/{i}/g,i);
		content = content.replace(/{[\w]*}/g,"");
		$(this).parents("table").append(content);
		return false;
	});
	$(document).on("click",".product_list td a",function(){
		$(this).parents("tr").remove();
		return false;
	});
})	
</script>';
}

//v1.4.32 - добавление пользователя
$content .= '
<script type="text/javascript">document.addEventListener("DOMContentLoaded", function () {
	$(document).on("select2:select","select[name=user]", function (e) {
		var box = $(this),
			data = e.params.data;
		//console.log(data);
		if (data.id=="add"){
			$.post(
				"admin.php?m=users&u=edit&option=json",
				{"email":data.value,"change":1},
				function(response){
					//console.log(response.id+ " "+data.value);
					//чистим селект от value=add
					$(box).html("");
					if (response.id) {
						//заменяем данные в селекте на выбранную запись
						$(box).html("<option value="+response.id+">"+data.value+"</option>");
					}
					else {
						alert("Ошибка при добавлении пользователя");
					}
				}
			)
		}
	})
});
</script>';