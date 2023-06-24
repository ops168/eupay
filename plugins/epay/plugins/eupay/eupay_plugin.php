<?php

class eupay_plugin
{
	static public $info = [
		'name'        => 'eupay',
		'showname'    => 'EUPAY支付',
		'author'      => 'EUPAY',
		'link'        => 'https://eupay.cn', 
		'types'       => ['usdt'],
		'inputs' => [
			'appid' => [
				'name' => '用户编号',
				'type' => 'input',
				'note' => '',
			],
			'appkey' => [
				'name' => '用户密钥',
				'type' => 'input',
				'note' => '',
			],
		],
		'select' => null,
		'note' => null,
	];

	static public function submit()
	{
		global $channel, $order, $conf, $cdnpublic;
		require(PAY_ROOT."inc/EuPayService.php");
		$parameter = array(
        	"uid" => trim($channel['appid']),
        	"order_id" => TRADE_NO,
        	"amount" => (double)$order['realmoney'],
        	"notify_url" => $conf['localurl'].'pay/notify/'.TRADE_NO.'/'
		);
        ksort($parameter);
        $datastr = http_build_query($parameter);
        $postdata = $datastr.'&sign='.md5($datastr.trim($channel['appkey']));
        $result = (new EuPayService)->Post_Request($postdata);
        $res_array = json_decode($result,true);
        if($res_array['code'] == 200){
            $usdt_amount = $res_array['data'][0]['actual_amount'];
            $address = $res_array['data'][0]['token'];
            $valid = $res_array['data'][0]['expiration_time'];
            ob_clean();
            header("application:text/html;charset=UTF-8");
            define('PLUGIN_PATH', PLUGIN_ROOT . PAY_PLUGIN . '/');
            define('PLUGIN_STATIC', 'https://cdn.vizan.cc/other/epay_usdt');
            require_once PLUGIN_PATH . '/inc/Pay.php';
            exit(0);
        } else {
            exit($res_array['msg']);
        }
	}
	static public function notify()
	{
	    global $channel, $order, $DB;
        $data = array(
        	'uid' => trim($channel['appid']),
        	'trade_id' => $_GET['trade_id'],
        	'order_id' => $_GET['order_id'],
        	'amount' => (double)$_GET['amount'],
        	'actual_amount' => (double)$_GET['actual_amount'],
        	'token' => $_GET['token'],
        	'block_transaction_id' => $_GET['block_transaction_id'],
        	'status' => $_GET['status'],
        );
        $data['sign'] = $_GET['sign'];
        ksort($data);
        reset($data);
        $sign = '';
        $urls = '';
        foreach ($data as $key => $val) {
        	if ($val == '') continue;
        	if ($key != 'sign') {
        		if ($sign != '') {
        			$sign .= "&";
        			$urls .= "&";
        		}
        		$sign .= "$key=$val";
        		$urls .= "$key=" . urlencode($val);
        	}
        }
        $sign = md5($sign.$channel['appkey']);
        if($data['sign'] != $sign || $data['status'] != 2){
        		return ['type'=>'html','data'=>'fail'];
        } else {
        	if($_GET['status'] == 2){
				if($_GET['order_id'] == TRADE_NO){
				    $DB->exec('update pay_order set param = '.$_GET['block_transaction_id'].' where trade_no = '.$_GET['order_id']);
					processNotify($order, $_GET['trade_id']);
				}
        	}
        	return ['type'=>'html','data'=>'ok'];
        }
	}
}