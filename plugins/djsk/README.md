## 简介

此插件适用于使用独角数卡系统对接EUPAY的USDT(TRC-20)收款插件。

本源码仅供个人学习研究所用，任何人或组织用作他用，产生的任何后果责任自负。

## 流程

1. 将此目录下的全部文件和文件夹全部上传到独角数卡系统网站的根目录
2. 在独角数卡系统网站的根目录中依次进入`/routes/common`目录，找到名为`pay.php`文件并打开
3. 在最底下的`});`符号前面添加如下代码并保存
```php 
Route::get('eupay/{payway}/{orderSN}', 'EupayController@gateway');
Route::any('eupay/notify_url', 'EupayController@notifyUrl');
Route::get('eupay/check', 'EupayController@check');
```
4. 完成上面步骤后，在独角数卡后台->`配置`->`支付通道`->点击`新增`，照着下面的说明内容填写，没有说明的留空
5. 支付名称填`USDT`，商户ID填你在EUPAY的`用户编号`，商户密钥填你在EUPAY的`用户密钥`，支付标识填`Eupay`，支付场景选`通用`，支付方式选`跳转`，支付处理路由填`/pay/eupay`
6. 保存后在商品页面会看到有支付方式为USDT的选项，即可使用

## 申请
 - [EUPAY官网](https://www.eupay.cn)
 - [EUPAY接口申请](https://www.eupay.cn/User_Reg.html)
---  

**如果没搞错没意外，至此独角数卡便能正常使用EUPAY插件进行收款。**