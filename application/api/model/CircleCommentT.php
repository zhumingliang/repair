<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/10
 * Time: 6:23 PM
 */

namespace app\api\model;


use think\Model;

class CircleCommentT extends Model
{

    public static function getList($page, $size, $c_id)
    {
        $pagingData = self::where('c_id', '=', $c_id)
            ->where('parent_id', '=', 0)
            ->field('id,parent_id,nickName,avatarUrl,content,create_time')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();

        return $pagingData;
    }


}