<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/29
 * Time: 10:37 AM
 */

namespace app\api\service;


use app\api\controller\v1\Shop;
use app\api\model\DemandOrderT;
use app\api\model\DemandOrderV;
use app\api\model\DemandV;
use app\api\model\FormidT;
use app\api\model\ServiceOrderV;
use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use zml\tp_aliyun\SendSms;

class SendMsgService
{
    private $id;
    private $phone;
    private $params;
    private $openid;
    private $form_id;
    private $source_id;

    public function __construct($id, $source_id)
    {
        $this->id = $id;
        $this->source_id = $source_id;
    }


    public function sendToNormal()
    {
        if (self::checkFormID()) {
            //发送模板消息
            (new WxTemplate($this->openid, $this->form_id, $this->params))->sendToNormal();

        } else {
            //发送短息消息
            $this->getOrderInfo(2);
            SendSms::instance()->send($this->phone, $this->params);
        }


    }


    public function sendToShop()
    {
        if (self::checkFormID()) {
            //发送模板消息
            (new WxTemplate($this->openid, $this->form_id, $this->params))->sendToShop();

        } else {
            //发送短息消息
            $this->getOrderInfo(1);
            SendSms::instance()->send($this->phone, $this->params);
        }

    }

    /**
     * 检查用户是否有可以使用的formid
     * @return mixed|null
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkFormID()
    {
        $u_id = Token::getCurrentUid();
        $form = FormidT::where('u_id', $u_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereTime('create_time', 'week')
            ->find();
        if (count($form)) {
            $this->form_id = $form->form_id;
            return true;
        }
        return null;
    }

    private function getOrderInfo($type)
    {
        if ($type == 1) {
            //服务订单信息
            $info = ServiceOrderV::where('order_id', $this->id)
                ->find();
            $params = [
                'time' => $info->order_time,
                'time_begin' => $info->time_begin,
                'server' => $info->source_name,
                'phone' => $info->user_phone,
                'remark' => $info->remark

            ];
            $this->phone = $info->shop_phone;
            $this->params = $params;
            $this->getOpenidForShop();

        } else {
            //需求订单信息
            $info = DemandOrderV::where('order_id', $this->id)
                ->find();

            $params = [
                'demand' => $info->source_name,
                'shop_name' => $info->shop_name,
                'phone' => $info->shop_phone,
                'time' => $info->order_time

            ];
            $this->phone = $info->user_phone;
            $this->params = $params;
            $this->getOpenidForNormal();

        }

    }

    private function getOpenidForShop()
    {
        $shop = ShopT::where('id', $this->source_id)->with('user')->find();
        $this->openid = $shop->user->openId;

    }

    private function getOpenidForNormal()
    {
        $demand = DemandV::where('id', $this->source_id)->find();
        $this->openid = $demand->openId;
    }


}