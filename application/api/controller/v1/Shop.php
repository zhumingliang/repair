<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:05
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
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
     * @apiParam (返回参数说明) {String} name 店铺名称
     * @apiParam (返回参数说明) {String} phone 商家手机号
     * @apiParam (返回参数说明) {String} phone 备用号码
     * @apiParam (返回参数说明) {String} province 省
     * @apiParam (返回参数说明) {String} city 市
     * @apiParam (返回参数说明) {String} area 区
     * @apiParam (返回参数说明) {String} address 详细地址
     * @apiParam (返回参数说明) {String} type 需求类别：1 | 维修；2 | 家政
     * @apiParam (返回参数说明) {String} imgs 商家资料图片id，多个用逗号隔开
     * @apiParam (返回参数说明) {String} head_url 头像，base64
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
     * @api {POST} /api/v1/demand/save  8-商家新增服务
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
     * @apiParam (返回参数说明) {int} c_id 类别id
     * @apiParam (返回参数说明) {String} name 服务名称
     * @apiParam (返回参数说明) {String} des 服务描述
     * @apiParam (返回参数说明) {String} area 区
     * @apiParam (返回参数说明) {int} price 价格
     * @apiParam (返回参数说明) {String} unit 单位
     * @apiParam (返回参数说明) {String} cover 封面图 base64
     * @apiParam (返回参数说明) {int} extend 是否推广：1 | 推广；2 | 不推广
     * @apiParam (返回参数说明) {String} imgs 图片id，多个用逗号隔开
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




}