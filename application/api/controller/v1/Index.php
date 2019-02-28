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

    private function preScore($money = 1000, $openid = "osEM-5TP__6QYVhf95dyZUBHDdxo", $order_type = 1, $order_id = 647)
    {


    }


    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
