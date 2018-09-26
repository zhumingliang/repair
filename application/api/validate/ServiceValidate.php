<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 下午2:28
 */

namespace app\api\validate;


class ServiceValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'phone' => 'require',
        'province' => 'require',
        'city' => 'require',
        'arena' => 'require',
        'address' => 'require',
        'time_begin' => 'require',
        'time_end' => 'require',
        'money' => 'require|isPositiveInteger',
        'type' => 'require|in:1,2',
        'imgs' => 'require'
    ];

}