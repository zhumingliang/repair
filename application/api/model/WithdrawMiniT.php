<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 11:34 AM
 */

namespace app\api\model;


use think\Model;

class WithdrawMiniT extends Model
{
    public static function getList($u_id, $page, $size)
    {
        $list = self::where('u_id', $u_id)
            ->field('id,money/100 as money,"微信零钱" as account,create_time,state,pay_id,type')
            ->order('state desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;

    }

}