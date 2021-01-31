<?php

namespace App\Listeners;

use App\Models\User\User;
use App\Models\Wechat\WechatUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;

class WeChatUserAuthorizedHandleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(WeChatUserAuthorized $event)
    {
        $wechatOpenid = $event->getUser()->getId();
        $wechatUser = WechatUser::where('wechat_openid', $wechatOpenid)->first();
        if (!$wechatUser) {
            $eventUser = $event->getUser();
            $user = User::create([
                'name' => $eventUser->getName() ?? $eventUser->getId(),
                'email' => $eventUser->getId() . '@qq.com',
                'password' => Hash::make($wechatOpenid),
            ]);
            $wechatUser = WechatUser::create([
                'user_id' => $user->id,
                'wechat_openid' => $eventUser->getId(),
                'name' => $eventUser->getName(),
                'nickname' => $eventUser->getNickname(),
                'avatar' => $eventUser->getAvatar(),
            ]);
        }
        Session::put(WechatUser::SESSION_KEY, $wechatUser);
    }
}
