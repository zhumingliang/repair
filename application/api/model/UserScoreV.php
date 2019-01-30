<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 7:08 PM
 */

namespace app\api\model;


use think\Model;

class UserScoreV extends Model
{
    public static function getUserScore($u_id)
    {
        $score = self::where('u_id', $u_id)->sum('score');
        return $score;

    }

    /**
     * @param $u_id
     * @param $type
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getList($u_id, $type, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->where(function ($query) use ($type) {
                if ($type == 1) {
                    $query->where('score', '>', 0);

                } elseif ($type == 2) {
                    $query->where('score', '<', 0);

                }
            })
            ->paginate($size, false, ['page' => $page])->toArray();

        return $list;
    }

}