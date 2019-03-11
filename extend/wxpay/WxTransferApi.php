<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/4/30
 * Time: 下午12:40
 */

namespace wxpay;


use app\api\model\LogT;
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
        $transfer->setSpbillCreateIp(self::getServerIp());
        $transfer->setNonceStr(ApiCommon::getNonceStr());
        $transfer->setSign();
        var_dump($transfer);
       /* $xml = $transfer->toXml();
        $response = ApiCommon::postXmlCurl($xml, $url, true, $timeOut);
        $result = WxPayResults::Init2($response);
        return $result;*/

       // <xml><amount>1.0000</amount><check_name><![CDATA[NO_CHECK]]></check_name><desc><![CDATA[商户提现]]></desc><mch_appid><![CDATA[wx21b17ce43511ef1a]]></mch_appid><mchid>1354265502</mchid><nonce_str><![CDATA[rqd50kx5mfkeavc6su3lzav7exx78prx]]></nonce_str><openid><![CDATA[osEM-5eqzFwX5eUXgse0aaAS680Q]]></openid><partner_trade_no><![CDATA[BB09951195487279]]></partner_trade_no><re_user_name><![CDATA[盟蚁网络科技～朱明良]]></re_user_name><spbill_create_ip><![CDATA[192.168.0.1]]></spbill_create_ip><sign><![CDATA[E3F2C181E4AB32E8A7D8D2FEEF053B63]]></sign></xml>
    }

    public static function getServerIp()
    {
        $server_ip = '127.0.0.1';
        if (isset($_SERVER)) {
            if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } elseif (isset($_SERVER['LOCAL_ADDR']) && $_SERVER['LOCAL_ADDR']) {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }

}