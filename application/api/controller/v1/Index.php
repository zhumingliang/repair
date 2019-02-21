<?php

namespace app\api\controller\v1;

use app\api\service\SendMsgService;
use think\Env;

class Index
{
    public function index()
    {
    /*    $d_id = 56;
        $order_id = 251;

        (new SendMsgService($order_id, $d_id))->sendToShop();*/
        //WithDrawService::apply(1,$money);
        echo \think\facade\Env::get('app_path');

    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
