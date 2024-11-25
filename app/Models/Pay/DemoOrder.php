<?php

namespace App\Models\Pay;

use App\Http\Traits\SendMessageTrait;
use App\Models\BaseModel;
use App\Models\BaseModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Pay\DemoOrder.
 *
 * @property int                    $id
 * @property int                    $user_id           用户id
 * @property string                 $number            订单号
 * @property string                 $title             订单名称
 * @property float                  $price             价格
 * @property string|null            $pay_at            支付时间
 * @property int                    $status            状态(0未付款，1已付款)
 * @property Carbon|null            $created_at
 * @property Carbon|null            $updated_at
 * @property MultiBill              $bill
 * @property MultiBill              $billed
 * @property Collection|MultiBill[] $bills
 * @property int|null               $bills_count
 * @property mixed                  $can_refund_amount
 * @property mixed                  $payed_amount
 * @property mixed                  $refunded_amount
 * @property mixed                  $refunding_amount
 * @property mixed                  $status_string
 * @mixin Eloquent
 */
class DemoOrder extends BaseModel
{
    use BaseModelTrait;
    use SendMessageTrait;

    public const STATUS = [
        0 => '未支付',
        1 => '已支付',
        2 => '部分退款',
        3 => '全额退款',
    ];

    public const STATUS_LABEL = [
        0 => 'default',
        1 => 'success',
        2 => 'warning',
        3 => 'danger',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $order) {
            if (!$order->number) {
                $order->number = self::getNewNumber(self::class);
            }
            if (!$order->status) {
                $order->status = 0;
            }
        });
    }

    public function bills(): MorphMany
    {
        return $this->morphMany(MultiBill::class, 'billable');
    }

    public function bill(): MorphOne
    {
        return $this->morphOne(MultiBill::class, 'billable')->latest();
    }

    public function billed(): MorphOne
    {
        return $this->morphOne(MultiBill::class, 'billable')->where('bill_status', 1)->latest();
    }

    // 已支付的金额 payed_amount
    public function getPayedAmountAttribute(): float
    {
        return $this->billed ? $this->billed->pay_amount : 0.00;
    }

    // 已退款的金额 refunded_amount
    public function getRefundedAmountAttribute()
    {
        return $this->billed ? $this->billed->refunded_amount : 0.00;
    }

    // 退款中的金额 refunding_amount
    public function getRefundingAmountAttribute()
    {
        return $this->billed ? $this->billed->refunding_amount : 0.00;
    }

    // 可退款的金额 can_refund_amount
    public function getCanRefundAmountAttribute()
    {
        return $this->billed ? $this->billed->can_refund_amount : 0.00;
    }

    /**
     * 获取支付结果页面链接.
     */
    public function payResultUrl(): string
    {
        return route('web.pay_result', ['id' => $this->id]);
    }

    /**
     * 支付成功处理.
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function handlePied(MultiBill $bill)
    {
        Cache::put('DemoOrder' . $this->id, 1, 600);
        $this->update([
            'pay_at' => $bill->pay_at,
            'status' => 1,
        ]);
        // 发送消息
        $this->sendMessage(config('app.name') . $bill->pay_way_string . '有一笔新的收款' . money_show($this->payed_amount) . '元', $bill->pay_way_string . '于 ' . $bill->pay_at . ' 收款：￥' . money_show($this->payed_amount));
    }

    /**
     * 支付宝支付成功处理同步页.
     */
    public function payResult()
    {
        return redirect($this->payResultUrl());
    }

    /**
     * 退款处理.
     */
    public function handleRefunded(MultiBill $bill)
    {
        if (5 == $bill->pay_status) {
            $this->status = 2;
            $this->save();
        } elseif (6 == $bill->pay_status) {
            $this->status = 3;
            $this->save();
        }
    }
}
