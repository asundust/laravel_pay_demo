<?php

if (!function_exists('curl_get_url')) {
    /**
     * CURL_GET
     *
     * @param $url
     * @return bool|mixed|string
     */
    function curl_get_url($url)
    {
        $curl = curl_init();
        $timeout = 5;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($curl);
        curl_close($curl);
        return $file_contents;
    }
}

if (!function_exists('curl_post_url')) {
    /**
     * CURL_POST
     *
     * @param $url
     * @param array $data
     * @param array $httpHeader
     * @return mixed
     */
    function curl_post_url($url, $data = [], $httpHeader = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }
}

if (!function_exists('log_channel')) {
    /**
     * 返回指定通道的日志实例
     *
     * @param $channel
     * @return mixed
     */
    function log_channel($channel)
    {
        return logger()->channel($channel);
    }
}

if (!function_exists('pl')) {
    /**
     * 快速日志打印
     *
     * @param string $message 日志信息
     * @param string $name 日志文件名
     * @param string $path 日志写入路径
     * @param int $max 该目录下最大日志文件数
     */
    function pl($message = '', $name = 'test', $path = '', $max = 30)
    {
        if (strlen($path) == 0) {
            $path = $name;
        }
        config([
            'logging.channels.' . $path . '_' . $name => [
                'driver' => 'daily',
                'path' => storage_path('logs/' . $path . '/' . $name . '.log'),
                'level' => 'debug',
            ],
        ]);
        $type = '';
        if (function_exists('debug_backtrace') && debug_backtrace()) {
            $first = Illuminate\Support\Arr::first(debug_backtrace());
            if (is_array($first) && isset($first['file']) && isset($first['line'])) {
                $str = substr(str_replace(base_path(), '', $first['file']), 1);
                $type = "On {$first['line']} Line At [{$str}] " . PHP_EOL;
            }
        }
        if (!is_array($message)) {
            $type = $type . $message;
            $message = [];
        }
        log_channel($path . '_' . $name)->info($type, $message);
    }
}

if (!function_exists('api_res')) {
    /**
     * 封装返回数据
     *
     * @param string $msg
     * @param array $data
     * @param int $code
     *
     * @return array
     */
    function api_res($msg, $data = [], $code = 0)
    {
        return compact('msg', 'data', 'code');
    }
}

if (!function_exists('api_ok')) {
    /**
     * 封装返回数据-成功
     * @param string|array $msg
     * @param array|int $data
     * @param int $code
     *
     * @return array
     */
    function api_ok($msg, $data = [], $code = 0)
    {
        if (is_string($msg)) {
            return api_res($msg, $data, $code);
        }
        return api_res('', $msg, is_int($data) ? $data : $code);
    }
}

if (!function_exists('da')) {
    /**
     * dd打印封装 不断点
     * 如果能转成toArray()则转成数组
     *
     * @param mixed $args
     */
    function da(...$args)
    {
        $varDumper = new Symfony\Component\VarDumper\VarDumper;
        foreach ($args as $x) {
            if (method_exists($x, 'toArray')) {
                $x = $x->toArray();
            }
            $varDumper->dump($x);
        }

    }
}

if (!function_exists('dad')) {
    /**
     * dd打印封装 并断点
     * 如果能转成toArray()则转成数组
     *
     * @param mixed $args
     */
    function dad(...$args)
    {
        da(...$args);
        die(1);
    }
}

if (!function_exists('ma')) {
    /**
     * 移动版dd打印封装 不断点
     * 如果能转成toArray()则转成数组
     *
     * @param mixed $args
     */
    function ma(...$args)
    {
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">';
        da(...$args);
    }
}

if (!function_exists('mad')) {
    /**
     * 移动版dd打印封装 并断点
     * 如果能转成toArray()则转成数组
     *
     * @param mixed $args
     */
    function mad(...$args)
    {
        ma(...$args);
        die(1);
    }
}

if (!function_exists('is_time_string')) {
    /**
     * 判断字符串是否为时间格式
     *
     * @param string $var
     * @return bool
     */
    function is_time_string($var)
    {
        if (!is_string($var)) {
            return false;
        }
        $time = strtotime($var);
        return date('Y-m-d', $time) == $var || date('Y-m-d H:i:s', $time) == $var;
    }
}

if (!function_exists('admin_switch_arr')) {
    /**
     * admin系统的switch选项
     *
     * @param $arr
     * @param bool $isOpposite
     * @return array
     */
    function admin_switch_arr($arr, $isOpposite = true)
    {
        $keys = array_keys($arr);
        $key1 = $isOpposite ? 1 : 0;
        $key2 = $isOpposite ? 0 : 1;
        return [
            'on' => ['value' => $keys[$key1], 'text' => $arr[$keys[$key1]], 'color' => 'success'],
            'off' => ['value' => $keys[$key2], 'text' => $arr[$keys[$key2]], 'color' => 'danger'],
        ];
    }
}

if (!function_exists('console_line')) {
    /**
     * 命令行模式中, 打印需要的数据
     *
     * @param $text
     * @param string $type
     */
    function console_line($text, $type = 'line')
    {
        if (app()->runningInConsole()) {
            $types = [
                'info' => 32, 'comment' => 33, 'warn' => 33, 'line' => 37, 'error' => '41;37', 'question' => '46;30',
            ];
            $code = $types[$type] ?? '37';
            // 30黑色，31红色，32绿色，33黄色，34蓝色，35洋红，36青色，37白色，
            echo chr(27) . "[" . $code . "m" . "$text" . chr(27) . "[0m" . PHP_EOL;
        }
    }
}

if (!function_exists('console_info')) {
    function console_info($text)
    {
        console_line($text, 'info');
    }
}

if (!function_exists('console_comment')) {
    function console_comment($text)
    {
        console_line($text, 'comment');
    }
}

if (!function_exists('console_warn')) {
    function console_warn($text)
    {
        console_line($text, 'warn');
    }
}

if (!function_exists('console_error')) {
    function console_error($text)
    {
        console_line($text, 'error');
    }
}

if (!function_exists('console_question')) {
    function console_question($text)
    {
        console_line($text, 'question');
    }
}

if (!function_exists('is_wechat')) {
    /**
     * 判断是否是微信访问
     *
     * @return bool
     */
    function is_wechat()
    {
        return strpos(\Jenssegers\Agent\Facades\Agent::getUserAgent(), 'MicroMessenger') !== false;
    }
}

if (!function_exists('money_show')) {
    /**
     * 金额显示处理
     *
     * @param object|string|array $money
     * @return string|array
     */
    function money_show($money)
    {
        if (is_array($money)) {
            foreach ($money as $k => $v) {
                $money[$k] = money_show($v);
            }
            return $money;
        }
        if ($money == null || $money == '') {
            return '0.00';
        }

        if (is_object($money)) {
            $result = $money;
        } else {
            $result = \Brick\Math\BigDecimal::of($money);
        }
        return sprintf('%01.2f', $result->getIntegralPart() . '.' . $result->getFractionalPart());
    }
}

if (!function_exists('admin_to_label')) {
    /**
     * Admin 组Label数组
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    function admin_to_label($array1, $array2)
    {
        $data = [];
        foreach ($array1 as $k => $v) {
            $data[$v] = $array2[$k] ?? '';
        }
        return $data;
    }
}