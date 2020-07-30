@extends('layouts.app_base')
@section('title', '临时付款结果')
@section('body')
  <div class="container">
    <div class="row clearfix">
      <div class="col-md-12 column">
        <h2>
          付款成功
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
            支付订单号：{{ $order->bill->pay_service_no }}
          </p>
          <small>请截图该页面，页面将于10分钟后无法访问</small>
          <br>
          <p>
            <a href="{{ route('web.pay') }}" target="_blank" style="color: #0d6aad;text-decoration: none">再付一笔</a>
          </p>
        </blockquote>
      </div>
    </div>
  </div>
@endsection
