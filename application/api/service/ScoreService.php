<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 12:07 AM
 */

namespace app\api\service;


use app\api\model\ScoreBuyRuleT;
use app\api\model\ScoreBuyT;
use app\api\model\SignDayT;
use app\api\model\SignInT;
use app\api\model\SystemSignInT;
use app\api\model\UserScoreV;
use app\api\model\UserT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;

class ScoreService
{
    /**
     * 新增购买积分记录
     * @param $id
     * @return mixed
     * @throws OperationException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function buy($id)
    {
        $rule = ScoreBuyRuleT::where('id', $id)->find();
        if (!$rule) {
            throw  new OperationException([
                'msg' => 'id不存在'
            ]);
        }
        $params['money'] = $rule->money;
        $params['score'] = $rule->score;
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

    public function signIn()
    {
        $u_id = Token::getCurrentUid();
        $rule = SystemSignInT::find();
        if (!$rule) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '签到失败，签到规则没有设置',
                'errorCode' => 300005
            ]);
        }

        $last = SignInT::where('u_id', $u_id)
            ->order('create_time desc')
            ->find();
        if (!$last) {
            $score = $this->checkSignDay($u_id, true, $rule);
        } else {

            //判断今天是否已经签到
            if (strtotime(date('Y-m-d')) ==
                strtotime(date('Y-m-d', strtotime($last->create_time)))) {
                throw  new OperationException([
                    'code' => 401,
                    'msg' => '今日已经签到',
                    'errorCode' => 300001
                ]);
            }
            $score = $this->checkSignDay($u_id, false, $rule);
        }

        //新增签到记录
        $res = SignInT::create([
            'u_id' => $u_id,
            'score' => $score
        ]);

        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '签到失败',
                'errorCode' => 300002
            ]);
        }
        return $score;

    }

    /**
     * 处理此次签到积分
     * @param $u_id
     * @param $first
     * @param $rule
     * @return float|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkSignDay($u_id, $first, $rule)
    {
        if ($first) {
            $data = [
                'u_id' => $u_id,
                'count' => 1,
            ];
            SignDayT::create($data);
            return $rule->begin_score;
        }
        $info = SignDayT::where('u_id', $u_id)->find();
        if ($info->count + 1 == $rule->cycle) {
            SignDayT::update(['count' => 0], ['u_id' => $u_id]);
        } else {
            SignDayT::update(['count' => $info->count + 1], ['u_id' => $u_id]);

        }
        return ($info->count) * ($rule->add) + $rule->begin_score;

    }

    public function checkSignInToday()
    {
        $u_id = Token::getCurrentUid();
        $count = SignInT::where('u_id', $u_id)
            ->whereTime('create_time', 'today')
            ->count();
        if (!$count) {
            return [
                'sign_in' => 0
            ];
        }

        return [
            'sign_in' => 1,
            'score' => UserScoreV::getUserScore($u_id),
        ];
    }


    public function getScoreList($type, $page, $size)
    {
        $u_id = Token::getCurrentUid();
        $list = UserScoreV::getList($u_id, $type, $page, $size);
        $list['balance'] = UserScoreV::getUserScore($u_id);
        return $list;

    }

    /**
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getUserScoreList($page, $size)
    {
        $list = UserScoreV::getUserScoreList($page, $size);
        return $list;

    }

    public function getUserScoreInfo($page, $size)
    {
        $u_id = Token::getCurrentUid();
        $list = UserScoreV::getUserScoreInfo($u_id, $page, $size);
        $list['balance'] = UserScoreV::getUserScore($u_id);
        $list['user'] = UserT::getUserInfo($u_id);
        return $list;

    }

}