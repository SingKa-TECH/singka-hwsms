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
    protected $config;
    protected $status;

    public function __construct($config = ['url' => 'https://rtcsms.cn-north-1.myhuaweicloud.com:10743/sms/batchSendSms/v1', 'appKey' =>'', 'appSecret' => '', 'sender' => ''])
    {
        if (empty($config['appKey']) || empty($config['appSecret']) || empty($config['sender'])) {
            $this->status = false;
        } else {
            $this->status = true;
            $this->config = $config;
        }
    }

    public function send($templateId, $signature, $mobile, $statusCallback = '', $param)
    {
        if ($this->status) {
            $client = new Client();
            try {
                $response = $client->request('POST', $this->config['url'], [
                    'form_params' => [
                        'from' => $this->config['sender'],
                        'to' => $mobile,
                        'templateId' => $templateId,
                        'templateParas' => $param,
                        'statusCallback' => $statusCallback,
                        'signature' => $signature
                    ],
                    'headers' => [
                        'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
                        'X-WSSE' => $this->buildWsseHeader($this->config['appKey'], $this->config['appSecret'])
                    ],
                    'verify' => false //为防止因HTTPS证书认证失败造成API调用失败，需要先忽略证书信任问题
                ]);
                $result = $response->getBody();
                $result = json_decode($result, true);
                $data['code'] = $result['code'];
                $data['msg'] = $result['description'];
                $data['result'] = $result['result'][0];
            } catch (RequestException $e) {
                $result = $e->getResponse()->getBody();
                $result = json_decode($result, true);
                $data['code'] = $result['code'];
                $data['msg'] = $result['description'];
            }
        } else {
            $data['code'] = 202;
            $data['msg'] = '配置有误，请检查';
        }
        return $data;

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