<?php
require 'inc.php';
require_once (SYSTEM_ROOT."eupay/eupay_config.php");
require_once (SYSTEM_ROOT."eupay/eupay_api.php");
@header('Content-Type: text/html; charset=UTF-8');
$trade_no=daddslashes($_GET['trade_no']);
if($config['eupay_api'] == 0 || $config['eupay_api'] <> 1)exit('当前支付接口未开启');
$row=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no='{$trade_no}' LIMIT 1");
if(!$row)exit('该订单号不存在，请返回来源地重新发起请求！');
$data = array("trade_no" => $trade_no,"price" => $row['money'],"notify_url" => $siteurl.'eupay_notify.php');
$euayApi = (new eupay)->submit($config, $data);
return $euayApi;