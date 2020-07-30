<?php

namespace App\Admin\Actions\DemoOrder;

use App\Models\Pay\DemoOrder;
use Encore\Admin\Actions\RowAction;

class RowRefundResend extends RowAction
{
    public $name = '退款中再次发起';

    public function handle(DemoOrder $order)
    {
        $refundBills = $order->billed->refundBills->where('refund_status', 1);
        foreach ($refundBills as $refundBill) {
            $result = $order->billed->toRefundResend($refundBill);
        }

        return $this->response()->success('已发起再次退款申请')->refresh();
    }
}
