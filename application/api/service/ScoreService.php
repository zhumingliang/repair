<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 12:07 AM
 */

namespace app\api\service;


use app\api\model\ScoreBuyT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;

class ScoreService
{
    /**
     * 新增购买积分记录
     * @param $params
     * @return mixed
     * @throws OperationException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function buy($params)
    {
        $params['pay_id'] = CommonEnum::ORDER_STATE_INIT;
        $params['u_id'] = Token::getCurrentUid();
        $params['openid'] = Token::getCurrentOpenid();
        $params['order_number'] = makeOrderNo();
        $params['r_id'] = CommonEnum::ORDER_STATE_INIT;
        $params['pay_money'] = 0;
        $res = ScoreBuyT::create($params);
        if (!$res) {
            throw  new OperationException();
        }
        return $res->id;

    }

}