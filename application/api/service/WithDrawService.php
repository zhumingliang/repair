<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 10:07 AM
 */

namespace app\api\service;


use app\api\model\BondBalanceV;
use app\api\model\BusinessBalanceV;
use app\api\model\JoinBalanceV;
use app\api\model\PaymentsV;
use app\api\model\SystemTimeT;
use app\api\model\WithdrawMiniT;
use app\api\model\WithdrawPcT;
use app\lib\enum\CommonEnum;
use app\lib\exception\WithdrawException;
use think\Model;

class WithDrawService
{
    /**
     * 获取用户余额
     * @return array|int
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function getBalance()
    {
        //获取佣金设置-加盟商设置

        $shop_id = Token::getCurrentTokenVar('shop_id');
        if (!$shop_id) {
            return ['balance' => 0];
        } else {
            //余额：保证金/营业额度
            $bond_balance = self::getBondBalance(Token::getCurrentUid());
            $business_balance = self::getBusinessBalance($shop_id);
            return [
                'bond_balance' => $bond_balance,
                'business_balance' => $business_balance
            ];
        }
    }

    /**
     * 保存提现申请
     * @param $type
     * @param $money
     * @throws WithdrawException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function apply($type, $money)
    {
        if ($type == CommonEnum::WITHDRAW_BOND) {
            $balance = self::getBondBalance(Token::getCurrentUid());
            if ($balance < $money) {
                throw  new WithdrawException(
                    ['code' => 401,
                        'msg' => '保证金余额不足',
                        'errorCode' => 200002
                    ]
                );
            }

        } else {
            //检测余额是否充足
            $shop_id = Token::getCurrentTokenVar('shop_id');
            $balance = self::getBusinessBalance($shop_id);
            if ($balance < $money) {
                throw  new WithdrawException(
                    ['code' => 401,
                        'msg' => '余额不足',
                        'errorCode' => 200002
                    ]
                );
            }
        }


        $res = WithdrawMiniT::create([
            'money' => $money * 100,
            'u_id' => Token::getCurrentUid(),
            'type' => $type,
            'pay_id' => CommonEnum::ORDER_STATE_INIT,
            'state' => CommonEnum::STATE_IS_OK,
            'order_number' => makeOrderNo()
        ]);

        if (!$res) {
            throw  new WithdrawException();
        }
    }


    /**
     * 保存提现申请
     * @param $params
     * @throws WithdrawException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function joinApply($params)
    {
        //检查有没有未处理的申请
        if ($this->checkJoinApplying()) {
            throw  new WithdrawException(
                ['code' => 401,
                    'msg' => '有待处理提现申请，不能发起提现',
                    'errorCode' => 200008
                ]
            );
        }
        //检查余额是否充足
        if (!$this->checkJoinBalance($params['money'])) {
            throw  new WithdrawException(
                ['code' => 401,
                    'msg' => '加盟商余额不足',
                    'errorCode' => 200007
                ]
            );
        }


        $params['admin_id'] = Token::getCurrentUid();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $res = WithdrawPcT::create($params);
        if (!$res) {
            throw  new WithdrawException();
        }
    }

    private function checkJoinBalance($money)
    {
        return $this->getJoinBalance() - $money;
        //$params['admin_id'] = Token::getCurrentUid();
    }


    public function getJoinBalance()
    {
        $province = Token::getCurrentTokenVar('province');
        $city = Token::getCurrentTokenVar('city');
        $area = Token::getCurrentTokenVar('area');
        $sql = preJoinSqlForGetDShops($province, $city, $area);

        //获取余额
        $balance = JoinBalanceV::where('pay_state', 2)
            ->whereRaw($sql)->sum('join_money');

        return $balance;
    }

    /**
     * 检测是否有待处理的申请
     * @return float|string
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    private function checkJoinApplying()
    {
        $admin_id = Token::getCurrentUid();
        $count = WithdrawPcT::where('admin_id', $admin_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->count('id');
        return $count;
    }


    /**
     * 获取提现列表
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function withdraws($page, $size)
    {
        $u_id = Token::getCurrentUid();
        return WithdrawMiniT::getList($u_id, $page, $size);
    }

    /**
     * 收支明细列表
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function payments($page, $size)
    {
        $u_id = Token::getCurrentUid();
        $shop_id = Token::getCurrentTokenVar('shop_id');
        if ($shop_id) {
            return PaymentsV::getListForShop($shop_id, $u_id, $page, $size);
        } else {
            return PaymentsV::getListForNormal($u_id, $page, $size);

        }
    }

    public static function getBondBalance($u_id)
    {
        $balance = BondBalanceV::where('u_id', $u_id)
            ->sum('money');
        return $balance;

    }


    /**
     * 获取商家可提现额度
     * @param $shop_id
     * @return float
     */
    public static function getBusinessBalance($shop_id)
    {

        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));
        $consult_limit = 'date_format("' . $consult_limit . '","%Y-%m-%d %H:%i")';
        $user_confirm_limit = 'date_format("' . $user_confirm_limit . '","%Y-%m-%d %H:%i")';

        $sql = '( confirm_id = 1 ) ';
        $sql .= 'OR';
        $sql .= '(pay_id <> 99999 AND confirm_id = 99999 AND  order_time < ' . $user_confirm_limit . ') ';
        $sql .= 'OR';
        $sql .= ' ( confirm_id = 2 AND  order_time < ' . $consult_limit . ')';


        $balance = BusinessBalanceV::where('shop_id', $shop_id)
            ->whereRaw($sql)
            ->sum('money');
        return $balance;

    }

    /**
     * @param $id
     * @param $state
     * @throws WithdrawException
     * @throws \app\lib\exception\PayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \wxpay\WxPayException
     */
    public static function HandelForShop($id, $state)
    {
        if ($state == CommonEnum::DELETE) {
            $res = WithdrawPcT::update(['state' => $state], ['id' => $id]);
            if (!$res) {
                throw  new WithdrawException(
                    ['code' => 401,
                        'msg' => '删除失败',
                        'errorCode' => 200008
                    ]
                );
            }
        } else {
            (new TransferService($id))->transferToUser();
        }


    }

}