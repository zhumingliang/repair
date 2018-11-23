<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/27
 * Time: 10:57 PM
 */

namespace app\api\validate;


class JoinCommissionValidate extends BaseValidate
{
    protected $rule = [
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'discount' => 'require',
        'id' => 'require|isPositiveInteger'
    ];

    protected $scene = [
        'save' => ['province', 'city', 'area', 'discount'],
        'handel' => ['id']
    ];


}