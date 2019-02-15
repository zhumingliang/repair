<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-31
 * Time: 15:55
 */

namespace app\api\validate;


class ForumValidate extends BaseValidate
{

    protected $rule = [
        'content' => 'require',
        'title' => 'require',
        'imgs' => 'require',
        'f_id' => 'require|isPositiveInteger',
        'id' => 'require|isPositiveInteger',
        'page' => 'require|isPositiveInteger',
        'size' => 'require|isPositiveInteger',
        'state' => 'require|isPositiveInteger|in:2,3,4',
        'top' => 'require|isPositiveInteger|in:2,1',
        'type' => 'require|isPositiveInteger|in:1,2',
        'parent_id' => 'require',


    ];

    protected $scene = [
        'id' => ['id'],
        'handel' => ['id','state'],
        'save' => ['content', 'title', 'imgs'],
        'comment_save' => ['content', 'parent_id', 'f_id'],
        'comments' => ['id', 'page', 'size']
    ];
}