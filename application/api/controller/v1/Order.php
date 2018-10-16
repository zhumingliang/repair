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

    /**
     * @api {POST} /api/v1/order/taking  78-商家接单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 接单成功之后跳转至需求服务-待服务列表
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {int} id  需求id
     * @apiSuccessExample {json} 返回样例:
     *{"id":1}
     * @apiSuccess (返回参数说明) {int} id 订单id
     * @throws \app\lib\exception\OrderException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function orderTaking()
    {
        (new OrderValidate())->scene('taking')->goCheck();
        $id = $this->request->param('id');
        $u_id = TokenService::getCurrentUid();
        OrderService::taking($id, $u_id);


    }

    /**
     * 79-获取需求信息
     * $order_id
     */
    public function getDemandInfo()
    {

    }

    /**
     * 80-获取自己需求列表
     * 店铺 type: 待服务；待确认；已完成
     */
    public function getDemandListForShop()
    {

    }

    /**
     * 81-获取自己需求列表
     * 普通用户 type: 待接单；待付款；待确认；待评价；已完成
     */
    public function getDemandListForNormal()
    {

    }

    /**
     * 82-用户/店铺 确认电话
     */
    public function phone()
    {

    }

    /**
     * 83-店铺修改价格
     */
    public function updatePrice()
    {

    }

    /**
     * 84-用户评价
     */
    public function comment()
    {

    }

}