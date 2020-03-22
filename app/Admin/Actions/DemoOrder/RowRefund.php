<?php

namespace App\Admin\Actions\DemoOrder;

use App\Models\Pay\DemoOrder;
use Encore\Admin\Actions\RowAction;
use Illuminate\Http\Request;

class RowRefund extends RowAction
{
    public $name = '退款';

    public function handle(DemoOrder $order, Request $request)
    {
        $refundAmount = $request->input('refund_amount');
        // if ((double)$refundAmount > $order->can_refund_amount) {
        //     return $this->response()->error('最大可退款金额为：' . money_show($order->can_refund_amount));
        // }

        $result = $order->billed->toRefund($refundAmount);
        if ($result['code'] == 0) {
            return $this->response()->success($result['msg'])->refresh();
        }
        return $this->response()->error($result['msg']);
    }

    public function form()
    {
        $this->text('refunded_amount', '已退款金额')->default(money_show($this->row->refunded_amount))->disable();
        $this->text('refunding_amount', '退款中金额')->default(money_show($this->row->refunding_amount))->disable();
        $this->text('can_refund_amount', '可退款金额')->default(money_show($this->row->can_refund_amount))->disable();
        $this->text('refund_amount', '退款金额')->default(money_show($this->row->can_refund_amount))
            ->rules('required|numeric|min:0.01|max:' . $this->row->can_refund_amount)
            ->autofocus();
    }
}