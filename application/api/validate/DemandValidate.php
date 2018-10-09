<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午8:43
 */

namespace app\api\validate;


class DemandValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'phone' => 'require',
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'address' => 'require',
        'time_begin' => 'require',
        'time_end' => 'require',
        'money' => 'require|isPositiveInteger',
        'type' => 'require|in:1,2',
        'imgs' => 'require'
    ];

}