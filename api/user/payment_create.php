<?php

// Создание записи оплаты пользователя (StoreKit)
/*
 * v2.0.0
 * example: /api/user/payment_create
 * method: POST
 * required: key, user_id, package_id, price, product_id, transaction_id, original_transaction_id, purchase_date, signed_transaction_info
 */

include_once __DIR__.'/../_guard.php';

$userId    = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
$packageId = isset($_REQUEST['package_id']) ? intval($_REQUEST['package_id']) : 0;
$price     = isset($_REQUEST['price']) ? (float)str_replace(',', '.', $_REQUEST['price']) : 0.0;

// новые обязательные поля из StoreKit
$productId  = isset($_REQUEST['product_id']) ? trim((string)$_REQUEST['product_id']) : '';
$transactionId = isset($_REQUEST['transaction_id']) ? trim((string)$_REQUEST['transaction_id']) : '';
$originalTransactionId = isset($_REQUEST['original_transaction_id']) ? trim((string)$_REQUEST['original_transaction_id']) : '';
$purchaseDate = isset($_REQUEST['purchase_date']) ? trim((string)$_REQUEST['purchase_date']) : '';
$signedTransactionInfo = isset($_REQUEST['signed_transaction_info']) ? (string)$_REQUEST['signed_transaction_info'] : '';

if ($userId<=0 || $packageId<=0 || $price<=0 || $productId==='' || $transactionId==='' || $originalTransactionId==='' || $purchaseDate==='' || $signedTransactionInfo==='') {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
	$api['message'] = 'user_id, package_id, price, product_id, transaction_id, original_transaction_id, purchase_date, signed_transaction_info are required';
    return;
}

// проверим пользователя
$existsUser = mysql_select("SELECT id FROM users WHERE id='".$userId."' LIMIT 1", 'string');
if (!$existsUser) {
    $api['success'] = 0;
    $api['error'] = 'not_found';
    $api['message'] = 'user not found';
    return;
}

// защита от повторной обработки
$dup = mysql_select("SELECT id FROM user_payment WHERE transaction_id='".mysql_res($transactionId)."' LIMIT 1", 'string');
if ($dup) {
	$api['success'] = 0;
	$api['error'] = 'already_processed';
	$api['message'] = 'transaction already processed';
	return;
}

// верификация у Apple Server API
global $config;
$appleIssuerId = isset($config['apple_issuer_id']) ? $config['apple_issuer_id'] : '';
$appleKeyId = isset($config['apple_key_id']) ? $config['apple_key_id'] : '';
$appleBundleId = isset($config['apple_bundle_id']) ? $config['apple_bundle_id'] : '';
$applePrivateKey = isset($config['apple_private_key']) ? $config['apple_private_key'] : '';
$appleSandbox = isset($config['apple_sandbox']) ? (intval($config['apple_sandbox'])===1) : false;

if ($appleIssuerId==='' || $appleKeyId==='' || $appleBundleId==='' || $applePrivateKey==='') {
	log_add('storekit.log', ['stage'=>'missing_config','issuer'=>$appleIssuerId,'kid'=>$appleKeyId,'bundle'=>$appleBundleId]);
	$api['success'] = 0;
	$api['error'] = 'config_error';
	$api['message'] = 'Apple StoreKit config is missing';
	return;
}

$now = time();
$header = ['alg'=>'ES256','kid'=>$appleKeyId,'typ'=>'JWT'];
$claims = [
	'iss' => $appleIssuerId,
	'iat' => $now,
	'exp' => $now + 1800,
	'aud' => 'appstoreconnect-v1',
	'bid' => $appleBundleId,
];

$base64url = function($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); };
$signJwtEs256 = function($header, $claims, $privateKeyPem) use ($base64url) {
	$h = $base64url(json_encode($header));
	$p = $base64url(json_encode($claims));
	$data = $h.'.'.$p;
	$signature = '';
	$ok = openssl_sign($data, $signature, openssl_pkey_get_private($privateKeyPem), OPENSSL_ALGO_SHA256);
	if (!$ok) return false;
	return $data.'.'.$base64url($signature);
};
$jwt = $signJwtEs256($header, $claims, $applePrivateKey);
if ($jwt===false) {
	log_add('storekit.log', ['stage'=>'jwt_sign_failed']);
	$api['success'] = 0;
	$api['error'] = 'sign_error';
	$api['message'] = 'failed to sign JWT for Apple API';
	return;
}

$appleUrl = $appleSandbox
	? 'https://api.storekit-sandbox.itunes.apple.com/inApps/v1/transactions/'.rawurlencode($transactionId)
	: 'https://api.storekit.itunes.apple.com/inApps/v1/transactions/'.rawurlencode($transactionId);

$ch = curl_init($appleUrl);
curl_setopt_array($ch, [
	CURLOPT_HTTPGET => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HEADER => true,
	CURLOPT_HTTPHEADER => [
		'Authorization: Bearer '.$jwt,
		'Accept: application/json',
	],
	CURLOPT_CONNECTTIMEOUT => 10,
	CURLOPT_TIMEOUT => 20,
]);
$resp = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

$respBody = substr($resp, $headerSize);
log_add('storekit.log', ['stage'=>'verify_request','url'=>$appleUrl,'http_code'=>$httpCode,'curl_error'=>$curlErr,'body_head'=>substr($respBody,0,300)]);

if ($curlErr!=='' || $httpCode<200 || $httpCode>=300 || !$respBody) {
	$api['success'] = 0;
	$api['error'] = 'verify_failed';
	$api['message'] = 'failed to verify transaction with Apple';
	$api['apple_http_code'] = $httpCode;
	return;
}

$verifiedProductId = '';
$verifiedTransactionId = '';
$verifiedOriginalTransactionId = '';
$verifiedPurchaseDateMs = 0;

$isJson = json_decode($respBody, true);
if (is_array($isJson) && isset($isJson['signedTransactionInfo'])) {
	$jws = $isJson['signedTransactionInfo'];
} else {
	$jws = trim($respBody, "\" \n\r\t");
}
$parts = explode('.', $jws);
if (count($parts)>=2) {
	$payload = $parts[1];
	$payloadJson = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	if (is_array($payloadJson)) {
		$verifiedProductId = isset($payloadJson['productId']) ? $payloadJson['productId'] : '';
		$verifiedTransactionId = isset($payloadJson['transactionId']) ? $payloadJson['transactionId'] : '';
		$verifiedOriginalTransactionId = isset($payloadJson['originalTransactionId']) ? $payloadJson['originalTransactionId'] : '';
		$verifiedPurchaseDateMs = isset($payloadJson['purchaseDate']) ? intval($payloadJson['purchaseDate']) : 0;
	}
}

if ($verifiedTransactionId!==$transactionId || $verifiedProductId!==$productId) {
	$api['success'] = 0;
	$api['error'] = 'mismatch';
	$api['message'] = 'verified data mismatch';
	return;
}

$row = array(
	'user_id'    => $userId,
	'package_id' => $packageId,
	'price'      => number_format($price, 2, '.', ''),
	'product_id' => $productId,
	'transaction_id' => $transactionId,
	'original_transaction_id' => $originalTransactionId,
	'purchase_date' => date('Y-m-d H:i:s', strtotime($purchaseDate)),
	'signed_transaction_info' => $signedTransactionInfo,
	'status' => 'completed',
	'verified' => 1,
);

$id = mysql_fn('insert', 'user_payment', $row);

if ($id) {
	if (stripos($productId, 'credits') !== false) {
		$pkgCount = mysql_select("SELECT `count` FROM packages WHERE id='".$packageId."' LIMIT 1", 'string');
		if ($pkgCount) {
			mysql_fn('query', "UPDATE users SET count_generation = count_generation + " . intval($pkgCount) . ", updated_at = NOW() WHERE id = '".$userId."'");
		}
	} elseif ($productId === 'com.watchwalls.unlock_all') {
		mysql_fn('query', "UPDATE users SET has_unlocked_all = 1, updated_at = NOW() WHERE id = '".$userId."'");
	}
	$api['success'] = 1;
	$api['id'] = intval($id);
	$api['message'] = 'created_verified';
} else {
	$api['success'] = 0;
	$api['error'] = 'db_error';
	$api['message'] = 'failed to create payment';
}


