<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/26
 * Time: 下午6:47
 */

namespace app\api\model;


use think\Model;

class ServiceExtendT extends Model
{


    public function service(){
        return $this->belongsTo('ServicesT',
            's_id', 'id');
    }

}