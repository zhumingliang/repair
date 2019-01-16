<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 11:24 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class AddressT extends Model
{
    public static function getList($u_id)
    {
        $list = self::where('u_id',$u_id)->where('state', CommonEnum::STATE_IS_OK)
            ->hidden(['create_time','state','update_time','latitude','longitude','u_id'])
            ->order('type,create_time desc')
            ->select();
        return $list;
    }

}