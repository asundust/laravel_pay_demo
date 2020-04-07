<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\DemoOrder\RowPayStatusCheck;
use App\Admin\Actions\DemoOrder\RowRefund;
use App\Admin\Actions\DemoOrder\RowRefundClose;
use App\Admin\Actions\DemoOrder\RowRefundResend;
use App\Models\Pay\DemoOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;

class DemoOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DemoOrder());

        $grid->column('id', __('Id'));
        // $grid->column('user_id', __('User id'));
        $grid->column('number', '订单号');
        $grid->column('billed.pay_service_no', '商户流水号');
        $grid->column('title', '订单标题');
        $grid->column('price', '订单金额')->display(function () {
            return '￥' . money_show($this->price);
        });
        $grid->column('pay_way_name', '支付方式')->display(function () {
            return $this->bill->pay_way_name ?? '';
        });
        $grid->column('pay_at', '支付时间');
        $grid->column('status_name', '订单状态')->display(function () {
            return $this->status_name;
        })->label(admin_to_label(DemoOrder::STATUS, DemoOrder::STATUS_LABEL));
        $grid->column('pay_info', '支付信息')->display(function () {
            $str = '';
            if ($this->billed) {
                $str .= '已付￥' . money_show($this->payed_amount);
            }
            if ($this->refunded_amount > 0 || $this->refunding_amount > 0) {
                $str .= '/已退￥' . money_show($this->refunded_amount) . '/在退￥' . money_show($this->refunding_amount);
            }
            return $str;
        });
        $grid->column('created_at', '创建时间');
        // $grid->column('updated_at', __('Updated at'));

        $grid->model()->with(['bill', 'billed.refundBills'])->latest();

        $grid->filter(function (Filter $filter) {
            $filter->expand();
            $filter->like('number', '订单号');
            $filter->where(function (Builder $builder) {
                $builder->whereHas('billed', function (Builder $builder) {
                    $builder->where('pay_service_no', 'like', "%{$this->input}%");
                });
            }, '商户流水号');
            $filter->equal('price', '订单金额')->currency();
            $filter->equal('status', '状态')->select(DemoOrder::STATUS);
            $filter->between('pay_at', '支付时间')->datetime();
            $filter->between('created_at', '创建时间')->datetime();
        });

        $grid->actions(function (Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            if ($this->row->billed) {
                $actions->add(new RowRefund());
                if ($this->row->refunding_amount > 0) {
                    $actions->add(new RowRefundResend());
                    $actions->add(new RowRefundClose());
                }
            }
            if ($this->row->bill) {
                if ($this->row->bill->bill_status == 0 || $this->row->bill->pay_status == 1) {
                    $actions->add(new RowPayStatusCheck());
                }
            }
        });

        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->disableExport();
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(DemoOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('number', __('Number'));
        $show->field('title', __('Title'));
        $show->field('price', __('Price'));
        $show->field('pay_at', __('Pay at'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DemoOrder());

        $form->number('user_id', __('User id'));
        $form->text('number', __('Number'));
        $form->text('title', __('Title'));
        $form->decimal('price', __('Price'));
        $form->datetime('pay_at', __('Pay at'))->default(date('Y-m-d H:i:s'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
