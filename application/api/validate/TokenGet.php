<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/20
 * Time: 下午2:00
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty',
        'phone' => 'require|isMobile',
        'pwd' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '微信端获取Token，需要code',
        'phone' => '手机端获取Token，需要手机号',
        'pwd' => '手机端获取Token，需要密码'
    ];

    protected $scene = [
        'wx' => ['code'],
        'pc' => ['phone', 'pwd'],
    ];

}