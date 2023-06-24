<?php
class eupay
{
    public $gateway_url = "http://api.eupay.cn/Go_Submit";
	public function submit($config,$data)
	{
		$parameter = array(
        	"uid"        => $config['uid'],
        	"order_id"   => $data['trade_no'],
        	"amount"     => (double)$data['price'],
        	"notify_url" => $data['notify_url']
		);
        ksort($parameter);
        $datastr = http_build_query($parameter);
        $postdata = $datastr.'&sign='.md5($datastr.$config['key']);
        $result = $this->Post_Request($postdata);
        $res_array = json_decode($result,true);
        if($res_array['code'] == 200){
            $usdt_amount = $res_array['data'][0]['actual_amount'];
            $address = $res_array['data'][0]['token'];
            $valid = $res_array['data'][0]['expiration_time'];
            $order_id = $res_array['data'][0]['order_id'];
            ob_clean();
            header("application:text/html;charset=UTF-8");
            define('PLUGIN_STATIC', 'https://cdn.vizan.cc/other/epay_usdt');
            require_once 'eupay_pay.php';
            exit(0);
        } else {
            exit($res_array['msg']);
        }
	}
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