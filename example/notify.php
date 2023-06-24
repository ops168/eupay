<?php
/* *
 * 功能：异步通知
 * 说明：
 * 以下代码只是为了方便测试而提供的样例代码，用户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 用户编号和用户密钥请修改后再测试。
 * */
$user_id = '1';//用户编号
$user_key = 'xxx';//用户密钥
$data = array(
	'uid' => $user_id,
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
$sign = md5($sign.$user_key);
if($data['sign'] != $sign || $data['status'] != 2){
	return 'fail';
} else {
	//合法的数据
	return 'ok';
}