<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * TestController constructor.
     */
    public function __construct()
    {
        if (app()->isProduction()) {
            abort(404);
        }
    }

    public function t($action = '')
    {
        if (0 == strlen($action)) {
            $action = 'a';
        }
        $result = $this->$action();
        if (!empty($result)) {
            return $result;
        }
        dd('结束运行方法：'.$action);
    }

    // public function a()
    // {
    //     //
    // }

    public function a()
    {
    }
}
