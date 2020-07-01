# 华为云短信

#### 介绍
本项目集成了华为云短信发送业务，支持ThinkPHP5.0、ThinkPHP5.1和ThinkPHP6.0，由宁波晟嘉网络科技有限公司维护。

#### 安装教程

使用 `composer require singka/singka-hwsms` 命令行安装即可。

#### 使用示例（基于ThinkPHP6.0）


```php
<?php
// +----------------------------------------------------------------------
// | 胜家云 [ SingKa Cloud ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.singka.net All rights reserved.
// +----------------------------------------------------------------------
// | 宁波晟嘉网络科技有限公司
// +----------------------------------------------------------------------
// | Author: ShyComet <shycomet@qq.com>
// +----------------------------------------------------------------------
namespace app\home\controller;

use SingKa\HwSms\HwSms;

class Index
{
    /**
    * 短信发送示例
    *
    * @url          华为云短信APP接入地址+接口访问URI
    * @appKey       华为云短信appKey
    * @appSecret    华为云短信appSecret
    * @sender       国内短信签名通道号或国际/港澳台短信通道号
    */
    public function smsDemo()
    {
        $config['url'] = 'https://rtcsms.cn-north-1.myhuaweicloud.com:10743/sms/batchSendSms/v1';
        $config['appKey'] = 'PkT889B*************wM0GAi';
        $config['appSecret'] = 'U58fd****************0o4N';
        $config['sender'] = 'csms12345678';
        $sms = new HwSms($config);
        $result = $sms->singleSend('模板ID', '短信签名', '手机号码（多个号码可以用英文逗号隔开）', '短信发送状态返回接收地址，可以为空', '短信变量数组');
        if ($result['code'] == '000000') {
            echo '发送成功';
        } else {
            echo $result['msg'];
        }
    }
  
}
```

#### 其他说明

相关材料请查阅：
[华为云短信介绍](https://support.huaweicloud.com/productdesc-msgsms/sms_desc.html)
[SDK简介](https://support.huaweicloud.com/devg-msgsms/sms_04_0003.html)
[API概览](https://support.huaweicloud.com/api-msgsms/sms_05_0000.html)

作者联系方式：shycomet@163.com