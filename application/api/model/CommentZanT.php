<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/11
 * Time: 1:49 AM
 */

namespace app\api\model;


use think\Model;

class CommentZanT extends Model
{

    protected $hidden=['create_time','update_time','c_id','u_id'];
}