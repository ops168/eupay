# 前言
开发者可通过`EUPAY`提供的`API`接口将交易功能集成至任何系统

# 接口统一加密方式
### 签名算法MD5
签名生成的通用步骤如下：  

◆ 将所有非空值的参数按照参数名(a-z)排序并拼接为URL键值格式的待加密参数,如a=b&c=d&e=f;  

◆ 将待加密参数与用户密钥拼接进行MD5运算,如a=b&c=d&e=f+用户密钥,最终得出32位字节小写的sign签名参数;

重要规则:  
◆ 参数名ASCII码从小到大排序(字典序);  
◆ sign键值不参与签名;     
◆ 参数名区分大小写;  

# 创建交易接口
◆ 请求地址:http://api.eupay.cn/Go_Submit  
◆ 请求方式:POST  
◆ 请求参数:
|参数名|类型|描述|
|---|---|---|
|uid|Int|用户编号|
|order_id|String|商户订单号|
|amount|Float|请求金额[CNY]|
|notify_url|String|异步回调地址|
|sign|String|MD5签名字符串|

◆ 请求示例  
> 假设用户密钥为：`987654321`  
> 假设传送参数如下:  
```
uid = 1
order_id = 12345678
amount = 1
notify_url = http://www.example.com/notify
```  
> 按照接口统一加密方式将参数排序和拼接为URL格式：
```
amount=1&notify_url=http://example.com/notify&order_id=12345678&uid=1
```
> 拼接用户密钥KEY并加密：
```
MD5(amount=1&notify_url=http://example.com/notify&order_id=12345678&uid=1987654321)
```
> 加密后得到的签名字符串：
```
0e783f2e218d327cb5e3e3c2bf35717f
```
> 最终得到提交的参数：    
```
uid = 1
order_id = 12345678
amount = 1
notify_url = http://www.example.com/notify
sign = 0e783f2e218d327cb5e3e3c2bf35717f
```
◆ 返回结果  
> 参数类型：JSON  
> 参数结构：  

|参数名|类型|描述|
|---|---|---|
|» code|Int|状态码，200代表成功|
|» msg|String|信息提示|
|» data|Object|返回数据|
|»» trade_id|String|系统交易号|
|»» order_id|String|商户订单号|
|»» amount|Float|订单金额[CNY]|
|»» actual_amount|Float|支付金额[USDT]|
|»» token|String|钱包地址|
|»» expiration_time|integer|过期时间戳[秒]|

> 返回示例  
```json
{
  "code": 200,
  "msg": "OK",
  "data": {
    "trade_id": "1234567890",
    "order_id": "12345678",
    "amount": 99.55,
    "actual_amount": 15.62,
    "token": "TNEns8t9jbWENbStkQdVQtHMGpbsYsQjZK",
    "expiration_time": 1648381192
  },
}
```
# 异步通知

支付成功后,`EUPAY`会向目标服务器发生异步通知告知该笔交易已经支付完成。  
目标服务器验证消息签名并处理完成后须返回字符串`ok`,否则`EUPAY`会认为通知失败,将会重新发起通知,最高`5`次。

◆ 请求方式:GET  
◆ 请求参数:  
|参数名|类型|描述|
|---|---|---|
|uid|Int|用户编号|
|trade_id|String|系统交易号|
|order_id|String|商户订单号|
|amount|Float|订单金额[CNY]|
|actual_amount|Float|支付金额[USDT]|
|token|String|钱包地址|
|block_transaction_id|String|区块交易号|
|status|Int|订单状态|
|sign|String|MD5签名字符串|

◆ 请求示例  
```json
{
  "uid": "1",
  "trade_id": "1234567890",
  "order_id": "12345678",
  "amount": 99.55,
  "actual_amount": 15.62,
  "token": "TNEns8t9jbWENbStkQdVQtHMGpbsYsQjZK",
  "block_transaction_id": "123333333321232132131",
  "status": 2
  "sign": "35b0e0e6e6b669787427fee7e8b9b012",
}
```
