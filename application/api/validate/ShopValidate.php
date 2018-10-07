<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:18
 */

namespace app\api\validate;


class ShopValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'phone' => 'require',
        'id_number' => 'require',
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'address' => 'require',
        'time_begin' => 'require',
        'time_end' => 'require',
        'type' => 'require|in:1,2',
        'imgs' => 'require',
        'head_url' => 'require',
        'id' => 'require|isPositiveInteger',
        's_id' => 'require|isPositiveInteger',
        'c_id' => 'require|isPositiveInteger',
        'page' => 'require|isPositiveInteger',
        'size' => 'require|isPositiveInteger',
        'state' => 'require|isPositiveInteger|in:2,3',

    ];

    protected $scene = [
        'save' => ['name', 'phone', 'province', 'city', 'area', 'address', 'head_url', 'imgs',
            'type', 'id_number'],
        'handel' => ['id', 'state'],
        'booking' => ['s_id', 'area', 'address', 'phone', 'time_begin',
            'time_end'],
        'service' => ['type', 'page', 'size','c_id','area']
    ];

}