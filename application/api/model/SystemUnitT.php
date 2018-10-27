<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/23
 * Time: 1:48 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class SystemUnitT extends Model
{
    public static function getUnitsForMini()
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->field('id,name')
            ->select();
        return $list;
    }

}