<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 1:23 AM
 */

namespace app\api\validate;


class BondValidate extends BaseValidate
{
    protected $rule = [
        'type' => 'require',
        'money' => 'require',
    ];

}