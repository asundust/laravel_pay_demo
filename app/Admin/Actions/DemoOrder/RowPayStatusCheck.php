<?php

namespace App\Admin\Actions\DemoOrder;

use App\Models\Pay\DemoOrder;
use Encore\Admin\Actions\RowAction;

class RowPayStatusCheck extends RowAction
{
    public $name = '支付检查';

    public function handle(DemoOrder $order)
    {
        $result = $order->bill->toPayFind();
        if ($result['code'] == 0) {
            return $this->response()->success($result['msg'])->refresh();
        }
        return $this->response()->error($result['msg']);
    }
}