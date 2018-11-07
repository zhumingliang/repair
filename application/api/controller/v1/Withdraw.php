<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 1:43 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\BondT;
use app\api\model\JoinBalanceV;
use app\api\model\WithdrawMiniT;
use app\api\model\WithdrawMiniV;
use app\api\model\WithdrawPcT;
use app\api\service\WithDrawService;
use app\api\validate\PagingParameter;
use app\api\validate\WithdrawValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\api\service\Token as TokenService;
use app\lib\exception\WithdrawException;

class Withdraw extends BaseController
{


    /**
     * @api {POST}    93-用户/店铺提交提现申请
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "type": 1,
     *       "money": 500
     *     }
     * @apiParam (请求参数说明) {int} type   提现类别：1 | 保证金；2| 服务费用
     * @apiParam (请求参数说明) {int} money    金额
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \app\lib\exception\WithdrawException
     * @throws \think\Exception
     */
    public function apply()
    {
        (new WithdrawValidate())->scene('apply')->goCheck();
        $params = $this->request->param();
        WithDrawService::apply($params['type'], $params['money']);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/withdraw/check  92-检查用户是否有未处理的提现订单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}
     * 请求样例: https://mengant.cn/api/v1/withdraw/check?type=1
     * @apiParam (请求参数说明) {int} type   提现类别：1 | 保证金；2| 服务费用
     * @apiSuccessExample {json} 返回样例:
     * {"state":1}
     * @apiSuccess (返回参数说明) {int} state 是否有未处理的提现订单：1 | 无；2 |有
     *
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function checkWithdraw()
    {
        (new WithdrawValidate())->scene('check')->goCheck();
        $type = $this->request->param('type');
        $count = WithdrawMiniT::where('type', $type)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pay_id', CommonEnum::ORDER_STATE_INIT)
            ->where('u_id', TokenService::getCurrentUid())
            ->count();

        return json(['state' => $count + 1]);
    }

    /**
     * @api {GET} /api/v1/withdraw/balance  90-获取用户/商家可提现余额
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}
     * 请求样例: https://mengant.cn/api/v1/withdraw/balance
     * @apiSuccessExample {json} 店铺返回样例:
     * {"bond_balance":500,"business_balance":10000}
     * @apiSuccess (返回参数说明) {int} bond_balance 保证金余额
     * @apiSuccess (返回参数说明) {int} business_balance 营业额余额
     * * @apiSuccessExample {json} 用户返回样例:
     * {"balance":0}
     * @apiSuccess (返回参数说明) {int} balance 用户余额
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getBalance()
    {
        $res = WithDrawService::getBalance();
        return json($res);


    }

    /**
     * @api {GET} /api/v1/withdraw/bond/check  91-检查用户保证金本月申请提现次数
     * @apiGroup  MINI 不能超过3次
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}
     * 请求样例: https://mengant.cn/api/v1/withdraw/bond/check
     * @apiSuccessExample {json} 店铺返回样例:
     * {"state":1}
     * @apiSuccess (返回参数说明) {int} state 是否超过三次：1 | 否；2 | 是（超过三次不能发起提现申请）
     *
     * @return array
     */
    public function checkBond()
    {
        $count = BondT::where('u_id', 1)->whereTime('create_time', 'month')
            ->count();
        if ($count < 3) {
            return json(['state' => 1]);
        } else {
            return json(['state' => 2]);
        }
    }

    /**
     * @api {GET} /api/v1/withdraws 94-获取提现记录
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/withdraws?&page=1&size=15
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"money":1000,"account":"微信零钱","create_time":"2018-10-18 13:31:23","state":1,"pay_id":99999,"type":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 订单id
     * @apiSuccess (返回参数说明) {int} money 提现金额
     * @apiSuccess (返回参数说明) {String} account 到账账户
     * @apiSuccess (返回参数说明) {int} state 订单状态：1 | 正常；2 | 拒绝
     * @apiSuccess (返回参数说明) {int} pay_id 平台是否支付到账：99999 为未支付
     * @apiSuccess (返回参数说明) {int} type 提现类别：1 | 保证金；2 | 余额
     * @apiSuccess (返回参数说明) {String} create_time 提现时间
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getWithdrawList()
    {
        (new PagingParameter())->goCheck();
        $params = $this->request->param();
        $list = WithDrawService::withdraws($params['page'], $params['size']);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/payments 95-获取收支明细
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/payments?&page=1&size=15
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"order_name":"修电脑","order_time":"2018-10-16 11:26:55","money":-800}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {String} order_name 消费名称
     * @apiSuccess (返回参数说明) {String} order_time 订单时间
     * @apiSuccess (返回参数说明) {int} money 金额
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function getPayments()
    {
        (new PagingParameter())->goCheck();
        $params = $this->request->param();
        $list = WithDrawService::payments($params['page'], $params['size']);
        return json($list);

    }

    /**
     * @api {POST} /api/v1/withdraw/apply/join  155-加盟商-提交提现申请
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "card_num": "62282832323232323",
     *       "bank": "农业银行",
     *       "username": 朱明良,
     *       "phone": 18956225230,
     *       "money": 500
     *     }
     * @apiParam (请求参数说明) {string} card_num  提现卡号
     * @apiParam (请求参数说明) {string} bank  提现银行
     * @apiParam (请求参数说明) {string} username  收款人姓名
     * @apiParam (请求参数说明) {string} phone  联系电话
     * @apiParam (请求参数说明) {string} money  金额
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \app\lib\exception\WithdrawException
     * @throws \think\Exception
     */
    public function joinApply()
    {
        (new WithdrawValidate())->scene('apply_join')->goCheck();
        $params = $this->request->param();
        (new WithDrawService())->joinApply($params);
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/withdraw/balance/join  153-加盟商-资金信息-列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/withdraw/balance/join?&page=1&size=15
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":4,"per_page":"1","current_page":1,"last_page":4,"data":[{"order_time":"2018-10-31 22:58:56","join_money":-500,"des":"提现-待审核"}],"balance":0}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} balance 用户余额
     * @apiSuccess (返回参数说明) {int} join_money 金额
     * @apiSuccess (返回参数说明) {String} des 说明
     * @apiSuccess (返回参数说明) {String} order_time 提现时间
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getBalanceList($page, $size)
    {

        $province = TokenService::getCurrentTokenVar('province');
        $city = TokenService::getCurrentTokenVar('city');
        $area = TokenService::getCurrentTokenVar('area');
        $sql = preJoinSqlForGetDShops($province, $city, $area);

        //获取余额
        $balance = JoinBalanceV::where('pay_state', 2)
            ->whereRaw($sql)->sum('join_money');

        $list = JoinBalanceV::order('order_time desc')
            ->field('order_time,join_money,des')
            ->whereRaw($sql)
            ->paginate($size, false, ['page' => $page])->toArray();
        $list['balance'] = $balance;
        return json($list);


    }

    /**
     * @api {GET} /api/v1/withdraws/join  165-加盟商管理-提现列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 查看操作-就用本接口返回的数据
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/shop/join?&page=1&size=15&state=1
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} state 列别类别：1 | 待处理；2 | 已完成
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"10","current_page":1,"last_page":1,"data":[{"id":36,"admin_id":2,"money":500,"state":1,"create_time":"2018-10-31 22:58:56","update_time":"2018-10-31 22:58:56","card_num":"62282832323232323","bank":"农业银行","username":"朱明良","phone":"13111111111","admin":{"id":2,"phone":"13711111111"}}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 提现申请id
     * @apiSuccess (返回参数说明) {int} admin_id 加盟商id
     * @apiSuccess (返回参数说明) {string} admin->phone 账号
     * @apiSuccess (返回参数说明) {String} bank 银行
     * @apiSuccess (返回参数说明) {String} card_num 银行卡号
     * @apiSuccess (返回参数说明) {String} username 收款人
     * @apiSuccess (返回参数说明) {String} create_time 提现时间
     * @apiSuccess (返回参数说明) {String} phone 联系电话
     * @apiSuccess (返回参数说明) {String} state 状态：1 | 等待处理；2 | 已完成
     *
     * @param $page
     * @param $size
     * @param $state
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getWithdrawsWithJoin($page, $size, $state)
    {
        $list = WithdrawPcT::where('state', $state)
            ->with(['admin' => function ($query) {
                $query->field('id,phone');
            }])
            ->paginate($size, false, ['page' => $page])->toArray();


        return json($list);


    }

    /**
     * @api {POST} /api/v1/withdraws/apply/handel/shop   166-管理员-商户提现操作（通过/拒绝）
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员审核提现审：通过|审核
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state": 2,
     * }
     * @apiParam (请求参数说明) {int} id 提现记录id
     * @apiParam (请求参数说明) {int} state 状态：2 | 通过；3 | 拒绝
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @throws WithdrawException
     * @throws \app\lib\exception\PayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \wxpay\WxPayException
     */
    public function applyHandelForShop($id, $state)
    {

        // $res = WithdrawPcT::update(['state' => $state], ['id' => $id]);
        WithDrawService::HandelForShop($id, $state);
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/withdraw/apply/handel/join    167-管理员-加盟商管理-提现申请处理（通过/删除）
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员审核提现审改/删除审核
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state": 2,
     * }
     * @apiParam (请求参数说明) {int} id 提现记录id
     * @apiParam (请求参数说明) {int} state 状态：2 | 通过；3 | 拒绝
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @throws WithdrawException
     */
    public function applyHandelForJoin($id, $state)
    {
        $res = WithdrawPcT::update(['state' => $state], ['id' => $id]);
        if (!$res) {
            throw new WithdrawException(['code' => 401,
                'msg' => '提现申请操作状态失败',
                'errorCode' => 200010
            ]);
        }

        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/withdraws/shop  168-提现管理-提现列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 查看操作-就用本接口返回的数据
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/withdraws/shop?&page=1&size=15&state=1
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} state 类别类别：1 | 待处理；2 | 已完成
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"10","current_page":1,"last_page":1,"data":[{"id":36,"admin_id":2,"money":500,"state":1,"create_time":"2018-10-31 22:58:56","update_time":"2018-10-31 22:58:56","card_num":"62282832323232323","bank":"农业银行","username":"朱明良","phone":"13111111111","admin":{"id":2,"phone":"13711111111"}}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 提现申请id
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {string} nickName 昵称
     * @apiSuccess (返回参数说明) {String} money 提现金额
     * @apiSuccess (返回参数说明) {int} type  提现类别：1 | 保证金；2 | 服务费
     * @apiSuccess (返回参数说明) {String} create_time 申请时间
     * @apiSuccess (返回参数说明) {String} state 状态：1 | 等待处理；2 | 已完成
     * @param $page
     * @param $size
     * @param $state
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getWithdrawsWithShop($page, $size, $state)
    {
        $list = WithdrawMiniV::where('state', $state)
            ->paginate($size, false, ['page' => $page])->toArray();
        return json($list);

    }
}