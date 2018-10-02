<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/5/27
 * Time: 下午4:06
 */

namespace app\api\model;


use think\Model;

class AdminT extends Model
{
    public function adminJoin()
    {
        return $this->hasOne('AdminJoinT',
            'admin_id', 'id');
    }


}