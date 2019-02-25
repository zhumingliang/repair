<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/14
 * Time: 10:58 AM
 */

namespace app\api\model;


use think\Model;

class RechargeT extends Model
{
    public static function getList($page, $size)
    {
        $pagingData = self::field('id,code,state,score,create_time')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;
    }


}