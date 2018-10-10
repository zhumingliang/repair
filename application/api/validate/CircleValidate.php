<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:30 PM
 */

namespace app\api\validate;


class CircleValidate extends BaseValidate
{
    protected $rule = [
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'name' => 'require',
        'content' => 'require',
        'title' => 'require',
        'head_img' => 'require',
        'c_id' => 'require|isPositiveInteger',
        'id' => 'require|isPositiveInteger',
        'page' => 'require|isPositiveInteger',
        'size' => 'require|isPositiveInteger',
        'state' => 'require|isPositiveInteger|in:2,3',
        'top' => 'require|isPositiveInteger|in:2,1',
        'type' => 'require|isPositiveInteger|in:1,2',
        'parent_id' => 'require',


    ];

    protected $scene = [
        'category_save' => ['province', 'city', 'area', 'name'],
        'circle_list_mini' => ['province', 'city', 'area', 'page', 'size', 'c_id'],
        'handel' => ['id', 'state'],
        'top_handel' => ['id', 'top'],
        'id' => ['id'],
        'set' => ['id', 'state'],
        'circle_save' => ['content', 'title', 'head_img', 'c_id'],
        'comment_save' => ['content', 'parent_id', 'c_id'],
        'comments' => ['id', 'page', 'size']
    ];


}