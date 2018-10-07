<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:05
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ServiceListV;
use app\api\model\ServiceV;
use app\api\model\ShopT;
use app\api\service\ShopService;
use app\api\validate\ServiceValidate;
use app\api\validate\ShopValidate;
use  app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\ShopException;
use app\lib\exception\SuccessMessage;

class Shop extends BaseController
{
    /**
     * @api {POST} /api/v1/shop/apply  6-用户发起申请开商铺
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户发起申请开商铺
     * @apiExample {post}  请求样例:
     * {
     * "name": "维修小铺",
     * "phone": "18956225230",
     * "phone_sub": "13731872800",
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "address": "石城大道",
     * "type": "1",
     * "head_url": "dadasdsadfsfdasfasd",
     * "imgs": "1,2,3",
     * "id_number": "34272792931939123",
     * }
     * @apiParam (请求参数说明) {String} name 店铺名称
     * @apiParam (请求参数说明) {String} phone 商家手机号
     * @apiParam (请求参数说明) {String} phone 备用号码
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} address 详细地址
     * @apiParam (请求参数说明) {String} type 需求类别：1 | 维修；2 | 家政
     * @apiParam (请求参数说明) {String} imgs 商家资料图片id，多个用逗号隔开
     * @apiParam (请求参数说明) {String} head_url 头像，base64
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function ShopApply()
    {
        (new ShopValidate())->scene('save')->goCheck();
        $u_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['u_id'] = $u_id;
        $params['state'] = CommonEnum::STATE_IS_OK;
        ShopService::apply($params);
        return json(new  SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/shop/handel  7-商铺申请审核
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员审核商铺申请：同意或者拒绝
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/shop/handel?id=1&state=2
     * @apiParam (请求参数说明) {int} id  申请id
     * @apiParam (请求参数说明) {int} state  申请操作：2 | 同意；3 | 拒绝
     *
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     *
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @throws ShopException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel($id, $state)
    {
        (new ShopValidate())->scene('handel')->goCheck();
        $res = ShopT::update(['state' => $state], ['id' => $id]);
        if (!$res) {
            throw new ShopException([
                ['code' => 401,
                    'msg' => '申请操作失败',
                    'errorCode' => 60002
                ]
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/shop/service/save  8-商家新增服务
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序商家新增服务
     * @apiExample {post}  请求样例:
     * {
     * "c_id": 1,
     * "name": "修电脑",
     * "area": "天河区",
     * "price": 500,
     * "unit": "次",
     * "cover": "kdkmaskdmls;,ls;,",
     * "des": "什么电脑都会修",
     * "extend": 1,
     * "imgs": "1,2,3",
     * }
     * @apiParam (请求参数说明) {int} c_id 类别id
     * @apiParam (请求参数说明) {String} name 服务名称
     * @apiParam (请求参数说明) {String} des 服务描述
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {int} price 价格
     * @apiParam (请求参数说明) {String} unit 单位
     * @apiParam (请求参数说明) {String} cover 封面图 base64
     * @apiParam (请求参数说明) {int} extend 是否推广：1 | 推广；2 | 不推广
     * @apiParam (请求参数说明) {String} imgs 图片id，多个用逗号隔开
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function addService()
    {
        (new ServiceValidate())->goCheck();
        $params = $this->request->param();
        $shop_id = TokenService::getCurrentTokenVar('shop_id');
        $params['shop_id'] = $shop_id;
        ShopService::addService($params);
        return json(new SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/bond/check  9-检测商铺保证是否充足
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  商家发布服务/接单 时检测保证金是否充足
     * @apiExample {get}
     * 请求样例: http://mengant.cn/api/v1/bond/check?money=100
     * @apiParam (返回参数说明) {int} money 价格：新增服务时为服务价格；接单时为订单金额
     * @apiSuccessExample {json} 返回样例:
     * {"need": 0}
     * @apiSuccess (返回参数说明) {int} need 需要补交保证金金额
     *
     * @param $money
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function checkBalanceForBond($money)
    {
        $res = ShopService::checkBalance($money);
        if ($res['res']) {
            return json(['need' => 0]);
        } else {
            return json(['need' => $res['money']]);
        }


    }

    /**Pay.php
     * @api {POST} /api/v1/service/booking  42-用户预约商家服务
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户预约商家服务
     * @apiExample {post}  请求样例:
     * {
     * "s_id": 1,
     * "phone": "18956225230",
     * "area": "天河区",
     * "address": 馨园小区,
     * "time_begin": "2018-10-05 09:00",
     * "time_end": "2018-10-05 10:00",
     * "remark": 我是备注,
     * }
     * @apiParam (请求参数说明) {int} s_id 服务id
     * @apiParam (请求参数说明) {String} phone 手机号
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} address 地址
     * @apiParam (请求参数说明) {String} time_begin 服务开始时间
     * @apiParam (请求参数说明) {String} time_end 服务结束时间
     * @apiParam (请求参数说明) {String} remark 备注
     * @apiSuccessExample {json} 返回样例:
     * {"id":"3","money":1000}
     * @apiSuccess (返回参数说明) {int} id 预约id
     * @apiSuccess (返回参数说明) {int} money 该服务需支付费用
     *
     * @return \think\response\Json
     * @throws ShopException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bookingService()
    {
        (new ShopValidate())->scene('booking')->goCheck();
        $params = $this->request->param();
        $res = ShopService::booking($params);
        return json($res);


    }

    /**
     * @api {GET} /api/v1/service/mini/list 48-小程序首页家政/维修模块/服务排行获取服务列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序首页家政/维修模块/服务排行获取服务列表（服务排行时获取对应的数据为10条：page=1;size=10）
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/service/mini/list?id=5&page=1&size=15&area=铜官区&type=1&c_id=1
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {String} area 用户地址位置--区
     * @apiParam (请求参数说明) {int} c_id 服务类别id，获取全部时默认为0
     * @apiParam (请求参数说明) {int} type 模块类别：1 |家政；2 |维修
     * @apiSuccessExample {json} 返回样例:
     * {"total":4,"per_page":"15","current_page":1,"last_page":1,"data":[{"id":5,"name":"修五金","cover":"http:\/\/repair.com\/static\/imgs\/CE41DE68-9E89-B6C1-E63D-57149CC54BBF.jpg","sell_money":"10000","sell_num":"1","area":"铜官区"},{"id":4,"name":"修五金","cover":"http:\/\/repair.com\/static\/imgs\/E72CCAE6-79A1-D88D-F755-48FE0DB381BC.jpg","sell_money":"0","sell_num":"0","area":"铜官区"},{"id":2,"name":"修五金","cover":"http:\/\/repair.com\/static\/imgs\/5782AD69-9B21-2B94-DCCA-6AD299AF32E1.jpg","sell_money":"0","sell_num":"0","area":"铜官区"},{"id":1,"name":"修五金","cover":"http:\/\/repair.com\/static\/imgs\/B9439BE2-857E-22D2-D058-CFE57315EEAE.jpg","sell_money":"0","sell_num":"0","area":"铜官区"}]}     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {int} sell_money 总销量
     * @apiSuccess (返回参数说明) {int} sell_num 成交额
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {String} cover 服务封面
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getServiceListForMini()
    {
        (new ShopValidate())->scene('service')->goCheck();
        $params = $this->request->param();
        return json(ServiceListV::getList($params['area'], $params['page'],
            $params['size'], $params['c_id'], $params['type']));
    }


}