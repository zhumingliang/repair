<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/7
 * Time: 9:55 PM
 */

namespace app\api\validate;


class CommentValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'page' => 'require|isPositiveInteger',
        'size' => 'require|isPositiveInteger'
    ];

    protected $scene = [
        'service' => ['id','page','size']
    ];

}