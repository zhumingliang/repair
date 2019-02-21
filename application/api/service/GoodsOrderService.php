<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 6:49 PM
 */

namespace app\api\service;


use app\api\controller\v1\Goods;
use app\api\model\GoodsCommentImgT;
use app\api\model\GoodsOrderCommentT;
use app\api\model\GoodsOrderT;
use app\api\model\GoodsOrderV;
use app\api\model\GoodsT;
use app\api\model\UserScoreV;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class GoodsOrderService
{
    public function save($params)
    {
        $u_id = Token::getCurrentUid();
        if ($this->checkScore($u_id, $params['score']) < 0) {
            return 0;
        }
        $g_id = $params['g_id'];
        $goods = GoodsT::where('id', $g_id)->find();
        $params['score'] = $goods->score * $params['count'];
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
    public function getListForCMS($type, $page, $size,$key)
    {
        $list = array();
        if ($type == 1) {
            //获取全部
            $list = GoodsOrderV::getListForCMSWithALL($page, $size,$key);
        } else if ($type == 2) {
            //获取未发货
            $list = GoodsOrderV::getListForCMSWithNoSend($page, $size,$key);
        } else if ($type == 3) {
            $list = GoodsOrderV::getListForCMSWithComplete($page, $size,$key);
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


    public function getTheOrderForCMS($id)
    {
        $info = GoodsOrderT::getInfoForCMS($id);
        $info['comment'] = $this->getOrderComment($id);
        return $info;
    }

    private function getOrderComment($id)
    {
        $comment = GoodsOrderCommentT::getComment($id);
        return $comment;

    }

    public function getTheOrderForMINI($id)
    {
        $info = GoodsOrderT::getInfoForMINI($id);
        $info['comment'] = $this->getOrderComment($id);
        $info['express_info'] = $this->getExpressInfo($info['express_no'], $info['express_code']);
        return $info;


    }

    /**
     * 保存评论
     * @param $params
     * @throws Exception
     */
    public function saveComment($params)
    {

        Db::startTrans();
        try {

            $params['u_id'] = Token::getCurrentUid();
            $params['state'] = CommonEnum::STATE_IS_OK;
            $obj = GoodsOrderCommentT::create($params);
            if (!$obj) {
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '新增评论失败',
                        'errorCode' => 150010
                    ]
                );
            }
            if (key_exists('imgs', $params) && strlen($params['imgs'])) {
                $imgs = $params['imgs'];
                $relation = [
                    'name' => 'c_id',
                    'value' => $obj->id
                ];
                $res = $this->saveImageRelation($imgs, $relation);
                if (!$res) {
                    Db::rollback();
                    throw new OperationException(
                        [
                            'code' => 401,
                            'msg' => '创建评论图片关联失败',
                            'errorCode' => 150011
                        ]
                    );
                }
            }

            //修改评论状态
            $com_id = GoodsOrderT::update(['comment_id' => $obj->id], ['id' => $params['o_id']]);
            if (!$com_id) {
                Db::rollback();
                throw new OperationException(
                    [
                        'code' => 401,
                        'msg' => '修改评论状态失败',
                        'errorCode' => 150011
                    ]
                );
            }

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }

    /**
     * @param $imgs
     * @param $relation
     * @return bool
     * @throws \Exception
     */
    private function saveImageRelation($imgs, $relation)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $OCI = new GoodsCommentImgT();
        $res = $OCI->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }


    public function getExpressInfo($express_no, $express_code)
    {
        if (!strlen($express_no)) {
            return array();
        }
        $info = (new ExpressService($express_code, $express_no))->getInfo();
        if ($info->code == 0) {
            return $data = $info->data;
            // return $data[0]->data;
            // return ($data[0]->data)[0];

        } else {
            return array();
        }
    }


}