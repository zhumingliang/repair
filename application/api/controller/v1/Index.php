<?php

namespace app\api\controller\v1;

class Index
{
    public function index()
    {
        $token = "4013e96782dcf82dcf8bc0d5cd51b202";
        $info = \app\api\service\Token::getCurrentTokenVar();
        return json(json_decode($info));
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
