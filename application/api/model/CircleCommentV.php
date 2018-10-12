<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/12
 * Time: 10:52 PM
 */

namespace app\api\model;


use think\Model;

class CircleCommentV extends Model
{
    public static function getList($page, $size, $c_id)
    {
        $pagingData = self::where('c_id', '=', $c_id)
            ->hidden(['openid','c_id'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);

        return $pagingData;
    }


}