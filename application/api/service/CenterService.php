<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/20
 * Time: 7:45 PM
 */

namespace app\api\service;


use app\api\model\DemandOrderV;
use app\api\model\OrderNormalMsgT;
use app\api\model\OrderShopMsgT;
use app\api\model\UserT;
use app\lib\enum\CommonEnum;

class CenterService
{
    private $u_id;
    private $shop_id;

    public function __construct()
    {
        $this->u_id = Token::getCurrentUid();
        $this->shop_id = Token::getCurrentTokenVar('shop_id');
    }

    /**
     * 获取我的-信息
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getCenterInfo()
    {
        return [
            'userInfo' => $this->userInfo(),
            'balance' => $this->balance(),
            'msg_count' => $this->msgCount(),
            'demand_count' => $this->demandCount()
        ];


    }

    /**
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    private function userInfo()
    {
        return UserT::where('id', Token::getCurrentUid())
            ->field('nickName,province,city,country,phone,avatarUrl,address')->find()->toArray();
    }

    private function balance()
    {
        if (!$this->shop_id) {
            return 0;
        }
        //获取店铺余额
        return WithDrawService::getBusinessBalance($this->shop_id);
    }

    private function msgCount()
    {

        if (!$this->shop_id) {
            return OrderNormalMsgT::where('u_id', $this->u_id)
                ->where('state', CommonEnum::STATE_IS_OK)
                ->count();
        }

        return OrderShopMsgT::where('u_id', $this->u_id)->where('state', CommonEnum::STATE_IS_OK)
            ->count();
    }

    private function demandCount()
    {
        if (!$this->shop_id) {
            return DemandOrderV::getCountForNormal($this->u_id);
        }

        return DemandOrderV::getCountForShop($this->shop_id);

    }

}