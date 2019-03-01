<?php

namespace app\api\controller\v1;

use app\api\model\LogT;
use app\api\model\ScoreOrderRoleT;
use app\api\model\ScoreOrderT;
use app\api\model\UserT;
use app\api\service\SendMsgService;
use app\lib\enum\CommonEnum;
use think\Env;

class Index
{
    public function index()
    {
        $this->preScore();

    }

    private function preScore($money = 100, $openid = "osEM-5TP__6QYVhf95dyZUBHDdxo", $order_type = 1, $order_id = 657)
    {
        //获取订单积分设置
        $info = ScoreOrderRoleT::field('self,parent as parent_score,parent_other')->find();
        if ($info) {
            $self = $info->self;
            $parent = $info->parent_score;
            $parent_other = $info->parent_other;
            $user = UserT::getByOpenID($openid);
            $u_id = $user->id;
            $parent_id = $user->parent_id;

            $self_score = $self * $money / 10000;
            $self_score = $self_score * (1 + $parent_other / 100);
            $parent_score = ($self * $money / 10000) * $parent / 100;
            $save_data = array();
            $self_data = [
                'u_id' => $u_id,
                'score' => $self_score,
                'order_type' => $order_type,
                'source_id' => $order_id,
                'self' => CommonEnum::STATE_IS_OK
            ];
            array_push($save_data, $self_data);
            if ($parent_id) {
                $parent_data = [
                    'u_id' => $parent_id,
                    'score' => $parent_score,
                    'order_type' => $order_type,
                    'source_id' => $order_id,
                    'self' => CommonEnum::STATE_IS_FAIL
                ];
                array_push($save_data, $parent_data);

            }

        }
        print_r($save_data);
        return 0;

    }


    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
