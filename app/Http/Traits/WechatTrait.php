<?php

namespace App\Http\Traits;

use App\Models\Wechat\WechatUser;

trait WechatTrait
{
    /**
     * 获取微信用户信息.
     *
     * @return WechatUser|null
     */
    public function getWechatUser(): ?WechatUser
    {
        return session(WechatUser::SESSION_KEY);
    }
}
