@extends('layouts.app')
@section('title', '临时付款结果')
@section('css')
    @include('layouts.alert_css')
@endsection
@section('content')
    <div class="container">
        <div class="row clearfix">
            <div class="col-md-12 column">
                <h2>
                    付款失败
                </h2>
                <blockquote>
                    <p>
                        付款金额：￥{{ money_show($order->price) }}
                    </p>
                    <p>
                        商家订单号：{{ $order->number }}
                    </p>
                    <p>
                        支付方式：{{ $order->bill->pay_way_name }}
                    </p>
                    <p>
                        当前状态：{{ $order->status_name }}
                    </p>
                    <small>请截图该页面，咨询管理员以得到最终支付结果</small>
                    <br>
                    <p>
                        <a href="{{ route('web.pay') }}" target="_blank" style="color: #0d6aad;text-decoration: none">再付一笔</a>
                    </p>
                </blockquote>
            </div>
        </div>
@endsection
