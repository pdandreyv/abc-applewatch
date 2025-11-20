<?php

// App Store Server Notifications V2 webhook
/*
 * v1.0.0
 * method: POST
 * body: { "signedPayload": "..." }
 */

include_once __DIR__.'/../_guard.php';

// читаем тело
$raw = file_get_contents('php://input');
$json = json_decode($raw, true);
if (!is_array($json) || !isset($json['signedPayload'])) {
	$api['success'] = 0;
	$api['error'] = 'bad_request';
	$api['message'] = 'signedPayload required';
	return;
}

$signedPayload = $json['signedPayload'];

// декодируем основной JWS (header.payload.signature)
$parts = explode('.', $signedPayload);
if (count($parts) < 2) {
	$api['success'] = 0;
	$api['error'] = 'bad_payload';
	$api['message'] = 'invalid signedPayload';
	return;
}
$payload = $parts[1];
$payloadJson = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
if (!is_array($payloadJson)) {
	$api['success'] = 0;
	$api['error'] = 'bad_payload';
	$api['message'] = 'invalid payload json';
	return;
}

$notificationType = isset($payloadJson['notificationType']) ? $payloadJson['notificationType'] : '';
$subtype = isset($payloadJson['subtype']) ? $payloadJson['subtype'] : '';
$data = isset($payloadJson['data']) ? $payloadJson['data'] : array();
$signedTransactionInfo = isset($data['signedTransactionInfo']) ? $data['signedTransactionInfo'] : '';

log_add('storekit.log', [
	'stage' => 'webhook_received',
	'notificationType' => $notificationType,
	'subtype' => $subtype,
]);

// декодируем транзакцию
$tx = array();
if ($signedTransactionInfo) {
	$tp = explode('.', $signedTransactionInfo);
	if (count($tp)>=2) {
		$txPayload = $tp[1];
		$tx = json_decode(base64_decode(strtr($txPayload, '-_', '+/')), true);
	}
}

$transactionId = isset($tx['transactionId']) ? $tx['transactionId'] : '';
$originalTransactionId = isset($tx['originalTransactionId']) ? $tx['originalTransactionId'] : '';
$productId = isset($tx['productId']) ? $tx['productId'] : '';
$revocationDateMs = isset($tx['revocationDate']) ? intval($tx['revocationDate']) : 0;
$revocationReason = isset($tx['revocationReason']) ? intval($tx['revocationReason']) : null;

// ищем платеж
$payment = false;
if ($transactionId!=='') {
	$payment = mysql_select("SELECT * FROM user_payment WHERE transaction_id='".mysql_res($transactionId)."' LIMIT 1", 'row');
}
if (!$payment && $originalTransactionId!=='') {
	$payment = mysql_select("SELECT * FROM user_payment WHERE original_transaction_id='".mysql_res($originalTransactionId)."' LIMIT 1", 'row');
}

if (!$payment) {
	log_add('storekit.log', ['stage'=>'payment_not_found','transactionId'=>$transactionId,'originalTransactionId'=>$originalTransactionId]);
	$api['success'] = 1;
	$api['message'] = 'ok';
	return;
}

$userId = intval($payment['user_id']);
$paymentId = intval($payment['id']);

// обработка REFUND/REVOKE
if ($notificationType==='REFUND' || $notificationType==='REVOKE') {
	if ($payment['status']!=='refunded' && $payment['status']!=='revoked') {
		$newStatus = ($notificationType==='REFUND') ? 'refunded' : 'revoked';
		$refundDate = $revocationDateMs ? date('Y-m-d H:i:s', intval($revocationDateMs/1000)) : date('Y-m-d H:i:s');
		mysql_fn('update','user_payment', array(
			'id' => $paymentId,
			'status' => $newStatus,
			'refund_date' => $refundDate,
			'revocation_reason' => ($revocationReason===null ? null : (string)$revocationReason),
		));

		// откат начислений
		if (stripos($productId, 'credits') !== false) {
			$pkgCount = mysql_select("SELECT `count` FROM packages WHERE id='".intval($payment['package_id'])."' LIMIT 1", 'string');
			if ($pkgCount) {
				mysql_fn('query', "UPDATE users SET count_generation = GREATEST(count_generation - ".intval($pkgCount).", 0), updated_at = NOW() WHERE id = '".$userId."'");
			}
		} elseif ($productId === 'com.watchwalls.unlock_all') {
			mysql_fn('query', "UPDATE users SET has_unlocked_all = 0, updated_at = NOW() WHERE id = '".$userId."'");
		}

		// лог
		mysql_fn('insert','refund_log', array(
			'user_id' => $userId,
			'payment_id' => $paymentId,
			'transaction_id' => $transactionId,
			'product_id' => $productId,
			'revocation_reason' => ($revocationReason===null ? null : intval($revocationReason)),
		));
	}
}

$api['success'] = 1;
$api['message'] = 'ok';


