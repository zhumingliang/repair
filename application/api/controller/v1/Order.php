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
use app\api\model\OrderCommentV;
use app\api\model\OrderNormalMsgT;
use app\api\model\ServiceBookingT;
use app\api\model\SystemTimeT;
use app\api\service\ExtendService;
use app\api\service\OrderMsgService;
use app\api\service\OrderService;
use app\api\service\ShopService;
use app\api\validate\OrderValidate;
use app\api\service\Token as TokenService;


use app\lib\enum\CommonEnum;
use app\lib\exception\DemandException;
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
     * @api {GET} /api/v1/order 79-获取订单信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 普通用户/店铺获取订单详情（服务订单/需求订单）
     *
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/order?id=1&type=2
     * @apiParam (请求参数说明) {int} id  订单id
     * @apiParam (请求参数说明) {int} type  订单类别 ：1 | 服务订单；2 需求订单
     * @apiSuccessExample {json} 返回样例:
     * {"order_id":1,"source_id":1,"shop_id":1,"source_name":"修马桶","update_money":800,"phone_shop":2,"phone_user":2,"user_name":"朱明良","pay_money":800,"shop_name":"修之家","time_begin":"2018-10-17 08:00:00","time_end":"2018-10-01 12:00:00","order_number":"BA16602025038574","order_time":"2018-10-16 11:23:22","area":"铜官山区","address":"高速","origin_money":800,"comment_id":99999,"confirm_id":99999,"pay_id":99999,"refund_id":99999,"u_id":1}
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {int} source_id 订单关联服务/需求id
     * @apiSuccess (返回参数说明) {int} shop_id 店铺id
     * @apiSuccess (返回参数说明) {String} source_name 订单关联服务/需求名称
     * @apiSuccess (返回参数说明) {int} money 订单金额
     * @apiSuccess (返回参数说明) {int} phone_user 商家是否联系用户：1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {int} phone_shop 用户是否联系商家：1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {String} user_name 用户命
     * @apiSuccess (返回参数说明) {int} pay_money 支付金额（涉及红包）
     * @apiSuccess (返回参数说明) {int} origin_money 原价
     * @apiSuccess (返回参数说明) {int} update_money 修改后价格
     * @apiSuccess (返回参数说明) {String} shop_name 店铺名称
     * @apiSuccess (返回参数说明) {String} time_begin 服务开始时间
     * @apiSuccess (返回参数说明) {String} time_end 服务结束时间
     * @apiSuccess (返回参数说明) {String} order_number 订单号
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 地址
     * @apiSuccess (返回参数说明) {String} order_time 订单创建时间
     * @apiSuccess (返回参数说明) {int} comment_id 评论状态：99999 | 没有评论 ；除此之外表示已经评论
     * @apiSuccess (返回参数说明) {int} confirm_id 确认操作id：未确认：99999；1  | 完工；2 | 协商
     * @apiSuccess (返回参数说明) {int} shop_confirm  商家是否 确认订单
     * @apiSuccess (返回参数说明) {int} pay_id 支付id：99999 | 未支付 ；除此之外表示已经支付
     * @apiSuccess (返回参数说明) {int} refund_id 退款id：99999 | 未退款 ；除此之外表示已经退款
     *
     * $order_id
     */
    public function getOrderInfo()
    {
        (new OrderValidate())->scene('phone')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $info = OrderService::getOrderInfo($id, $type);
        return json($info);


    }

    /**
     * @api {GET} /api/v1/order/demand/list 80-获取需求订单列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 普通用户/店铺获取需求订单列表
     *
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/order/demand/list?page=1&size=10&order_type&list_type=1
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} list_type  订单入口：1 | 普通用户入口；2 | 商家入口
     * @apiParam (请求参数说明) {int} order_type  用户订单类别 ：1 | 待接单；2 | 待付款；3 | 待确认；4 | 待评价；5 | 已完成
     * 商铺订单类别：1 | 待服务；2 | 待确认；3 | 已完成
     * @apiSuccessExample {json} 待接单-返回样例:
     * {"total":1,"per_page":"10","current_page":1,"last_page":1,"data":[{"order_id":1,"source_name":"修马桶","time_begin":"2018-10-17 08:00:00","time_end":"2018-10-01 12:00:00","money":800}]}
     * @apiSuccessExample {json} 用户（待付款/待确认/待评价/）、商家（待服务；待确认；已完成）-返回样例:
     * {"total":1,"per_page":"10","current_page":1,"last_page":1,"data":[{"order_id":2,"source_name":"修电脑","time_begin":"2018-10-17 08:00:00","time_end":"2018-10-15 12:00:00","origin_money":800,"update_money":800}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {String} source_name 需求名称
     * @apiSuccess (返回参数说明) {String} time_begin 服务开始时间
     * @apiSuccess (返回参数说明) {String} time_end 服务结束时间
     * @apiSuccess (返回参数说明) {String} user_phone 用户手机号
     * @apiSuccess (返回参数说明) {String} shop_phone 店铺手机号
     * @apiSuccess (返回参数说明) {int} origin_money 订单原金额
     * @apiSuccess (返回参数说明) {int} update_money 订单修改之后金额
     * @apiSuccess (返回参数说明) {int} phone_user 商家是否联系用户：1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {int} phone_shop 用户是否联系商家：1 | 是；2 | 否
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getDemandList()
    {
        (new OrderValidate())->scene('list')->goCheck();
        $params = $this->request->param();
        $list = OrderService::getDemandList($params['order_type'], $params['page'], $params['size'], $params['list_type']);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/order/service/list 81-获取服务列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 普通用户/店铺获取服务订单列表
     *
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/order/service/list?page=1&size=10&order_type&list_type=1
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} list_type  订单入口：1 | 普通用户入口；2 | 商家入口
     * @apiParam (请求参数说明) {int} order_type  用户订单类别 ： 已预约；待付款；待确认；待评价；已完成（1-5）
     * 店铺订单类别：待确认；待服务；服务中；已完成(1-4)
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"10","current_page":1,"last_page":1,"data":[{"order_id":1,"source_id":5,"shop_id":1,"source_name":"修五金4","time_begin":"2018-10-19 23:24:46","time_end":"2018-10-06 23:24:49","origin_money":1000,"update_money":10000,"phone_shop":1,"phone_user":1}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} order_id 订单id
     * @apiSuccess (返回参数说明) {String} source_name 服务名称
     * @apiSuccess (返回参数说明) {String} time_begin 服务开始时间
     * @apiSuccess (返回参数说明) {String} time_end 服务结束时间
     * @apiSuccess (返回参数说明) {String} user_phone 用户手机号
     * @apiSuccess (返回参数说明) {String} shop_phone 店铺手机号
     * @apiSuccess (返回参数说明) {int} origin_money 订单原金额
     * @apiSuccess (返回参数说明) {int} update_money 订单修改之后金额
     * @apiSuccess (返回参数说明) {int} phone_user 商家是否联系用户：1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {int} phone_shop 用户是否联系商家：1 | 是；2 | 否
     *
     * 普通用户 type: 已预约；待付款；待确认；待评价；已完成（1-5）
     * 店铺 type: 待确认；待服务；服务中；已完成(1-4)
     */
    public function getServiceList()
    {
        (new OrderValidate())->scene('list')->goCheck();
        $params = $this->request->param();
        $list = OrderService::getServiceList($params['order_type'], $params['page'], $params['size'], $params['list_type']);
        return json($list);

    }

    /**
     * @api {POST} /api/v1/order/phone/confirm  82-（需求订单/服务订单）确认电话联系
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 确认电话联系
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 1
     *     }
     * @apiParam (请求参数说明) {int} id  订单id
     * @apiParam (请求参数说明) {int} type  订单类别:1 | 服务订单；2 | 需求订单
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
            $res = DemandOrderT::update([$user => CommonEnum::STATE_IS_OK], ['id' => $id]);
        } else {
            $res = ServiceBookingT::update([$user => CommonEnum::STATE_IS_OK], ['id' => $id]);
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
     *       "type": 2,
     *       "money": 100,
     *       "price_remark": "我就是想修改价格"
     *     }
     * @apiParam (请求参数说明) {int} id  需求id
     * @apiParam (请求参数说明) {int} type  订单类别:1 | 服务订单；2 | 需求订单
     * @apiParam (请求参数说明) {int} money 修改之后的价格
     * @apiParam (请求参数说明) {String} price_remark 备注
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
        $remark = $this->request->param('price_remark');
        $money = $this->request->param('money');
        $money = $money * 100;
        $type = $this->request->param('type');
        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $res = DemandOrderT::update(['update_money' => $money,
                'price_remark' => $remark],
                ['id' => $id]);
        } else {

            $order_info = ServiceBookingT::where('id', $id)->find();
            $s_id = $order_info->s_id;
            $money = ExtendService::preExpendPrice($s_id, $money);
            $res = ServiceBookingT::update(['update_money' => $money,
                'price_remark' => $remark],
                ['id' => $id]);
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
     * @api {POST} /api/v1/order/comment  84-订单-用户评价（需求订单/服务订单）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 用户评价
     * @apiExample {post}  请求样例:
     *    {
     *       "o_id": 1,
     *       "s_id": 1,
     *       "order_type": 2,
     *       "content": "这次服务我很满意。",
     *       "score_type": 1,
     *       "score": 5
     *       "imgs": 1,2,3
     *     }
     * @apiParam (请求参数说明) {int} o_id  服务订单id/需求订单id
     * @apiParam (请求参数说明) {int} s_id  店铺id
     * @apiParam (请求参数说明) {int} order_type  订单类别：1 | 服务订单；2 | 需求订单
     * @apiParam (请求参数说明) {int} score_type  评价类别：1  | 好评；2| 中评；3 | 差评
     * @apiParam (请求参数说明) {String} content  评价内容
     * @apiParam (请求参数说明) {String} score  分数：每颗星星代表一分
     * @apiParam (请求参数说明) {String} imgs  评论图片id：逗号隔开
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function comment()
    {
        (new OrderValidate())->scene('comment')->goCheck();
        $params = $this->request->param();
        OrderService::saveComment($params);
        return json(new SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/order/service/begin 85-商家去服务操作（需求订单/服务订单）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 商家去服务操作
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 2
     *     }
     * @apiParam (请求参数说明) {int} id  订单id
     * @apiParam (请求参数说明) {int} type  订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     */
    public function serviceBegin()
    {
        (new OrderValidate())->scene('phone')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        //检测订单是否已经支付

        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $res = DemandOrderT::update(['service_begin' => CommonEnum::STATE_IS_OK], ['id' => $id]);
        } else {
            $res = ServiceBookingT::update(['service_begin' => CommonEnum::STATE_IS_OK], ['id' => $id]);
        }
        if (!$res) {
            throw  new OrderException(
                ['code' => 401,
                    'msg' => '去服务状态修改失败！',
                    'errorCode' => 150009
                ]
            );

        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/order/confirm 85-用户确认操作（需求订单/服务订单）完工/协商
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 用户确认操作（需求订单/服务订单）完工/协商
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 2,
     *       "confirm": 1,
     *     }
     * @apiParam (请求参数说明) {int} id  订单id
     * @apiParam (请求参数说明) {int} type  订单类别：1 | 服务订单；2 | 需求订单
     * @apiParam (请求参数说明) {int} confirm 确认状态：1 | 完工；2 | 协商
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     */
    public function confirm()
    {
        (new OrderValidate())->scene('confirm')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $confirm = $this->request->param('confirm');

        if ($confirm == 2) {
            $consult_time = date('Y-m-d H:i', time());
            $data = [
                'confirm_id' => $confirm,
                'consult_time' => $consult_time
            ];
        } else {

            $data = ['confirm_id' => $confirm];
        }
        if ($type == CommonEnum::ORDER_IS_DEMAND) {

            $res = DemandOrderT::update($data, ['id' => $id]);

        } else {
            $res = ServiceBookingT::update($data, ['id' => $id]);
        }

        if (!$res) {
            throw  new OrderException(
                ['code' => 401,
                    'msg' => '确认状态修改失败！',
                    'errorCode' => 150009
                ]
            );

        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/order/shop/confirm  86-商家确认订单(服务订单/需求订单)
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 1
     *     }
     * @apiParam (请求参数说明) {int} id  服务id
     * @apiParam (请求参数说明) {int} type  订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     * @throws OrderException
     * @throws \app\lib\exception\OrderMsgException
     * @throws \app\lib\exception\ParameterException
     */

    public function shopConfirmOrder()
    {
        (new OrderValidate())->scene('phone')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $res = DemandOrderT::update(['shop_confirm' => CommonEnum::STATE_IS_OK], ['id' => $id]);

        } else {
            $res = ServiceBookingT::update(['shop_confirm' => CommonEnum::STATE_IS_OK], ['id' => $id]);
        }
        if (!$res) {
            throw  new OrderException(
                ['code' => 401,
                    'msg' => '确认状态修改失败！',
                    'errorCode' => 150009
                ]
            );
        }

        //生成订单通知信息
        // OrderMsgService::saveNormal(OrderService::getUID($id, $type), $id, $type, 2);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/order/comments 100-获取店铺评论列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/order/comments?page=1&size=1&id=1
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} id  店铺id
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"shop_id":1,"content":"这次服务我很满意。","avatarUrl":"","create_time":"2018-10-17 11:16:46"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} shop_id 店铺id
     * @apiSuccess (返回参数说明) {String} content 评论内容
     * @apiSuccess (返回参数说明) {String} avatarUrl 头像
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     *
     * @param $id
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getCommentsForShop($id, $page, $size)
    {
        $list = OrderCommentV::where('shop_id', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->field('o_id,img_id');
                }])
            ->paginate($size, false, ['page' => $page]);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/index/search 101-首页搜索
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/index/search?type=5&area=铜官区&page=1&size=10&search_type=1&key=
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} search_type 查询类别：1 | 店铺；2 | 服务
     * @apiParam (请求参数说明) {int} type 排序类别：1 | 综合，2 |价格由高到底 3 | 价格由低到高，4| 销售量，5 | 销售量由低到高，6 | 销售量由高到底
     * @apiParam (请求参数说明) {int} area  区
     * @apiSuccessExample {json} 返回样例:
     * {"total":3,"per_page":"10","current_page":1,"last_page":1,"data":[{"id":2,"cover":"static\/imgs\/5782AD69-9B21-2B94-DCCA-6AD299AF32E1.jpg","name":"修五金2","price":"0"},{"id":4,"cover":"static\/imgs\/E72CCAE6-79A1-D88D-F755-48FE0DB381BC.jpg","name":"修五金3","price":"0"},{"id":5,"cover":"static\/imgs\/CE41DE68-9E89-B6C1-E63D-57149CC54BBF.jpg","name":"修五金4","price":"10000"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int}  id 订单id/店铺订单
     * @apiSuccess (返回参数说明) {String} cover 服务/店铺封面图
     * @apiSuccess (返回参数说明) {String} name 服务/店铺名称
     * @apiSuccess (返回参数说明) {String} price 金额
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function indexSearch()
    {
        $params = $this->request->param();
        $list = ShopService::getListIndex($params['search_type'], $params['type'], $params['area'], $params['key'], $params['page'], $params['size']);
        return json($list);
    }

    /**
     * @api {POST} /api/v1/order/pay/check  105-检测订单是否已经支付（需求订单/服务订单）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  1.需求订单 ：店铺-点击去服务-需要检测用户是否已经支付;2.服务订单 ：店铺-点击去服务-需要检测用户是否已经支付
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 1,
     *     }
     * @apiParam (请求参数说明) {int} id  订单id
     * @apiParam (请求参数说明) {int} id  订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccessExample {json} 返回样例:
     *{"sate":1}
     * @apiSuccess (返回参数说明) {int} state 支付状态：1 | 已经支付；2 | 没有支付
     * @param $id
     * @param $type
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkPay($id, $type)
    {
        return json([
            'state' => OrderService::checkOrderPay($id, $type)
        ]);

    }

    /**
     * @api {POST} /api/v1/order/phone/check 106-检测订单是否电话沟通（需求订单/服务订单）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription1 1.需求订单 ：用户-点击付款-需要检测商家有无选择已经电话联系;2.服务订单：用户-点击付款-需要检测商家有无选择已经电话联系
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 1,
     *     }
     * @apiParam (请求参数说明) {int} id  订单id
     * @apiParam (请求参数说明) {int} id  订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccessExample {json} 返回样例:
     *{"sate":1}
     * @apiSuccess (返回参数说明) {int} state 支付状态：1 | 是；2 | 否
     *
     * @param $id
     * @param $type
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkPhone($id, $type)
    {
        return json([
            'state' => OrderService::checkPhone($id, $type)
        ]);

    }

    /**
     * @api {POST} /api/v1/order/service/handel  109-小程序用户取消预约订单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户取消需求订单
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 订单id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws DemandException
     */
    public function serviceHandel()
    {
        $params = $this->request->param();
        $id = ServiceBookingT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new DemandException(['code' => 401,
                'msg' => '操作需求状态失败',
                'errorCode' => 120002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/order/delete  186-订单删除（需求订单/服务订单）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序删除订单
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "type": 2,
     * }
     * @apiParam (请求参数说明) {int} id 订单id
     * @apiParam (请求参数说明) {int} type 订单类别：1 | 服务订单；2 | 需求订单
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @param $type
     * @return \think\response\Json
     * @throws DemandException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function deleteCompleteOrder($id, $type)
    {
        $shop_id = \app\api\service\Token::getCurrentTokenVar('shop_id');
        if ($shop_id) {
            $field = 'shop_delete';
        } else {
            $field = 'normal_delete';
        }
        if ($type == CommonEnum::ORDER_IS_DEMAND) {
            $res = DemandOrderT::update([$field => CommonEnum::STATE_IS_FAIL], ['id' => $id]);

        } else {
            $res = ServiceBookingT::update([$field => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        }
        if (!$res) {
            throw new DemandException(['code' => 401,
                'msg' => '删除订单失败',
                'errorCode' => 120003
            ]);
        }

        return json(new SuccessMessage());

    }


}