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
use app\api\model\PaymentsV;
use app\api\model\WithdrawMiniT;
use app\lib\enum\CommonEnum;
use app\lib\exception\WithdrawException;

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
        $shop_id = Token::getCurrentTokenVar('shop_id');
        if (!$shop_id) {
            return ['balance' => 0];
        } else {
            //余额：保证金/营业额度
            $bond_balance = self::getBondBalance();
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
            $balance = self::getBondBalance();
            if ($balance - 500 < $money) {
                throw  new WithdrawException(
                    ['code' => 401,
                        'msg' => '保证金余额需要大于500',
                        'errorCode' => 200002
                    ]
                );
            }

        }

        $res = WithdrawMiniT::create([
            'money' => $money,
            'u_id' => Token::getCurrentUid(),
            'type' => $type,
            'pay_id' => CommonEnum::ORDER_STATE_INIT,
            'state' => CommonEnum::STATE_IS_OK,
            'order_number' => makeOrderNo(),
            'openid' => Token::getCurrentOpenid()
        ]);
        if (!$res) {
            throw  new WithdrawException();
        }
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
     * @return array|\think\Paginator|void
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

    private static function getBondBalance()
    {
        $balance = BondBalanceV::where('u_id', Token::getCurrentUid())
            ->sum('money');
        return $balance;

    }


    private static function getBusinessBalance($shop_id)
    {
        $day = 7;
        $time_limit = date('Y-m-d', strtotime('-' . $day . ' day',
            time()));
        $time_limit = 'date_format("' . $time_limit . '","%Y-%m-%d")';

        $sql = '( confirm_id =2  AND   order_time < ' . $time_limit . ') ';
        $sql .= 'OR';
        $sql .= ' (confirm_id = 1)';

        $balance = BusinessBalanceV::where('shop_id', $shop_id)
            ->whereRaw($sql)
            ->sum('money');
        return $balance;

    }

}