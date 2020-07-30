<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 测试
Route::get('test/{name?}', 'Tests\TestController@test');

// 主页
Route::get('/', function () {
    return view('welcome');
});

// 支付通知
Route::any('notify/alipay', 'NotifyController@notifyAlipay'); // 异步通知 - 支付宝 - 支付
Route::any('return/alipay', 'NotifyController@returnAlipay'); // 同步返回 - 支付宝 - 跳转
Route::any('notify/wechat', 'NotifyController@notifyWechat'); // 异步通知 - 微信 - 支付
Route::any('notify/wechat_refund', 'NotifyController@notifyWechatRefund'); // 异步通知 - 微信 - 退款

// 临时支付
Route::get('pay', 'Web\PayController@pay')->name('web.pay'); // 付款页面
Route::post('pay', 'Web\PayController@toPay')->name('web.to_pay'); // 充值请求
Route::post('money_check_pay', 'Web\PayController@checkPay')->name('web.check_pay'); // 充值检查 - 微信
Route::get('pay_result', 'Web\PayController@payResult')->name('web.pay_result'); // 充值结果
