<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 11:20 AM
 */

namespace app\api\validate;


class WithdrawValidate extends BaseValidate
{

    protected $rule = [
        'type' => 'require|in:1,2',
        'money' => 'require|isPositiveInteger'
    ];

    protected $scene = [
        'apply' => ['type', 'money'],
        'check' => ['type'],
    ];
}