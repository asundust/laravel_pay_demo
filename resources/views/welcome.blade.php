<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ cache_config('index_title') ?: 'Laravel' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 90vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .ad-title {
            font-size: 30px;
            margin-top: 50px;
            margin-bottom: 10px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .footer {
            text-align: center;
            height: 10vh;
        }

        .footer > a {
            font-family: 'Nunito', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: #636b6f;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">
            {{ cache_config('index_title') ?: 'Laravel' }}
        </div>

        <div class="links">
            <a href="https://github.com/asundust/laravel_new">基于项目</a>
            <a href="https://github.com/asundust/laravel_pay_demo">开源地址</a>
            <a href="https://github.com/asundust">作者GITHUB</a>
        </div>
        <div class="ad-title">
            以下为推广
        </div>
        <div class="links">
            <a href="http://youqian.360.cn/register.html?id=87098">加入360推广</a>
            <a href="https://hao.360.cn/?src=lm&ls=n286efe0291">360导航</a>
        </div>
    </div>
</div>
@if (config('app.beian'))
    <footer class="footer">
        <a href="http://beian.miit.gov.cn" target="_blank">{{ config('app.beian') }}</a>
    </footer>
@endif
</body>
</html>
