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

}