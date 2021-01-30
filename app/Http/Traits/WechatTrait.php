<?php

namespace App\Http\Traits;

use App\Models\Wechat\WechatUser;

trait WechatTrait
{
    /**
     * 获取微信用户信息
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getWechatUser()
    {
        $wechatOpenid = session('wechat.oauth_user.default', [])['id'];
        return WechatUser::where('wechat_openid', $wechatOpenid)->first();
    }
}