<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 6:46 PM
 */

namespace app\api\validate;


class GoodsOrderValidate extends BaseValidate
{
    protected $rule = [
        'g_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
        'score' => 'require|isPositiveInteger',
        'express' => 'require',
        'express_code' => 'require',
        'id' => 'require|isPositiveInteger',
        'type' => 'require|in:1,2,3,4',

    ];
    protected $scene = [
        'save' => ['g_id', 'count', 'score'],
        'express_update' => ['express', 'express_code', 'id'],
        'list' => ['type'],
    ];

}