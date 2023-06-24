<?php
require_once("./inc.php");
require_once (SYSTEM_ROOT."eupay/eupay_config.php");
$data = array(
	"uid"                   => $config['uid'],
	'trade_id'              => $_GET['trade_id'],
	'order_id'              => $_GET['order_id'],
	'amount'                => (double)$_GET['amount'],
	'actual_amount'         => (double)$_GET['actual_amount'],
	'token'                 => $_GET['token'],
	'block_transaction_id'  => $_GET['block_transaction_id'],
	'status'                => $_GET['status'],
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
$sign = md5($sign.$config['key']);
if($data['sign'] != $sign || $data['status'] != 2){
		return ['type'=>'html','data'=>'fail'];
} else {
    $srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no='{$_GET['order_id']}' LIMIT 1");
	if($_GET['status'] == 2 && $srow['status']==0 && round($srow['money'],2)==round($data['amount'],2)){
		if($DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`='{$_GET['order_id']}'")){
			$DB->exec("UPDATE `pre_pay` SET `endtime` ='$date',`api_trade_no` ='{$_GET['trade_id']}' WHERE `trade_no`='{$_GET['order_id']}'");
			processOrder($srow);
		}	    
	}
	echo "ok";
}

?>