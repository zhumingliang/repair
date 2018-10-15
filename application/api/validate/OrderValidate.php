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
    ];

    protected $scene = [
        'taking' => ['id'],
    ];

}