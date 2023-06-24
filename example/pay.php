<?php
/* *
 * 功能：发起订单请求
 * 说明：
 * 以下代码只是为了方便测试而提供的样例代码，用户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 用户编号和用户密钥请修改后再测试。
 * */
$user_id = '1';//用户编号
$user_key = 'xxx';//用户密钥
$api_url = 'http://api.eupay.cn/Go_Submit';//请求地址
function Post_Request($url,$data)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
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

$data = array(
	"uid" => $user_id,
	"order_id" => time(),
	"amount" => (double)12,
	"notify_url" => 'http://example.com/redirect'
);
ksort($data);
$datastr = http_build_query($data);
$postdata = $datastr.'&sign='.md5($datastr.$user_key);
$result = Post_Request($api_url,$postdata);
$res_array = json_decode($result,true);
print_r($res_array);