<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/22
 * Time: 下午10:55
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Pay as PayService;
use wxpay\PayNotifyCallBack;

class Pay extends BaseController
{


    /**
     * @api {GET} /api/v1/pay/getPreOrder  43-小程序端获取微信支付数据
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 小程序端获取微信支付数据（预约服务支付/需求支付/保证金支付）
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/pay/getPreOrder?id=1&type=1&r_id=1
     * @apiParam (请求参数说明) {int} id 订单id
     * @apiParam (请求参数说明) {int} type 订单类别：1 | 预约服务支付；2 | 需求支付；3 | 保证金支付
     * @apiParam (请求参数说明) {int} r_id  支付选取红包id
     * @apiSuccessExample {json} 返回样例:
     * {
     * "jsApiParameters": "{\"appId\":\"wxe259f1f58695b35e\",\"nonceStr\":\"m8l2v92he4ca4vjpfscpgbt5u0l8optz\",\"package\":\"prepay_id=wx201705061742181721b8ef8e0586973210\",\"signType\":\"MD5\",\"timeStamp\":\"1494063753\",\"paySign\":\"2177F2F635987A96D0F05B72299CF855\"}"
     * }
     * @apiSuccess (返回参数说明) {String} jsApiParameters 前端支付所需数据
     * @param string $id
     * @return \think\response\Json
     * @throws \app\lib\exception\BookingException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \wxpay\WxPayException
     */
    public function getPreOrder($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $pay = new PayService($id);
        return json($pay->pay());
    }


    /**
     * 接受支付回调
     * @return string
     * @throws \think\Exception
     */
    public function receiveNotify()
    {
        $notify = new PayNotifyCallBack();
        $notify->handle(true);
        if ($notify->getReturnCode() == 'SUCCESS') {
            $attach = $notify->getAttach();
            $attach_arr = explode("#", $attach);
            $order_id = $attach_arr[0];
            $type = $attach_arr[1];
            $pay = new PayService($order_id, $type, '');
            $res = $pay->receiveNotify($notify);
            if ($res) {
                return '<xml>
              <return_code><![CDATA[SUCCESS]]></return_code>
              <return_msg><![CDATA[OK]]></return_msg>
          </xml>';
            }

        }


    }

}