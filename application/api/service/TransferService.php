<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/1
 * Time: 9:38 PM
 */

namespace app\api\service;


use app\api\model\WithdrawMiniT;
use app\api\model\WithdrawMiniV;
use app\lib\enum\CommonEnum;
use app\lib\exception\PayException;
use wxpay\database\WxPayTransfer;
use wxpay\WxTransferApi;

class TransferService
{

    private $orderID = '';

    public function __construct($id)
    {
        $this->orderID = $id;

    }

    /**
     * 企业转账给用户
     * @throws PayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \wxpay\WxPayException
     */
    public function transferToUser()
    {

        $transfer = $this->makeWxPreOrder();
        $result = WxTransferApi::unifiedOrder($transfer);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            //更新状态
            WithdrawMiniT::update(['state' => 2], ['id' => $this->orderID]);

        } else {

            throw new PayException(
                [
                    'code' => 401,
                    'msg' => '该提现申请处理失败，请稍后再试。',
                    'errorCode' => 150011
                ]
            );
        }
    }

    /**
     * 准备转账数据
     * @return WxPayTransfer
     * @throws PayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function makeWxPreOrder()
    {
        $order = WithdrawMiniV::where('id', $this->orderID)->find();
        if ($order->state == CommonEnum::PASS) {
            throw new PayException([
                'code' => 401,
                'msg' => '该提现申请已经处理，不能重复操作。',
                'errorCode' => 150010
            ]);

        }
        $money = floatval($order->money)*100;
        $payTransfer = new WxPayTransfer();
        $payTransfer->setAmount($money);
        $payTransfer->setDesc("商户提现");
        $payTransfer->setOpenid($order->openid);
        $payTransfer->setPartnerTradeNo($order->order_number);
        $payTransfer->setReUserName($order->nickName);
        return $payTransfer;

    }


}