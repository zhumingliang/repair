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
        $codes = $this->generateCode($nums);
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
            if (!$save_res){
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

    /**
     * 生成vip激活码
     * @param int $nums 生成多少个优惠码
     * @param array $exist_array 排除指定数组中的优惠码
     * @param int $code_length 生成优惠码的长度
     * @param int $prefix 生成指定前缀
     * @return array                 返回优惠码数组
     */
    private function generateCode($nums, $exist_array = '', $code_length = 6, $prefix = '')
    {

        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz";
        $promotion_codes = array();//这个数组用来接收生成的优惠码

        for ($j = 0; $j < $nums; $j++) {

            $code = '';

            for ($i = 0; $i < $code_length; $i++) {

                $code .= $characters[mt_rand(0, strlen($characters) - 1)];

            }

            //如果生成的4位随机数不再我们定义的$promotion_codes数组里面
            if (!in_array($code, $promotion_codes)) {

                if (is_array($exist_array)) {

                    if (!in_array($code, $exist_array)) {//排除已经使用的优惠码

                        $promotion_codes[$j] = $prefix . $code; //将生成的新优惠码赋值给promotion_codes数组

                    } else {

                        $j--;

                    }

                } else {

                    $promotion_codes[$j] = $prefix . $code;//将优惠码赋值给数组

                }

            } else {
                $j--;
            }
        }

        return $promotion_codes;
    }

}