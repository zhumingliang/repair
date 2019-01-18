<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/18
 * Time: 11:47 PM
 */

namespace app\api\validate;


class SignInValidate extends BaseValidate
{
    protected $rule = [
        'cycle' => 'require',
        'begin' => 'require',
        'begin_score' => 'require',
        'add' => 'require',
        'id' => 'require',

    ];

    protected $scene = [
        'system_save' => ['cycle', 'begin', 'begin_score','begin_score'],
        'id'=>['id']
    ];

}