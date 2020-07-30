<?php

namespace App\Admin\Actions\DemoOrder;

use App\Models\Pay\DemoOrder;
use Encore\Admin\Actions\RowAction;

class RowRefundClose extends RowAction
{
    public $name = '关闭退款中';

    public function handle(DemoOrder $order)
    {
        $order->billed->refundBills()->where('refund_status', 1)->update(['refund_status' => 4]);

        return $this->response()->success('已关闭退款中的退款订单')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定关闭正在退款中的订单？', '请确认当前退款中的订单是无效订单后再操作，以免资金损失！');
    }
}
