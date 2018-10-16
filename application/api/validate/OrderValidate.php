<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 10:49 PM
 */

namespace app\api\validate;


class OrderValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'type' => 'require|isPositiveInteger|in:1,2',
        'money' => 'require|isPositiveInteger',
        'order_type'=>'require|isPositiveInteger',
        'page'=>'require|isPositiveInteger',
        'size'=>'require|isPositiveInteger',
    ];

    protected $scene = [
        'id' => ['id'],
        'phone' => ['id', 'type'],
        'price' => ['id', 'type', 'money'],
        'list'=>['order_type', 'page', 'size']
    ];

}