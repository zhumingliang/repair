<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/5/1
 * Time: 下午10:17
 */

namespace wxpay;


use wxpay\database\WxPayResults;

class WxRefundApi extends ApiCommon
{

    /**
     * 微信用户退款
     * @param \wxpay\database\WxPayRefund $refund
     * @param int $timeOut
     * @return array
     * @throws WxPayException
     */
    public static function unifiedOrder($refund, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";

        $refund->setAppid(WxPayConfig::$APPID);
        $refund->setMchId(WxPayConfig::$MCHID);
        $refund->setNonceStr(ApiCommon::getNonceStr());
        $refund->setSign();
        $xml = $refund->toXml();

        $response = ApiCommon::postXmlCurl($xml, $url, true, $timeOut);
        $result = WxPayResults::Init($response);
        return $result;


    }
}