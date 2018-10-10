<?php

namespace wxpay\database;

/**
 * 回调基础类
 *
 * Class WxPayNotifyReply
 * @package wxpay\database
 * @author goldeagle
 */
class WxPayNotifyReply extends WxPayDataBase
{
    /**
     *
     * 设置错误码 FAIL 或者 SUCCESS
     * @param string
     */
    public function setReturnCode($return_code)
    {
        $this->values['return_code'] = $return_code;
    }

    /**
     *
     * 获取错误码 FAIL 或者 SUCCESS
     * @return string $return_code
     */
    public function getReturnCode()
    {
        return $this->values['return_code'];
    }

    /**
     * 设置错误信息
     * @param string $return_msg
     */
    public function setReturnMsg($return_msg)
    {
        $this->values['return_msg'] = $return_msg;
    }

    /**
     *
     * 获取错误信息
     * @return string
     */
    public function getReturnMsg()
    {
        return $this->values['return_msg'];
    }

    /**
     * 设置商户平台订单号
     * @param string $return_msg
     */
    public function setOutTradeNo($return_msg)
    {
        $this->values['out_trade_no'] = $return_msg;
    }

    /**
     *
     * 获取商户平台订单号
     * @return string
     */
    public function getOutTradeNo()
    {
        return $this->values['out_trade_no'];
    }

    /**
     * 设置微信平台订单号
     * @param string $return_msg
     */
    public function setTransactionId($return_msg)
    {
        $this->values['transaction_id'] = $return_msg;
    }

    /**
     *
     * 获取微信平台订单号
     * @return string
     */
    public function getTransactionId()
    {
        return $this->values['transaction_id'];
    }

    /**
     * 设置附加数据
     * @param string $return_msg
     */
    public function setAttach($return_msg)
    {
        $this->values['attach'] = $return_msg;
    }

    /**
     *
     * 获取微信平台订单号
     * @return string
     */
    public function getAttach()
    {
        return $this->values['attach'];
    }

    /**
     * 设置支付金额
     * @param string $return_msg
     */
    public function setTotalFee($return_msg)
    {
        $this->values['total_fee'] = $return_msg;
    }

    /**
     *
     * 获取支付金额
     * @return string
     */
    public function getTotalFee()
    {
        return $this->values['total_fee'];
    }


    public function setData($return_msg)
    {
        $this->values['data'] = $return_msg;
    }

    public function getData()
    {
        return $this->values['data'];
    }
}