<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/30
 * Time: 上午1:21
 */

namespace app\api\validate;


class BannerValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'des' => 'require',
        'category' => 'require|in:1,2,3,4',
        'type' => 'require|in:1,2',
        'state' => 'require|in:1,2',
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
    ];

    protected $scene = [
        'save' => ['title', 'des', 'category', 'category'],
        'handel' => ['id'],
        'update' => ['id'],
        'list_mini' => ['type'],
        'list_mini_join' => ['province', 'city', 'area'],
    ];


}