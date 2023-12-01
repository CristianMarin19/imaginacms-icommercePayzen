<?php


/**
* Encript url to reedirect
* @param  $orderID
* @param  $transactionID
* @return $url
*/
if (!function_exists('paymentezEncriptUrl')) {

     function paymentezEncriptUrl($orderID,$transactionID){

        $url = "{$orderID}-{$transactionID}";
        $encrip = base64_encode($url);

        return  $encrip;

    }
}

/**
* Decript url to get data   
* @param  $eUrl
* @return array
*/
if (!function_exists('paymentezDecriptUrl')) {

    function paymentezDecriptUrl($eUrl){

        $decrip = base64_decode($eUrl);
        $infor = explode('-',$decrip);
        
        return  $infor;

    }
}