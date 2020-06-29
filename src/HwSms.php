<?php
// +----------------------------------------------------------------------
// | 胜家云 [ SingKa Cloud ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.singka.net All rights reserved.
// +----------------------------------------------------------------------
// | 宁波晟嘉网络科技有限公司
// +----------------------------------------------------------------------
// | Author: ShyComet <shycomet@163.com>
// +----------------------------------------------------------------------

namespace SingKa\HwSms;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HwSms
{
    protected $url;
    protected $appKey;
    protected $appSecret;
    protected $sender;

    public function __construct($url, $appKey, $appSecret, $sender)
    {
        $this->url = $url;
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->sender = $sender;
    }

    public function send($url, $appKey, $appSecret, $sender, $templateId, $signature, $mobile, $statusCallback = '', $param)
    {
        $client = new Client();
        try {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'from' => $sender,
                    'to' => $mobile,
                    'templateId' => $templateId,
                    'templateParas' => $param,
                    'statusCallback' => $statusCallback,
                    'signature' => $signature
                ],
                'headers' => [
                    'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
                    'X-WSSE' => $this->buildWsseHeader($appKey, $appSecret)
                ],
                'verify' => false //为防止因HTTPS证书认证失败造成API调用失败，需要先忽略证书信任问题
            ]);
            echo Psr7\str($response); //打印响应信息
        } catch (RequestException $e) {
            echo $e;
            echo Psr7\str($e->getRequest()), "\n";
            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }
        }
    }


    /**
     * 构造X-WSSE参数值
     * @param string $appKey
     * @param string $appSecret
     * @return string
     */
    public function buildWsseHeader(string $appKey, string $appSecret)
    {
        $now = date('Y-m-d\TH:i:s\Z'); //Created
        $nonce = uniqid(); //Nonce
        $base64 = base64_encode(hash('sha256', ($nonce . $now . $appSecret))); //PasswordDigest
        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", $appKey, $base64, $nonce, $now);
    }
}