<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/29
 * Time: 12:00 AM
 */


return [
    "default_options_name" => "repair",
    "mengant" => [
        'AccessKeyId' => '********', // 访问密钥，在阿里云的密钥管理页面创建
        'AccessSecret' => '************', // 访问密钥，在阿里云的密钥管理页面创建
        'TemplateCode' => 'SMS_******', // 短信模板ID
        'SignName' => '管理平台',
    ],
    "repair" => [
        'AccessKeyId' => 'LTAIGjx5Z4Qj62ng', // 访问密钥，在阿里云的密钥管理页面创建
        'AccessSecret' => '2Vww2c0X4yzEMnbnaT9Ll2YmmX1v3J', // 访问密钥，在阿里云的密钥管理页面创建
        'TemplateDemandCode' => 'SMS_151991521', // 商家接单发送通知给用户
        'TemplateServiceCode' => 'SMS_152195120', // 用户预约商家服务发送通知给服务
        'SignName' => '智慧城市',
    ],
];