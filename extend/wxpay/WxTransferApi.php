<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/4/30
 * Time: 下午12:40
 */

namespace wxpay;


use wxpay\database\WxPayResults;

class WxTransferApi extends ApiCommon
{
    /**
     * 企业向微信用户转账
     * @param \wxpay\database\WxPayTransfer $transfer
     * @param int $timeOut
     * @return array
     * @throws WxPayException
     */
    public static function unifiedOrder($transfer, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

        $transfer->setCheckName("NO_CHECK");
        $transfer->setMchId(WxPayConfig::$MCHID);
        $transfer->setMchAppId(WxPayConfig::$APPID);
        $transfer->setSpbillCreateIp('192.168.0.1');
        $transfer->setNonceStr(ApiCommon::getNonceStr());
        $transfer->setSign();
        $xml = $transfer->toXml();
        print_r($xml);
        $response = ApiCommon::postXmlCurl($xml, $url, true, $timeOut);
        $result = WxPayResults::Init($response);
        return $result;


    }

}