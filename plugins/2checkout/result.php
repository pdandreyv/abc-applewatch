<?php
$hashSecretWord = 'tango'; //2Checkout Secret Word
$hashSid = 901272222; //2Checkout account number
$hashTotal = '25.99'; //Sale total to validate against
$hashOrder = $_REQUEST['order_number']; //2Checkout Order Number
print_r($_REQUEST);
$StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));
if ($StringToHash != $_REQUEST['key']) {
	$result = 'Fail - Hash Mismatch';
} else {
	$result = 'Success - Hash Matched';
}

echo $result;