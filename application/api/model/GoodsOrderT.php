<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:16 AM
 */

namespace app\api\model;


use think\Model;

class GoodsOrderT extends Model
{

    public function goods()
    {
        return $this->belongsTo('GoodsT',
            'g_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('UserT',
            'u_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo('AddressT',
            'a_id', 'id');
    }

    public static function getListForNOComplete($u_id)
    {
        $list = self::where('express_status', '<', 3)
            ->select();
        return $list;

    }

    public static function getInfoForCMS($id)
    {
        $info = self::where('id', $id)
            ->with([
                'address' => function ($query) {
                    $query->field('id,name,phone,province,city,area,detail');
                },
                'goods' => function ($query) {
                    $query->field('id,name');
                }

            ])
            ->hidden(['a_id', 'u_id', 'g_id', 'state', 'update_time'])
            ->find();
        return $info;

    }


    public static function getInfoForMINI($id)
    {
        $info = self::where('id', $id)
            ->with([
                'address' => function ($query) {
                    $query->field('id,name,phone,province,city,area,detail');
                },
                'goods' => function ($query) {
                    $query->field('id,name,cover,money');
                },
                'user' => function ($query) {
                    $query->field('id,nickName,avatarUrl');
                },

            ])
            ->hidden(['a_id', 'u_id', 'g_id', 'state', 'update_time'])
            ->find();
        return $info;

    }


}