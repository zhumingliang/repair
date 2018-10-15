<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 4:01 PM
 */

namespace app\api\validate;


class ExtendValidate extends BaseValidate
{
    protected $rule = [
        'type' => 'require|in:1,2',
        'id' => 'require',
        'area' => 'require',
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger',

    ];
    protected $scene = [
        'list' => ['type'],
        'handel' => ['type', 'id'],
        'read' => ['id'],
        'index_more' => ['area', 'page', 'size'],
        'index' => ['area']
    ];

}