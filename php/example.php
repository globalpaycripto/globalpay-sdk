<?php
require('globalpay.php');

$privateKey = "";
$token = "";

$globalpay = new GlobalpayAPI($privateKey, $token);

try {

    //$balance = $globalpay->GetBalance();
    //print_r (json_encode($balance,JSON_PRETTY_PRINT,512));

    //$rates = $globalpay->GetRates();
    //print_r (json_encode($rates,JSON_PRETTY_PRINT,512));

    //$transactions = $globalpay->GetTransactions('2022-01-01','2022-12-31','BTC','CPGD36JZDOAGIRWEF0TRETPJDU');
    //print_r (json_encode($transactions,JSON_PRETTY_PRINT,512));

    $transaction = $globalpay->CreateTransaction(0.04447, 'LTCT', 'AABBCCDDEE', 'https://mysite.com/transactions/end', 'user@mysite.com');
    print_r (json_encode($transaction,JSON_PRETTY_PRINT,512));

} catch (Exception $e) {
    print_r($e);
}
