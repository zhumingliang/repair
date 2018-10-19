<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/12
 * Time: 10:52 PM
 */

namespace app\api\model;


use app\api\service\Token;
use think\Model;

class CircleCommentV extends Model
{

    public function zans()
    {
        return $this->hasMany('CommentZanT',
            'c_id', 'id');
    }

    public static function getList($page, $size, $c_id)
    {//Token::getCurrentUid()
        $pagingData = self::with(['zans' => function ($query) {
            $query->where('u_id', Token::getCurrentUid());
        }])->where('c_id', '=', $c_id)
            ->hidden(['openid', 'c_id'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        return $pagingData;
    }


}