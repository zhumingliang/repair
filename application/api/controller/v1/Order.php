<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 10:26 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\DemandOrderT;
use app\api\model\DemandOrderV;
use app\api\model\ServiceBookingT;
use app\api\service\OrderService;
use app\api\validate\OrderValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Order extends BaseController
{

    /**
     * @api {POST} /api/v1/order/taking  78-商家接单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 接单成功之后跳转至需求服务-订单信息
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
        (new OrderValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        $u_id = TokenService::getCurrentUid();
        $o_id = OrderService::taking($id, $u_id);
        return json([
            'id' => $o_id
        ]);

    }

    /**
     * 79-获取需求信息
     * $order_id
     */
    public function getDemandInfo()
    {
        (new OrderValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        $info = DemandOrderV::where('id', $id)->find();
        return json($info);


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
     * @api {POST} /api/v1/order/phone/confirm  82-用户/店铺（需求订单/服务订单）确认电话联系
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 确认电话联系
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 2
     *     }
     * @apiParam (请求参数说明) {int} id  需求id
     * @apiParam (请求参数说明) {int} type  订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function phone()
    {
        (new OrderValidate())->scene('phone')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $shop_id = TokenService::getCurrentTokenVar('shop_id');
        $user = $shop_id ? 'phone_user' : 'phone_shop';

        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $res = DemandOrderT::update([$user => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        } else {
            $res = ServiceBookingT::update([$user => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        }
        if (!$res) {
            throw  new OrderException(
                ['code' => 401,
                    'msg' => '修改是否联系状态失败！',
                    'errorCode' => 150007
                ]
            );

        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/order/price/update  82-商家修改订单价格（需求订单/服务订单）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 商家修改订单价格
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 2
     *     }
     * @apiParam (请求参数说明) {int} id  需求id
     * @apiParam (请求参数说明) {int} type  订单类别：1 | 服务订单；2 | 需求订单
     * @apiParam (请求参数说明) {int} money 修改之后的价格
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     * @return \think\response\Json
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function updatePrice()
    {
        (new OrderValidate())->scene('price')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $money = $this->request->param('money');

        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $res = DemandOrderT::update(['origin_money' => $money], ['id' => $id]);
        } else {
            $res = ServiceBookingT::update(['origin_money' => $money], ['id' => $id]);
        }
        if (!$res) {
            throw  new OrderException(
                ['code' => 401,
                    'msg' => '修改价格失败！',
                    'errorCode' => 150008
                ]
            );

        }
        return json(new SuccessMessage());

    }

    /**
     * 84-用户评价
     */
    public function comment()
    {

    }

}