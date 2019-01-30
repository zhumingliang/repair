<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/17
 * Time: 11:44 AM
 */

namespace app\api\validate;


class PayValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'type' => 'require|isPositiveInteger|in:1,2,3,4',
        'r_id' => 'require|isPositiveInteger',
    ];

    protected $scene = [
        'pre' => ['id', 'type'],
        'search' => ['city'],
    ];

}