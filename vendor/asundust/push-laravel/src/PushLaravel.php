<?php

namespace Asundust\PushLaravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PushLaravel
{
    private $pushUrl;
    private $pushSecret;

    /**
     * PushLaravel constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->pushUrl = $config['push_url'] ?? '';
        $this->pushSecret = $config['push_secret'] ?? '';
    }

    /**
     * send.
     *
     * @param        $title
     * @param string $content
     *
     * @return string
     *
     * @throws PushLaravelException
     * @throws GuzzleException
     */
    public function send($title, $content = '')
    {
        if (!$this->pushUrl || !$this->pushSecret) {
            throw new PushLaravelException('PushLaravel Config Error');
        }

        $formParams = [
            'title' => $title,
        ];
        if ($content) {
            $formParams['content'] = $content;
        }

        return json_decode(
            (new Client([
                'timeout' => 10,
                'verify' => false,
                'http_errors' => false,
            ]))
                ->post(rtrim($this->pushUrl, '/').'/push/'.$this->pushSecret, [
                    'form_params' => $formParams,
                ])
                ->getBody()
                ->getContents(),
            true);
    }
}
