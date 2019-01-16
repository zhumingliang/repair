<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 11:25 PM
 */

namespace app\api\validate;


class AddressValidate extends BaseValidate
{
    protected $rule = [
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'detail' => 'require',
        'phone' => 'require',
        'name' => 'require',
        'id' => 'require',

    ];

    protected $scene = [
        'save' => ['province', 'city', 'area','detail','phone','name'],
        'id'=>['id']
    ];

}