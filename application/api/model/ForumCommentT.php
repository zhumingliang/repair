<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-30
 * Time: 11:11
 */

namespace app\api\model;


use think\Model;

class ForumCommentT extends Model
{
    public static function getComment($f_id, $page, $size)
    {

        $comments = self::where('f_id', $f_id)
            ->where('state', '=', 2)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $comments;
    }

}