<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 10:26 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\OrderService;
use app\api\validate\OrderValidate;
use app\api\service\Token as TokenService;

class Order extends BaseController
{

    public function orderTaking()
    {
        (new OrderValidate())->scene('taking')->goCheck();
        $id = $this->request->param('id');
        $u_id =10;// TokenService::getCurrentUid();
        OrderService::taking($id, $u_id);


    }

}