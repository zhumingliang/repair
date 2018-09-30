<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/27
 * Time: 下午11:15
 */

namespace app\api\service;


use app\api\model\RedStrategyT;
use app\api\model\RedT;
use app\api\model\UserRedT;
use app\lib\enum\CommonEnum;
use app\lib\exception\RedException;

class RedService
{
    /**
     * 保存用户红包
     * @param $red_type
     * @throws RedException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addRed($red_type)
    {
        $u_id = Token::getCurrentUid();
        $red = RedT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('type', '=', $red_type)
            ->find();
        $money = rand($red->money_min, $red->money_max);
        $data = ['u_id' => $u_id,
            'r_id' => $red->id,
            'money' => $money,
            'state' => CommonEnum::STATE_IS_OK
        ];
        $res = UserRedT::create($data);
        if (!$res) {
            throw  new RedException();

        }


    }


    /**
     * 获取用户可用红包
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        $list = UserRedT::getList();
        if (count($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['end_time'] = date('Y-m-d', strtotime('+' . 30 . ' day',
                    strtotime($list[$k]['create_time'])));
            }
        }
        return $list;

    }


    /**
     * 使用红包
     * @param $id
     * @throws RedException
     */
    public static function redUse($id)
    {
        $res = UserRedT::update(['id' => $id], ['state' => CommonEnum::STATE_IS_FAIL]);
        if (!$res) {
            throw  new RedException(['code' => 401,
                'msg' => '红包使用失败',
                'errorCode' => 90002]);

        }

    }

    /**
     * 首页获取红包攻略列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getStrategyList()
    {
        return RedStrategyT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->field('id,des')
            ->select();

    }


}