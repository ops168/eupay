<?php

namespace App\Http\Controllers\Pay;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\PayController;
use Illuminate\Http\Request;

class EupayController extends PayController
{
    public function gateway(string $payway, string $orderSN)
    {
        try {
            // 加载网关
            $this->loadGateWay($orderSN, $payway);
            $params = [
            	'uid' => $this->payGateway->merchant_id,
            	'order_id' => $this->order->order_sn,
            	'amount' => (float)$this->order->actual_price,
            	'notify_url' => url($this->payGateway->pay_handleroute . '/notify_url'),
            ];
            ksort($params);
            $paramsstr = http_build_query($params);
            $postdata = $paramsstr.'&sign='.md5($paramsstr.$this->payGateway->merchant_pem);
            $url = 'http://api.eupay.cn/Go_Submit';
            $result = $this->Post_Request($url,$postdata);
            $res_array = json_decode($result,true);
            if($res_array['code'] == 200){
                $res = $res_array['data'][0];
                $html = "<html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no\"/>
        <meta name=\"renderer\" content=\"webkit\">
        <meta name=\"HandheldFriendly\" content=\"True\"/>
        <meta name=\"MobileOptimized\" content=\"320\"/>
        <meta name=\"format-detection\" content=\"telephone=no\"/>
        <meta name=\"apple-mobile-web-app-capable\" content=\"yes\"/>
        <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\"/>
        <link rel=\"shortcut icon\" href=\"https://cdn.vizan.cc/other/epay_usdt/img/tether.svg\"/>
        <title>USDT 在线收款</title>
        <link href=\"https://cdn.vizan.cc/other/epay_usdt/css/main.min.css\" rel=\"stylesheet\"/>
    </head>
    <body>
    <div class=\"container\">
        <div class=\"header\">
            <div class=\"icon\">
                <img class=\"logo\" src=\"https://cdn.vizan.cc/other/epay_usdt/img/tether.svg\" alt=\"logo\">
            </div>
            <label>
                请扫描二维码或点击金额和地址粘贴转账USDT(trc-20)支付。<br> <b>转账金额必须为下方显示的金额且需要在倒计时内完成转账，否则无法被系统确认！</b>
            </label>
        </div>
        <div class=\"content\">
            <div class=\"section\">
                <div class=\"title\">
                    <h1 class=\"amount parse-amount\" data-clipboard-text=\"{$res['actual_amount']}\" id=\"usdt\">{$res['actual_amount']} <span>USDT.TRC20</span></h1>
                </div>
                <div class=\"address parse-action\" data-clipboard-text=\"{$res['token']}\" id=\"address\">{$res['token']}</div>
                <div class=\"main\">
                    <div class=\"qr-image\" id=\"qrcode\"></div>
                </div>
                <div class=\"timer\">
                    <ul class=\"downcount\">
                        <li>
                            <span class=\"hours\">00</span>
                            <p class=\"hours_ref\">时</p>
                        </li>
                        <li class=\"seperator\">:</li>
                        <li>
                            <span class=\"minutes\">00</span>
                            <p class=\"minutes_ref\">分</p>
                        </li>
                        <li class=\"seperator\">:</li>
                        <li>
                            <span class=\"seconds\">00</span>
                            <p class=\"seconds_ref\">秒</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
		<p align=\"center\"><a href=\"https://eupay.cn\" target=\"_blank\">EUPAY</a></p>
    </div>
    <script src=\"https://cdn.vizan.cc/other/epay_usdt/js/jquery.min.js\"></script>
    <script src=\"https://cdn.vizan.cc/other/epay_usdt/js/clipboard.min.js\"></script>
    <script src=\"//cdn.staticfile.org/layer/3.1.1/layer.js\"></script>
    <script src=\"//cdn.staticfile.org/jquery.qrcode/1.0/jquery.qrcode.min.js\"></script>
    <script>
    function loadmsg() {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/pay/eupay/check/?orderid={$res['order_id']}',
            timeout: 10000, //ajax请求超时时间10s
            success: function (data, textStatus) {
                if (data.code == 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.1, time: 15000});
                    setTimeout(window.location.href = \"/detail-order-sn/{$res['order_id']}\", 1000);
                } else {
                    setTimeout('loadmsg()', 2000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == 'timeout') {
                    setTimeout('loadmsg()', 1000);
                } else { //异常
                    setTimeout('loadmsg()', 3000);
                }
            }
        });
    }

    $(function () {
        $('#qrcode').qrcode({
            text: '{$res['token']}',
            width: 230,
            height: 230,
            foreground: '#000000',
            background: '#ffffff',
            typeNumber: -1
        });

        (new Clipboard('#usdt')).on('success', function (e) {
            layer.msg('金额复制成功');
        });
        (new Clipboard('#address')).on('success', function (e) {
            layer.msg('地址复制成功');
        });

        function clock() {
            let timeout = '{$res['expiration_time']}';
            let now = Math.round(new Date() / 1000);
            let second = timeout - now;
            let minute = Math.floor(second / 60);
            let hour = Math.floor(minute / 60);
            if (second <= 0) {
                layer.alert('支付超时，请重新发起支付！', {icon: 5});
                return;
            }
            $('.hours').text(hour.toString().padStart(2, '0'));
            $('.minutes').text(minute.toString().padStart(2, '0'));
            $('.seconds').text((second % 60).toString().padStart(2, '0'));
            return setTimeout(clock, 1000);
        }
        setTimeout(clock, 1000);
        setTimeout('loadmsg()', 2000);
    });
    </script>
    </body>";
                return $html;
            } else {
                $msg = $res_array['msg'];
            }
            
        } catch (RuleValidationException $exception) {
            return $this->err($exception->getMessage());
        }
    }
    public function notifyUrl(Request $request)
    {
        $data = $request->all();
        $order = $this->orderService->detailOrderSN($data['order_id']);
        if (!$order) {
            return 'fail';
        }
        $payGateway = $this->payService->detail($order->pay_id);
        if(!$payGateway) {
            return 'fail';
        }
        if($payGateway->pay_handleroute != '/pay/eupay'){
            return 'fail';
        }
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
        $sign = md5($sign.$payGateway->merchant_pem);
        if($data['sign'] != $sign){
            return 'fail';
        } else {
            $this->orderProcessService->completedOrder($data['order_id'], $data['amount'], $data['trade_id']);
            return 'ok';
        }
    }    
    public function check(Request $request)
    {
        $data = $request->all();
        $cacheord = $this->orderService->detailOrderSN($data['orderid']);
		if($cacheord['status'] == 4){
			$fdata = '{"code":"1"}';
		}else{
			$fdata = '{"code":"0"}';
		}
		echo $fdata;
		die();
    }
    public function Post_Request($url,$data)
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
}
