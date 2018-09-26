<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:26
 */

namespace app\api\validate;


class CollectionValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
        'type' => 'require|isPositiveInteger|in:1,2'

    ];

    protected $scene = [
        'save' => ['id', 'type'],
        'handel' => ['id', 'state']
    ];

}