<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/31
 * Time: 10:43 PM
 */

namespace app\api\model;


use think\Model;

class WithdrawPcT extends Model
{


    public function getMoneyAttr($value, $data)
    {
        return $value / 100;

    }

    public function admin()
    {
        return $this->belongsTo('adminT',
            'admin_id', 'id');
    }


}