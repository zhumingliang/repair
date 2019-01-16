<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/13
 * Time: 5:27 PM
 */

namespace app\api\validate;


class ScoreValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'money' => 'require',
        'score' => 'require',
        'self' => 'require',
        'parent' => 'require',
        'parent_other' => 'require',
    ];

    protected $scene = [
        'buy' => ['money', 'score'],
        'id' => ['id'],
        'order_rule' => ['self', 'parent', 'parent_other'],
    ];

}