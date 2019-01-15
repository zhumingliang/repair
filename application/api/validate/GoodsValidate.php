<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:17 AM
 */

namespace app\api\validate;

class GoodsValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'name' => 'require',
        'c_id' => 'require|isPositiveInteger',
        'score' => 'require|isPositiveInteger',
        'address' => 'require',
        'money' => 'require',
    ];

    protected $scene = [
        'save' => ['name', 'c_id', 'score', 'address', 'money']
    ];

}