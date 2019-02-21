<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-02-21
 * Time: 22:32
 */

namespace app\api\model;


use think\Model;

class ForumCommentListV extends Model
{
    public static function getList($day, $type, $page, $size, $key)
    {
        $time_end = addDay(1, $day);
        $pagingData = self::whereBetweenTime('create_time', $day, $time_end)
            ->where(function ($query) use ($type) {
                if ($type == 1) {
                    $query->where('state', '=', $type);
                }
            })
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('nickName|title|content', 'like', '%' . $key . '%');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;
    }

}