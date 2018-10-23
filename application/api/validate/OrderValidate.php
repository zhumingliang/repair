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
        'confirm' => 'require|isPositiveInteger|in:1,2',
        'money' => 'require|isPositiveInteger',
        'order_type' => 'require|isPositiveInteger',
        'page' => 'require|isPositiveInteger',
        'size' => 'require|isPositiveInteger',
        'o_id' => 'require|isPositiveInteger',
        's_id' => 'require|isPositiveInteger',
        'score_type' => 'require|isPositiveInteger|in:1,2,3',
        'score' => 'require|isPositiveInteger',
        'list_type' => 'require|isPositiveInteger|in:1,2',


    ];

    protected $scene = [
        'id' => ['id'],
        'phone' => ['id', 'type'],
        'price' => ['id', 'money'],
        'confirm' => ['id', 'type', 'confirm'],
        'comment' => ['o_id', 's_id', 'score_type', 'score'],
        'list' => ['order_type', 'page', 'size','list_type']
    ];

}