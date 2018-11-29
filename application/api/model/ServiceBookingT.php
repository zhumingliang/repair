<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/4
 * Time: 2:47 PM
 */

namespace app\api\model;


use think\Model;

class ServiceBookingT extends Model
{
    public function service()
    {
        return $this->belongsTo('ServicesT',
            's_id', 'id');
    }


}