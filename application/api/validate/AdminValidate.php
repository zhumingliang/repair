<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 1:40 AM
 */

namespace app\api\validate;


class AdminValidate extends BaseValidate
{

    protected $rule = [
        'phone' => 'require',
        'username' => 'require',
        'pwd' => 'require',
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'id' => 'require|isPositiveInteger',
        'state' => 'require|isPositiveInteger|in:1,2,3',
    ];

    protected $scene = [
        'village' => ['phone', 'username', 'pwd'],
        'join' => ['phone', 'username', 'pwd', 'province', 'city', 'area'],
        'handel' => ['id', 'username', 'pwd', 'province', 'city', 'area']
    ];


}