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
        'c_id' => 'require|isPositiveInteger',
        'name' => 'require',
        'arena' => 'require',
        'price' => 'require',
        'unit' => 'require',
        'cover' => 'require',
        'extend' => 'require|in:1,2',
        'imgs' => 'require'
    ];

}