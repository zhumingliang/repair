<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/14
 * Time: 7:43 PM
 */

namespace app\api\validate;


class ImageValidate extends BaseValidate
{
    protected $rule = [
        'shop_id' => 'require|isPositiveInteger',
        'type' => 'require|isPositiveInteger|in:1,2,3',
        'city' => 'require',
    ];

    protected $scene = [
        'upload' => ['shop_id', 'type'],
        'search' => ['city'],
    ];

}