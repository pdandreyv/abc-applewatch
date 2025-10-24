<?php

if ($u[4]) {
	$error++;
}
elseif ($u[2]=='success') {
	$abc['content'] = html_render('order/success');
}
elseif ($u[2]=='fail') {
	$abc['content'] = html_render('order/fail');
}
else {
	//просмотр заказа по хешу
	if ($u[2]>0) {
		$query = "
			SELECT o.*,ot.name".$lang['i']." ot_name,ot.text".$lang['i']." ot_text
			FROM orders o
			LEFT JOIN order_types ot ON ot.id=o.type
			WHERE o.id='".intval($u[2])."'
			LIMIT 1
		"; //echo $query;
		if ($order = mysql_select($query,'row')) {
			$abc['page'] = $order;
			//проверка хеша
			if ($u[3]!=md5($order['id'].$order['created_at'])) {
				$error++;
				unset($order);
			}
		}
		else $error++;
	}
	//обрабока формы
	elseif ($_POST AND @$_SESSION['basket']) {
		$post = stripslashes_smart($_POST);
		//создание массива корзины
		$q['email'] = strtolower($post['email']);
		$q['total'] = 0;
		$q['count'] = 0;
		$q['user'] = serialize($post['fields']);
		$q['delivery_type'] = abs(intval($post['delivery_type']));
		$q['text'] = $post['text'];
		//создание массива товаров
		if (isset($post['count']) && is_array($post['count'])) {
			foreach ($post['count'] as $k=>$v) {
				if ($v>0 && isset($_SESSION['basket']['products'][$k])) {
					$q['products'][$k] = $_SESSION['basket']['products'][$k];
					$q['products'][$k]['count'] = $v;
					$q['total']+= $q['products'][$k]['price']*$v;
					$q['count']+= $v;
				}
			}
		}
		//стоимость доставки
		$d = mysql_select("SELECT * FROM order_deliveries WHERE id='".$q['delivery_type']."'",'row');
		$q['delivery_cost'] = $d['free']>0 && $q['total']>$d['free'] ? 0 : $d['cost'];
		if ($q['total']>0) {
			$q['total']+= $q['delivery_cost'];
			$o = mysql_select("SELECT * FROM order_types WHERE display=1 ORDER BY `rank` LIMIT 1",'row');
			$order = array(
				'paid'	=> 0,
				'type'	=> $o['id'],
				//'date'	=> date('Y-m-d H:i:s'),
				'created_at' => $config['datetime'],
				'email'	=> $q['email'],
				'total'	=> $q['total'],
				'user'	=> isset($user['id']) ? $user['id'] : 0,
				'basket' => array(
					'products' => $q['products'],
					'delivery' => array(
						'type'=>$q['delivery_type'],
						'cost'=>$q['delivery_cost']
					),
					'user'=> $post['fields'],
					'text'=> $q['text'],
				)
			);
			$order['basket'] = serialize($order['basket']);
			if ($abc['page']['id'] = $order['id']=mysql_fn('insert','orders',$order)) {
				$_SESSION['basket']=array();
			}
			$order['ot_name'] = $o['name'];
			$order['ot_text'] = $o['text'];
			$abc['page'] = $order;
			require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
			mailer('basket',$lang['id'],$order,$order['email']);
			mailer('basket',$lang['id'],$order);
			//$subject = $lang['basket_order_name'].' № '.$order['id'].' '.$lang['basket_order_from'].' '.date2($order['date'],'%d.%m.%Y');
			//$text = html_array('order/mail',$order);
			//email($config['email'],$config['email'],$subject,$text,$order['email']);
			//email($config['email'],$order['email'],$subject,$text);
		}
	}
	//корзина
	else {
		//удаление товара
		if (isset($_GET['delete'])) {
			if (isset($_SESSION['basket']['products'][$_GET['delete']])) {
				unset ($_SESSION['basket']['products'][$_GET['delete']]);
				//пересчет корзины
				$total = $count = 0;
				foreach ($_SESSION['basket']['products'] as $k=>$v) {
					$count+=$v['count'];
					$total+=$v['price']*$v['count'];
				}
				$_SESSION['basket']['total'] = $total;
				$_SESSION['basket']['count'] = $count;
			}
		}
		$abc['basket'] = isset($_SESSION['basket']) ? $_SESSION['basket'] : array();
		//добавления параметров из настроек пользователя
		if(access('user auth')) {
			$abc['basket']['user'] = $user['fields'];
			$abc['basket']['email'] = $user['email'];
		}
	}

	if ($error) {
		//если есть ошибка то шаблон корзины не грузим
	}
	//шаблон заказа
	elseif (isset($order)) {
		$abc['page']['name'] = i18n('basket|order_name').' № '.$order['id'].' '.i18n('basket|order_from').' '.date2($order['created_at'],'%d.%m.%Y');
		$abc['layout'] = 'order';
	}
}