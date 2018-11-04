<?php

namespace app\api\controller\v1;

use app\api\service\WithDrawService;

class Index
{
    public function index($money)
    {
       /* $token = "4013e96782dcf82dcf8bc0d5cd51b202";
        $info = \app\api\service\Token::getCurrentTokenVar();
        return json(json_decode($info));*/
       WithDrawService::apply(1,$money);
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
