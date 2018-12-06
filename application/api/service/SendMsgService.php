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
use app\api\model\LogT;
use app\api\model\ServiceOrderV;
use app\api\model\ShopT;
use app\lib\enum\CommonEnum;
use think\Exception;
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
        try {
            $this->getOpenidForNormal();
            $this->getOrderInfo(2);
            if (self::checkFormID()) {
                //发送模板消息
                $wx_res = (new WxTemplate($this->openid, $this->form_id, $this->params))->sendToNormal();
                if ($wx_res) {
                    $this->updateFormId();
                } else {
                    $this->sendSms('normal');
                }
            } else {
                //发送短息消息
                $this->sendSms('normal');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }


    }


    public function sendToShop()
    {
        $this->getOpenidForShop();
        $this->getOrderInfo(1);
        if (self::checkFormID()) {
            //发送模板消息
            $wx_res = (new WxTemplate($this->openid, $this->form_id, $this->params))->sendToShop();
            if ($wx_res) {
                $this->updateFormId();
            } else {
                $this->sendSms('shop');
            }
        } else {
            $this->sendSms('shop');
        }

    }

    private function sendSms($type)
    {
        LogT::create(['msg'=>$this->phone]);
        SendSms::instance()->send($this->phone, $this->params, $type);

    }

    /**
     * 检查用户是否有可以使用的formid
     * @return mixed|null
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkFormID()
    {
        $time_end = addDay(1, date('Y-m-d H:i', time()));
        $time_begin = reduceDay(6, $time_end);
        $form = FormidT::where('openId', $this->openid)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->whereBetweenTime('create_time', $time_begin, $time_end)
            ->order('create_time desc')
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
                'id' => $this->id,
                'time' => $info->order_time,
                'time_begin' => $info->time_begin,
                'server' => $info->source_name,
                'phone' => $info->user_phone,
                'remark' => $info->remark

            ];
            $this->phone = $info->shop_phone;
            $this->params = $params;

        } else {
            //需求订单信息
            $info = DemandOrderV::where('order_id', $this->id)
                ->find();

            $params = [
                'id' => $this->id,
                'demand' => $info->source_name,
                'shop_name' => $info->shop_name,
                'phone' => $info->shop_phone,
                'time' => $info->order_time

            ];
            $this->phone = $info->user_phone;
            $this->params = $params;
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

    private function updateFormId()
    {

        FormidT::update(['state' => CommonEnum::STATE_IS_FAIL], ['form_id' => $this->form_id]);
    }


}