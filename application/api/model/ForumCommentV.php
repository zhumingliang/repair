<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-02-16
 * Time: 00:16
 */

namespace app\api\model;


use think\Model;

class ForumCommentV extends Model
{
    public static function getComment($f_id, $page, $size)
    {
        $pagingData = self::where('f_id', '=', $f_id)
            ->hidden(['openid', 'c_id'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;
    }


}