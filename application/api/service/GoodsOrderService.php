<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 6:49 PM
 */

namespace app\api\service;


use app\api\model\GoodsOrderT;
use app\api\model\GoodsOrderV;
use app\api\model\UserScoreV;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;

class GoodsOrderService
{
    public function save($params)
    {
        $u_id = Token::getCurrentUid();
        if (!$this->checkScore($u_id, $params['score'])) {
            return 0;
        }
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['u_id'] = $u_id;
        $params['code_number'] = makeOrderNo();
        $params['status'] = 1;
        $params['comment_id'] = 0;
        $res = GoodsOrderT::create($params);
        if (!$res) {
            throw  new OperationException();
        }
        return 1;

    }

    /**
     * 小程序获取订单列表
     * @param $type
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getListForMINI($type, $page, $size)
    {
        $u_id = Token::getCurrentUid();
        $list = array();
        if ($type == 1) {
            //获取全部
            $list = GoodsOrderV::getListForMINIWithAll($u_id, $page, $size);
        } else if ($type == 2) {
            //待发货
            $list = GoodsOrderV::getListForMINIWithNoSend($u_id, $page, $size);

        } else if ($type == 3) {
            //待收货
            $list = GoodsOrderV::getListForMINIWithNoReceive($u_id, $page, $size);
        } else if ($type == 4) {
            //待评价
            $list = GoodsOrderV::getListForMINIWithNoComment($u_id, $page, $size);
        }
        return $list;

    }

    /**
     * PC获取订单列表
     * @param $type
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     */
    public function getListForCMS($type, $page, $size)
    {
        $list = array();
        if ($type == 1) {
            //获取全部
            $list = GoodsOrderV::getListForCMSWithALL($page, $size);
        } else if ($type == 2) {
            //获取未发货
            $list = GoodsOrderV::getListForCMSWithNoSend($page, $size);
        } else if ($type == 3) {
            $list = GoodsOrderV::getListForCMSWithComplete($page, $size);
        }
        return $list;

    }

    /*  private function preOrderExpress($u_id)
      {
          $list = GoodsOrderT::getListForNOComplete($u_id);
          if (count($list)) {
              foreach ($list as $k => $v) {
                  if ($v['express']) {
                      $res = $this->checkExpress($v['express'], $v['express_code'], $v['express_status']);
                      if (!$res['res']) {
                          GoodsOrderT::update(['express_status' => $res['type']], ['id' => $v['id']]);
                      }
                  }

              }
          }
      }*/

    private function checkExpress($express, $express_code, $type)
    {
        return [
            'res' => true,
            'type' => 3
        ];

    }


    private
    function checkScore($u_id, $score)
    {
        $user_score = UserScoreV::getUserScore($u_id);
        /*
         if ($user_score - $score < 0) {
             throw  new OperationException([
                 'code' => 401,
                 'msg' => '兑换失败,积分余额不足',
                 'errorCode' => 100002
             ]);

         }*/

        return $user_score - $score;


    }


}