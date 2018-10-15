<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/27
 * Time: 下午11:53
 */

namespace app\api\model;


use app\api\service\Token;
use app\lib\enum\CommonEnum;
use think\Model;

class UserRedT extends Model
{

    public function detail()
    {
        return $this->belongsTo('RedT',
            'r_id', 'id');
    }

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d', strtotime($value));

    }


    /**
     * 获取用户红包列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        $u_id = 1;// Token::getCurrentUid();
        $time_begin = date('Y-m-d', strtotime('-' . 30 . ' day',
            time()));
        $list = self::with(['detail' => function ($query) {
            $query->field('id,name');
        }])
            ->where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('u_id', '=', $u_id)
            ->whereTime('create_time', '>', $time_begin)
            ->field('id,r_id,create_time,money')
            ->order('create_time desc')
            ->select();
        return $list;

    }


}