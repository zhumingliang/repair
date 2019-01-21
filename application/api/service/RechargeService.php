<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/13
 * Time: 11:47 PM
 */

namespace app\api\service;


use app\api\model\RechargeT;
use app\api\model\ScoreExchangeT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class RechargeService
{


    /**
     * 生成积分兑换码
     * @param $nums
     * @param $score
     * @throws OperationException
     */
    public function save($nums, $score)
    {
        $codes = generateCode($nums);
        $data = $this->preData($codes, $score);
        $recharge = new RechargeT();
        $res = $recharge->saveAll($data);
        if (!$res) {
            throw new OperationException();
        }


    }

    /**
     * 积分兑换
     * @param $code
     * @throws Exception
     */
    public function exchange($code)
    {
        Db::startTrans();
        try {
            $recharge = $this->checkRechargeUse($code);
            $recharge->state = CommonEnum::STATE_IS_FAIL;
            $res = $recharge->save();
            if (!$res) {
                Db::rollback();
                throw  new OperationException([
                    'code' => 401,
                    'msg' => '兑换码兑换失败',
                    'errorCode' => 160011
                ]);
            }
            //新增兑换记录
            $data = array(
                'score' => $recharge->score,
                'r_id' => $recharge->id,
                'u_id' => Token::getCurrentUid()
            );
            $save_res = ScoreExchangeT::create($data);
            if (!$save_res) {
                Db::rollback();
                throw  new OperationException([
                    'code' => 401,
                    'msg' => '积分兑换记录添加失败',
                    'errorCode' => 160011
                ]);
            }

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }


    /**
     * 检查兑换码是否被使用
     * @param $code
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws OperationException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkRechargeUse($code)
    {
        $recharge = RechargeT::where('code', $code)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->find();
        if (!count($recharge)) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '兑换码无效',
                'errorCode' => 160010
            ]);
        }

        return $recharge;

    }

    private function preData($codes, $score)
    {
        $list_arr = array();
        foreach ($codes as $k => $v) {
            $list['code'] = $v;
            $list['score'] = $score;
            $list['admin_id'] = Token::getCurrentUid();
            $list['state'] = CommonEnum::STATE_IS_OK;
            array_push($list_arr, $list);

        }

        return $list_arr;

    }


}