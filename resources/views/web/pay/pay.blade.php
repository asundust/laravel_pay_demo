@extends('layouts.app_base')
@section('title', '临时付款')
@section('head')
  @parent
  @include('layouts.bootstrap_css')
  @include('layouts.toastr_css')
@endsection
@section('body')
  <div class="container">
    <!-- 模态框 -->
    <div class="modal fade" id="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- 模态框头部 -->
          <div class="modal-header">
            <h4 class="modal-title">请使用微信扫码进行支付</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-header">
            <h6 class="modal-title">(截图二维码识别亦可)</h6>
          </div>
          <!-- 模态框主体 -->
          <div class="modal-body">
            <div id="qr-img-div" class="text-center d-none">
              <img id="qr-img" src="" alt="二维码" width="200"
                   height="200">
            </div>
          </div>
          <!-- 模态框底部 -->
          <div class="modal-footer">
            <a href="" id="pay_result" class="btn btn-success">支付完成</a>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
          </div>
        </div>
      </div>
    </div>
    <br/>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form action="{{ route('web.to_pay') }}" method="post"
              class="needs-validation" novalidate="">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="money">请输入金额</label>
              <input type="text" class="form-control" name="money" id="money"/>
            </div>
          </div>
          <input type="hidden" id="pay_way" name="pay_way" value="2">
          <hr class="mb-4">
          @if (is_wechat())
            <button class="btn btn-primary btn-block" type="submit">
              支付宝付款(请在第三方浏览器内打开并支付)
            </button>
            <button onclick="toPay()" type="button" class="btn btn-primary btn-block">
              微信支付
            </button>
          @else
            <button class="btn btn-primary btn-block" type="submit">
              支付宝付款(支持手机和电脑)
            </button>
            <button onclick="toPay()" type="button" class="btn btn-primary btn-block">
              微信支付(支持扫码,微信里打开可直接支付)
            </button>
            <a role="button" class="btn btn-primary btn-block copy"
               data-clipboard-text="{{ request()->fullUrl() }}"
               href="{{ !is_wechat() ? 'weixin://' : 'javascript:void(0);' }}">复制地址{{ is_wechat() ? '' : '并打开微信' }}</a>
          @endif
          <input id="qr-alert" type="hidden" data-toggle="modal" data-target="#myModal">
        </form>
      </div>
    </div>
  </div>
@endsection
@section('js')
  @include('layouts.jquery')
  @include('layouts.bootstrap_js')
  @include('layouts.toastr_js')
  @include('layouts.clipboard')
  <script type="text/javascript">
    let wechatPayConfig;
    let id;

    let clipboard = new ClipboardJS('.copy');
    clipboard.on('success', function (e) {
      toastr.info('复制地址成功');
    });

    function toPay() {
      let money = $('#money').val();
      if (money.length === 0) {
        toastr.error('请输入金额');
        return false;
      }
      $.ajax({
        'url': '{{ route('web.to_pay') }}',
        'type': 'post',
        'data': {
          'money': money,
          'pay_way': 1
        },
        success(res) {
          if (res.code === 0) {
            if (res.data.type === 'mp') {
              wechatPayConfig = res.data.config;
              id = res.data.id;
              checkPay(id);
              callPay();
            } else {
              $('#qr-alert').click();
              $('#qr-img-div').removeClass('d-none');
              $('#qr-img').attr('src', res.data.img);
              $('#pay_result').attr('href', '{{ route('web.pay_result') }}' + '?id=' + res.data.id);
              id = res.data.id;
              checkPay(id);
            }
          } else {
            toastr.error(res.msg);
          }
        },
        error(res) {
          if (res.responseJSON.message) {
            toastr.error(res.responseJSON.message);
            return false;
          }
          toastr.error('系统错误');
        }
      });
    }

    function checkPay(id) {
      let count = 0;
      let check = setInterval(function () {
        $.ajax({
          'url': '{{ route('web.check_pay') }}',
          'type': 'post',
          'async': false,
          'data': {
            'id': id
          },
          success(res) {
            if (res.msg) {
              if (res.code === 0) {
                toastr.success(res.msg);
              } else {
                toastr.error(res.msg);
              }
            }
            if (res.data.wait === 0) {
              clearInterval(check);
            }
            if (res.data.url) {
              window.location.href = res.data.url;
            }
            count++;
          },
          error(res) {
            if (res.responseJSON.message) {
              toastr.error(res.responseJSON.message);
              return false;
            }
            toastr.error('系统错误');
            count++;
          }
        });
        if (count >= 10) {
          clearInterval(check);
          return false;
        }
      }, 5000)
    }

    // 调用微信 JS api 支付
    function jsApiCall() {
      WeixinJSBridge.invoke(
        'getBrandWCPayRequest', wechatPayConfig,
        function (res) {
          WeixinJSBridge.log(res.err_msg);
          if (res.err_msg === 'get_brand_wcpay_request:ok') {
            checkPay(id);
          } else {
            toastr.error('您已取消支付');
          }
        }
      );
    }

    function callPay() {
      if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
          document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
          document.attachEvent('WeixinJSBridgeReady', jsApiCall);
          document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
      } else {
        jsApiCall();
      }
    }
  </script>
@endsection
