<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/27
 * Time: 4:48 PM
 */

namespace app\api\model;


use think\Model;

class SystemTimeT extends Model
{
    public static function getSystemOrderTime()
    {
        $shop_confirm = $pay = $user_confirm = $consult = 1200;
        $orderTime = SystemTimeT::find();
        if ($orderTime) {
            $shop_confirm = $orderTime->shop_confirm;
            $pay = $orderTime->pay;
            $user_confirm = $orderTime->user_confirm;
            $consult = $orderTime->consult;
        }
        return [
            'shop_confirm' => $shop_confirm,
            'pay' => $pay,
            'user_confirm' => $user_confirm,
            'consult' => $consult
        ];
    }


}