<?php

namespace Asundust\WechatWorkPush\Http\Traits;

use Asundust\WechatWorkPush\Models\WechatWorkPushConfig;
use EasyWeChat\Factory;

trait SendMessageTrait
{
    /**
     * 使用自定配置发送消息
     *
     * @param array       $config  配置 ['corp_id' => 'xxx', 'agent_id' => 'xxx', 'secret' => 'xxx'];
     * @param string      $name    用户
     * @param string      $title   标题
     * @param string|null $content 内容
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function send(array $config, string $name, string $title, ?string $content = null): array
    {
        $message = $title;
        if ($content) {
            $message .= "\n\n" . $content;
        }
        $messenger = Factory::work($config)->messenger;
        $result = $messenger->ofAgent($config['agent_id'])->message($message)->toUser($name ?? '@all')->send();
        if ($result['errcode'] == 0 && $result['errmsg'] == 'ok') {
            return ['code' => 0, 'message' => 'success', 'original' => app()->isLocal() ? $result : []];
        }
        return ['code' => 1, 'message' => $result['errmsg'], 'original' => app()->isLocal() ? $result : []];
    }

    /**
     * 使用默认配置发送消息
     *
     * @param string      $name    用户
     * @param string      $title   标题
     * @param string|null $content 内容
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function defaultSend(string $name, string $title, ?string $content = null): array
    {
        $config = WechatWorkPushConfig::firstOrNew([]);
        if (!$config->is_complete) {
            return ['code' => 1, 'message' => '系统配置错误'];
        }
        $config = [
            'corp_id' => $config->corp_id,
            'agent_id' => $config->agent_id,
            'secret' => $config->secret,
        ];
        return $this->send($config, $name, $title, $content);
    }
}