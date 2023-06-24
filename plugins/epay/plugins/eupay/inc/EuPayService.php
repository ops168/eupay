<?php

class EuPayService {
	public $gateway_url = "http://api.eupay.cn/Go_Submit";
	public function Post_Request($data)
    {
    	$curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, $this->gateway_url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    	curl_setopt($curl, CURLOPT_POST, 1);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	$result = curl_exec($curl);
    	curl_close($curl);
    	return $result;
    }

}