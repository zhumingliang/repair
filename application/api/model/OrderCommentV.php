<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/19
 * Time: 5:05 PM
 */

namespace app\api\model;


use think\Model;

class OrderCommentV extends Model
{
    public function imgs()
    {
        return $this->hasMany('OrderCommentImgT',
            'o_id', 'id');
    }


}