<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/22
 * Time: 下午10:56
 */

namespace app\api\service;

use app\api\controller\v1\Demand;
use app\api\model\BondT;
use app\api\model\DemandOrderT;
use app\api\model\DemandT;
use app\api\model\JoinCommissionT;
use app\api\model\ServiceBookingT;
use app\api\model\ServicesT;
use app\api\model\ShopT;
use app\api\model\UserRedT;
use app\api\model\UserT;
use app\api\model\WxPayT;

use app\lib\enum\CommonEnum;
use app\lib\enum\RedEnum;
use app\lib\exception\PayException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Model;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\JsApiPay;
use wxpay\WxPayApi;


class Pay
{

    private $orderID;
    private $type;
    private $r_id;
    private $orderNumber;

    function __construct($orderID, $type, $r_id)
    {
        if (!$orderID) {
            throw new Exception('订单号不允许为NULL');
        }
        if (!$type) {
            throw new Exception('订单类别不允许为NULL');
        }
        $this->orderID = $orderID;
        $this->type = $type;
        $this->r_id = $r_id;
    }

    /**
     * 获取微信端预支付信息
     * @return array|string
     * @throws Exception
     * @throws PayException
     * @throws TokenException
     * @throws \wxpay\WxPayException
     */
    public function pay()
    {
        //订单号可能根本就不存在
        //订单号确实是存在的，但是，订单号和当前用户是不匹配的
        //订单有可能已经被支付过
        $status = $this->checkOrderValid();
        if (!$status['pass']) {
            return $status;
        }
        //检查红包
        $redMoney = $this->checkRed();
        $pay_money = $status['orderPrice'] - $redMoney;
        return $this->makeWxPreOrder($pay_money);
    }


    /**
     * 处理微信回调
     * @param $notify
     * @return bool
     * @throws PayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function receiveNotify($notify)
    {
        //检查订单是否已经完成支付处理
        //修改订单状态
        //存储支付信息
        $order = $this->getOrder();
        if ($order->pay_id != CommonEnum::ORDER_STATE_INIT) {
            return true;
        }

        $money = $notify->getTotalFee();
        $pay_id = $this->savePayRecord($notify, $order);
        $order->pay_id = $pay_id;
        $order->pay_money = $money;

        if ($this->type == 1 || $this->type == 2) {
            $discount = $this->getDiscount();
            $order->join_money = ($order->origin_money) * $discount;
        }

        if ($order->r_id) {
            UserRedT::update(['state' => 2], ['id' => $order->r_id]);
        }

        if (!$order->save()) {
            // LogService::Log('微信支付回调成功后修改订单状态出错，id：' . $this->orderID);
            //修改信息失败
            return false;
        }

        return true;
    }


    private function getDiscount()
    {

        //获取店铺信息
        $shop_id = 0;
        if ($this->type == CommonEnum::ORDER_IS_DEMAND) {
            $info = DemandOrderT::where('id', $this->orderID)
                ->field('s_id')->find();
            $shop_id = $info['s_id'];
        } else if ($this->type == CommonEnum::ORDER_IS_BOOKING) {
            $info = ServiceBookingT::where('id', $this->orderID)->field('s_id')
                ->find();
            $service_id = $info['s_id'];
            $service_info = ServicesT::where('id', $service_id)->field('shop_id')
                ->find();
            $shop_id = $service_info['shop_id'];
        }
        $shop_info = ShopT::where('id', $shop_id)->field('province,city,area')
            ->find();

        //获取该地区的加盟商佣金比例
        $sql = preJoinSql($shop_info['province'], $shop_info['city'], $shop_info['area']);
        $join = JoinCommissionT::where('state', CommonEnum::STATE_IS_OK)
            ->whereRaw($sql)
            ->find();

        if (!count($join)) {
            return 0.1;
        } else {
            return $join['discount'] / 100;
        }


    }

    /**
     * 生成预支付信息
     * @param $totalPrice
     * @return string
     * @throws BookingException
     * @throws Exception
     * @throws TokenException
     * @throws \wxpay\WxPayException
     */
    private
    function makeWxPreOrder($totalPrice)
    {

        $openid = Token::getCurrentOpenid();
        if (!$openid) {
            throw new TokenException();
        }
        $attend = $this->orderID . '#' . $this->type;
        $input = new WxPayUnifiedOrder();
        $input->setBody('家政维修小程序');
        $input->setAttach($attend);//添加附加数据
        $input->setOutTradeNo($this->orderNumber);
        //$input->setTotalFee($totalPrice * 100);
        $input->setTotalFee(1);
        $input->setTimeStart(date("YmdHis"));
        $input->setTimeExpire(date("YmdHis", time() + 600));
        $input->setNotifyUrl(config('secure.pay_back_url'));
        $input->setTradeType("JSAPI");
        $input->setOpenid($openid);
        $wxOrder = WxPayApi::unifiedOrder($input);
        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        ) {
            throw new PayException(
                [
                    'code' => 401,
                    'msg' => '获取微信预支付信息出错',
                    'errorCode' => 150006
                ]
            );
        }
        $this->recordPreOrder($wxOrder);
        $tools = new JsApiPay();
        $jsApiParameters = $tools->getJsApiParameters($wxOrder);
        return $jsApiParameters;
    }


    /**
     * @param $wxOrder
     * @throws PayException
     */
    private
    function recordPreOrder($wxOrder)
    {
        $prepay_id = $wxOrder['prepay_id'];
        if ($this->type == CommonEnum::ORDER_IS_BOOKING) {
            ServiceBookingT::update(['prepay_id' => $prepay_id],
                ['id' => $this->orderID]);

        } elseif ($this->type == CommonEnum::ORDER_IS_DEMAND) {
            DemandOrderT::update(['prepay_id' => $prepay_id],
                ['id' => $this->orderID]);

        } elseif ($this->type == CommonEnum::ORDER_IS_BOND) {
            BondT::update(['prepay_id' => $prepay_id],
                ['id' => $this->orderID]);

        } else {
            throw new PayException();
        }
    }


    /**
     * 检查订单有效性
     * @return array
     * @throws Exception
     * @throws PayException
     * @throws TokenException
     */
    private
    function checkOrderValid()
    {
        $order = self::getOrder();

        if (!$order) {
            throw new PayException(
                ['msg' => '订单不存在',
                    'errorCode' => 150002,
                    'code' => 401
                ]
            );
        }
        if (!Token::isValidOperate($order->openid)) {
            //if (0) {
            throw new PayException(
                [
                    'msg' => '订单与用户不匹配',
                    'errorCode' => 150003
                ]);
        }

        if ($order->state != CommonEnum::STATE_IS_OK) {
            throw new PayException(
                [
                    'msg' => '订单已取消',
                    'errorCode' => 150004,
                    'code' => 401
                ]);
        }


        if ($order->pay_id != CommonEnum::ORDER_STATE_INIT) {
            //if (0) {
            throw new PayException(
                [
                    'msg' => '订单已支付过啦',
                    'errorCode' => 150005,
                    'code' => 401
                ]);
        }
        $this->orderNumber = $order->order_number;
        if ($this->r_id) {
            $order->r_id = $this->r_id;
            $order->save();
        }
        $status = [
            'pass' => true,
            'orderPrice' => $order->origin_money
        ];

        return $status;
    }

    /**
     * @return DemandOrderT|array|null|\PDOStatement|string|\think\Model
     * @throws PayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private
    function getOrder()
    {
        if ($this->type == CommonEnum::ORDER_IS_BOOKING) {
            $order = ServiceBookingT::where('id', '=', $this->orderID)->find();
        } elseif ($this->type == CommonEnum::ORDER_IS_DEMAND) {
            $order = DemandOrderT::where('id', '=', $this->orderID)->find();
            $order['openid'] = $this->getOpenidForDemand($order->d_id);
        } elseif ($this->type == CommonEnum::ORDER_IS_BOND) {
            $order = BondT::where('id', '=', $this->orderID)
                ->field('id,u_id,1 as state,money as origin_money,pay_id,openid,order_number')
                ->find();
        } else {
            throw new PayException();
        }
        return $order;

    }

    private
    function getOpenidForDemand($id)
    {

        $demand = DemandT::where('id', $id)
            ->find();
        $user = UserT::where('id', $demand->u_id)->find();
        return $user->openId;


    }

    /**
     * @param $notify
     * @param $order
     * @return mixed
     */
    private
    function savePayRecord($notify, $order)
    {
        $this->payRed($order->openid);
        $wpt = new WxPayT();
        $wpt->out_trade_no = $notify->getOutTradeNo();
        $wpt->openid = $order->openid;
        $wpt->source_id = $this->orderID;
        $wpt->source_type = $this->type;
        $wpt->total_fee = $notify->getTotalFee();
        $wpt->transaction_id = $notify->getTransactionId();
        if (!$wpt->save()) {
            //存储失败
            echo "error=spr";
            /*
                        LogService::Log('微信支付回调成功后保存支付信息出错，notify：'
                            . json_decode($notify));*/
        }

        return $wpt->id;
    }


    private function payRed($openid)
    {
        $count = WxPayT::where('openid', $openid)
            ->count();
        if (!$count) {
            $user = UserT::where('openid', $openid)->field('id')->find()->toArray();
            RedService::addRed(RedEnum::FIRST_ORDER, $user['id']);
        }


    }

    /**
     * 检查红包状态/获取红包金额
     * @return mixed
     * @throws Exception
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private
    function checkRed()
    {

        if (!$this->r_id) {
            return 0;
        }
        $red = UserRedT::where('u_id', '=', Token::getCurrentUid())
            ->where('id', '=', $this->r_id)
            ->find();
        if ($red->state == RedEnum::USED) {
            return 0;
            /* throw new PayException(
                 [
                     'msg' => '红包已经被使用',
                     'errorCode' => 150006,
                     'code' => 401
                 ]);*/
        }

        return ($red->money) * 100;

    }


}