<?php
require('globalpay.php');

$privateKey = "";
$token = "";

$globalpay = new GlobalpayAPI($privateKey, $token);

try {

    //$balance = $globalpay->GetBalance();
    //print_r ($balance);

    $rates = $globalpay->GetRates();
    print_r($rates);

    //$transactions = $globalpay->GetTransactions('2022-01-01','2022-12-31');
    //print_r ($transactions);

    //$transaction = $globalpay->CreateTransaction(0.0001, 'BTC', 'AABBCCDDEE', 'https://mysite.com/transactions/end', 'user@mysite.com');
    //print_r ($transaction);

} catch (Exception $e) {
    print_r($e);
}