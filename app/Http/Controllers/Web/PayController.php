<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Service\Pay\AlipayService;
use App\Http\Service\Pay\WechatPayService;
use App\Models\Pay\DemoOrder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PayController extends Controller
{
    /**
     * 支付页面.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pay()
    {
        return view('web.pay.pay');
    }

    /**
     * 去支付.
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     *
     * @throws Exception
     */
    public function toPay(Request $request)
    {
        $money = $request->input('money');
        $payWay = $request->input('pay_way');

        if (0 == strlen($money) || !is_numeric($money)) {
            if ($request->ajax()) {
                throw new Exception('请输入金额');
            } else {
                return back()->withInput()->with('error', '请输入金额');
            }
        }

        if (is_wechat() && 2 == $payWay) {
            if ($request->ajax()) {
                throw new Exception('请使用第三方浏览器进行支付');
            } else {
                return back()->withInput()->with('error', '请使用第三方浏览器进行支付');
            }
        }

        $order = DemoOrder::create([
            'user_id' => 0,
            'price' => $money,
            'title' => '账户充值：￥'.money_show($money),
            'status' => 0,
        ]);
        $bill = $order->bills()->create([
            'pay_no' => $order->number,
            'pay_amount' => $order->price,
            'pay_way' => $payWay,
            'title' => $order->title,
        ]);
        $payData = [
            'pay_no' => $bill->pay_no,
            'pay_amount' => $bill->pay_amount,
            'title' => $bill->title,
        ];

        switch ($payWay) {
            case 1:
                $result = (new WechatPayService())->pay($payData, 'scan');
                if ('NATIVE' == $result['trade_type']) {
                    $base64 = base64_encode(QrCode::format('png')
                        ->size(200)
                        ->margin(0)
                        ->generate($result['code_url']));

                    return [
                        'code' => 0,
                        'msg' => '',
                        'data' => [
                            'id' => $order->id,
                            'img' => 'data:image/png;base64,'.$base64,
                        ],
                    ];
                }

                return ['code' => 1, 'msg' => '下单失败，请重试'];
                break;

            case 2:
                return (new AlipayService())->pay($payData);
                break;

            default:
                if ($request->ajax()) {
                    throw new Exception('付款方式错误');
                } else {
                    return back()->withInput()->with('error', '付款方式错误');
                }
                break;
        }
    }

    /**
     * 检查支付.
     *
     * @return array
     */
    public function checkPay(Request $request)
    {
        $id = $request->input('id');
        $order = DemoOrder::find($id);
        if (empty($order)) {
            return [
                'code' => 1,
                'msg' => '支付失败，订单不存在',
                'data' => [
                    'wait' => 0,
                    'url' => route('home'),
                ],
            ];
        }
        if (1 == $order->status && !empty($order->pay_at)) {
            return [
                'code' => 0,
                'msg' => '支付成功',
                'data' => [
                    'wait' => 0,
                    'url' => $order->payResultUrl(),
                ],
            ];
        }

        return [
            'code' => 1,
            'msg' => '',
            'data' => [
                'wait' => 1,
                'url' => '',
            ],
        ];
    }

    /**
     * 支付结果页.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payResult(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            abort(404);
        }
        $order = DemoOrder::with(['bill'])->where('id', $id)->where('created_at', '>=', now()->subDay())->firstOrFail();
        if (1 == $order->status) {
            if (Cache::missing('DemoOrder'.$id)) {
                abort(404);
            }

            return view('web.pay.pay_success', compact('order'));
        }

        return view('web.pay.pay_fail', compact('order'));
    }
}
