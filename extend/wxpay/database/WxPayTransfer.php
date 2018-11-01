<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/4/30
 * Time: 下午12:43
 */

namespace wxpay\database;


class WxPayTransfer extends WxPayDataBase
{


    /**
     * 设置微信分配的公众账号ID
     * @param string $value
     **/
    public function setAppid($value)
    {
        $this->values['appid'] = $value;
    }

    /**
     * 设置微信支付分配的商户号
     * @param string $value
     **/
    public function setMchId($value)
    {
        $this->values['mchid'] = $value;
    }

    /**
     * 设置微信支付分配的商户号mch_appid
     * @param string $value
     **/
    public function setMchAppId($value)
    {
        $this->values['mch_appid'] = $value;
    }


    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['nonce_str'] = $value;
    }

    /**
     *设置转账金额
     * @param int $value
     **/
    public function setAmount($value)
    {
        $this->values['amount'] = $value;
    }

    /**
     *设置商户订单号
     * @param sting $value
     **/
    public function setPartnerTradeNo($value)
    {
        $this->values['partner_trade_no'] = $value;
    }

    /**
     *校验用户姓名选项，NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
     *（未实名认证的用户会校验失败，无法转账）
     * OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
     * @param sting $value
     **/
    public function setCheckName($value)
    {
        $this->values['check_name'] = $value;
    }

    /**
     *用户姓名
     * @param sting $value
     **/
    public function setReUserName($value)
    {
        $this->values['re_user_name'] = $value;
    }

    /**
     *设置请求ip
     * @param sting $value
     **/
    public function setSpbillCreateIp($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }

    /**
     *设置描述
     * @param sting $value
     **/
    public function setDesc($value)
    {
        $this->values['desc'] = $value;
    }

    /**
     *设置openid
     * @param sting $value
     **/
    public function setOpenid($value)
    {
        $this->values['openid'] = $value;
    }


}