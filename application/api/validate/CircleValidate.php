<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:30 PM
 */

namespace app\api\validate;


class CircleValidate extends BaseValidate
{
    protected $rule = [
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'name' => 'require',
        'id' => 'require|isPositiveInteger',
        'type' => 'require|isPositiveInteger|in:1,2'

    ];

    protected $scene = [
        'category_save' => ['province', 'city', 'area', 'name'],
        'handel' => ['id'],
        'set' => ['id', 'type']
    ];

}