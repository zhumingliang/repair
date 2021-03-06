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
use app\api\model\ServicesImgT;
use app\api\model\ServicesT;
use app\api\model\ShopImgT;
use app\api\model\ShopStaffImgT;
use app\api\model\ShopT;
use app\api\model\StaffV;
use app\api\service\ShopListService;
use app\api\service\ShopService;
use app\api\validate\PagingParameter;
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
     * "head_url": 1,
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
     * @apiParam (请求参数说明) {String} head_url 头像id
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
     * @api {GET} /api/v1/shop/handel  7-商铺状态操作（CMS管理员/小程序用户）
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription  管理员审核商铺申请：同意或者拒绝;管理员商铺状态操作：删除；审核通过之后，商家确认操作
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/shop/handel?id=1&state=2
     * @apiParam (请求参数说明) {int} id  申请id
     * @apiParam (请求参数说明) {int} state  管理员操作：申请操作：2 | 同意；3 | 拒绝；5 | 删除；小程序用户操作：4 | 审核通过之后，商家确认操作;
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
     * "cover": 1,
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
     * @apiParam (请求参数说明) {String} cover 封面图id
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
        $params['price'] = $params['price'] * 100;
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

    /**
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
     * @api {GET} /api/v1/service/mini/list 48-小程序首页家政/维修模块/服务排行获取服务列表/家政维修服务点击更多
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序首页家政/维修模块/服务排行获取服务列表（服务排行时获取对应的数据为10条：page=1;size=10）
     *
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/service/mini/list?&page=1&size=15&area=铜官区&type=1&c_id=1
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
        $list = ServiceListV::getList($params['area'], $params['page'],
            $params['size'], $params['c_id'], $params['type']);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/shop/info 69-小程序商家查看店铺状态并获取数据（点击商家入驻）
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  商家点击商家入驻，显示店铺申请状态和获取信息（接口返回数据如果为null，则没有申请店铺）
     *
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/info
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"type":1,"name":"修之家","address":"","province":"安徽省","city":"铜陵市","area":"铜官区","phone":"1895622530","phone_sub":"","id_number":"","head_url":"http:\/\/repair.com\/","state":1,"imgs":[{"img_id":1,"img_url":{"url":"http:\/\/repair.com\/1212"}},{"img_id":2,"img_url":{"url":"http:\/\/repair.com\/121"}}]}
     * @apiSuccess (返回参数说明) {int} id 店铺id
     * @apiSuccess (返回参数说明) {String} name 店铺名称
     * @apiSuccess (返回参数说明) {String} phone 商家手机号
     * @apiSuccess (返回参数说明) {String} phone 备用号码
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 详细地址
     * @apiSuccess (返回参数说明) {String} type 需求类别：1 | 维修；2 | 家政
     * @apiSuccess (返回参数说明) {String} imgs 商家资料图片
     * @apiSuccess (返回参数说明) {String} head_url 头像
     * @apiSuccess (返回参数说明) {int} state 店铺状态：1 | 申请中 ； 2 | 已审核; 4 | 审核通过并确认
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function shopInfo()
    {
        $info = ShopService::getShopInfo();
        return json($info);

    }

    /**
     * @api {POST} /api/v1/shop/update  74-修改店铺信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户修改店铺信息
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * "name": "维修小铺",
     * "phone": "18956225230",
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "des": "提供最好的服务",
     * "staffs": "1,2,3",
     * "head_url": 1,
     * "imgs": "商家图片",
     * }
     * @apiParam (请求参数说明) {int} id 店铺id
     * @apiParam (请求参数说明) {String} name 店铺名称
     * @apiParam (请求参数说明) {String} phone 商家手机号
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} des 商家介绍
     * @apiParam (请求参数说明) {String} staffs 商家员工头像列表：只有首次添加或者修改时才传入此参数，并且只传入修改值
     * @apiParam (请求参数说明) {String}  head_url 商家头像id
     * @apiParam (请求参数说明) {String} imgs 商家资料图片id，多个用逗号隔开
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function updateShop()
    {
        $params = $this->request->param();
        ShopService::updateShop($params);
        return json(new SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/shop/staff/examine 75-加盟商-商家人脸审核-审核操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  通过操作
     * @apiExample {get}  请求样例:
     * {
     * "id": 1
     * }
     * @apiParam (请求参数说明) {int} id  店铺和与员工头像关联id
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function examineStaff($id)
    {
        // (new ShopValidate())->scene('staff')->goCheck();
        //$params = $this->request->param();
        ShopService::examineStaff($id);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/shop/staff/delete 76-小程序删除店铺员工头像信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  删除操作
     * @apiExample {get}  请求样例:
     * {
     * "id": 69,
     * "face_token": "a104e76591417d746c754dfd37113331",
     * "city": "广州市"
     * }
     * @apiParam (请求参数说明) {int} id  店铺和与员工头像关联id
     * @apiParam (请求参数说明) {String} face_token 员工在人脸库识别标识;
     * 当获取店铺信息接口返回face_token为null时，表示还未审核通过，face_token传入为null即可
     * @apiParam (请求参数说明) {String} city 城市
     *
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function deleteStaff()
    {
        (new ShopValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        ShopService::deleteStaff($params['id'], $params['city'], $params['face_token']);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/shop/info/edit 77-小程序商家获取店铺信息-编辑
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/info/edit
     * @apiSuccessExample {json} 返回样例:
     * {"id":56,"name":"维修小家","province":"安徽省","city":"铜陵市","area":"郊区","phone":"18956225230","address":"高速","des":"哈哈","head_url":"https:\/\/mengant.cn\/static\/imgs\/20181113\/919c9eb7528c6928164e726e164d1c54.jpg","staffs":[{"id":91,"img_id":2300,"state":1,"face_token":null,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181210\/b8ff2df8d5c5381dccfe7ab6a5695612.jpg"}}],"imgs":[{"img_id":1099,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181113\/df4cb228042118490c33cdd75ff99168.jpg"}},{"img_id":1098,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181113\/f4560b9d43879a013762227988edaf57.jpg"}}]}
     * @apiSuccess (返回参数说明) {int} id 店铺id
     * @apiSuccess (返回参数说明) {String} name 店铺名称
     * @apiSuccess (返回参数说明) {String} phone 商家手机号
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} head_url 店铺头像
     * @apiSuccess (返回参数说明) {String} address 详细地址
     * @apiSuccess (返回参数说明) {int} des 店铺简介
     * @apiSuccess (返回参数说明) {String} staffs 商家员工头像图片
     * @apiSuccess (返回参数说明) {int} staffs->id 店铺与员工头像关联id
     * @apiSuccess (返回参数说明) {int} staffs->state  员工头像状态：1 | 审核中；2 | 审核通过
     * @apiSuccess (返回参数说明) {String} staffs->img_url->url  员工头像地址
     * @apiSuccess (返回参数说明) {String} imgs  店铺资料图片
     * @apiSuccess (返回参数说明) {String} imgs->img_url->url  图片地址
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function shopInfoForEdit()
    {
        $info = ShopService::getInfoForEdit();
        return json($info);

    }

    /**
     * @api {GET} /api/v1/shop/service/list 87-获取店铺我的服务列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/service/list?page=1&size=10
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":4,"per_page":"10","current_page":1,"last_page":1,"data":[{"id":5,"name":"修五金4","price":1000,"cover":"https:\/\/mengant.cn\/static\/imgs\/CE41DE68-9E89-B6C1-E63D-57149CC54BBF.jpg"},{"id":4,"name":"修五金3","price":1000,"cover":"https:\/\/mengant.cn\/static\/imgs\/E72CCAE6-79A1-D88D-F755-48FE0DB381BC.jpg"},{"id":2,"name":"修五金2","price":1000,"cover":"https:\/\/mengant.cn\/static\/imgs\/5782AD69-9B21-2B94-DCCA-6AD299AF32E1.jpg"},{"id":1,"name":"修五金1","price":1000,"cover":"https:\/\/mengant.cn\/static\/imgs\/B9439BE2-857E-22D2-D058-CFE57315EEAE.jpg"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 服务id
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {String} cover 封面
     *
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getServiceList($page = 1, $size = 10)
    {
        (new PagingParameter())->goCheck();
        $list = ServicesT::where('shop_id', TokenService::getCurrentTokenVar('shop_id'))
            ->where('state', CommonEnum::STATE_IS_OK)
            ->field('id, name,price/100 as price,cover,unit')
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/shop/service/delete  88-删除服务
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription 商家/管理员
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/shop/service/delete?id=1
     * @apiParam (请求参数说明) {int} id  服务id
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     * @throws ShopException
     * @throws \app\lib\exception\ParameterException
     */
    public function deleteService()
    {
        (new ShopValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        $res = ServicesT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new ShopException([
                ['code' => 401,
                    'msg' => '删除操作失败',
                    'errorCode' => 600019
                ]
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/shop/service/normal/list 98-用户进入商家店铺获取店铺服务列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/service/normal/list?page=1&size=1&id=1
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} id  店铺id
     * @apiSuccessExample {json} 返回样例:
     * {"total":3,"per_page":"1","current_page":1,"last_page":3,"data":[{"id":2,"shop_id":1,"name":"修五金2","cover":"https:\/\/mengant.cn\/static\/imgs\/5782AD69-9B21-2B94-DCCA-6AD299AF32E1.jpg","sell_num":"0","price":1000,"unit":"ci"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 服务id
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {String} cover 封面
     * @apiSuccess (返回参数说明) {String} unit 单位
     * @apiSuccess (返回参数说明) {int} sell_num 出售服务数量
     *
     */
    public function getServiceListForNormal()
    {
        (new  ShopValidate())->scene('list')->goCheck();
        $params = $this->request->param();
        $list = ServiceListV::where('shop_id', $params['id'])
            ->field('id,shop_id,name,cover,sell_num,price,unit')
            ->paginate($params['size'], false, ['page' => $params['page']])->toArray();
        return json($list);


    }

    /**
     * @api {GET} /api/v1/shop/info/normal 99-用户进入店铺 获取店铺信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/info/normal?id=1
     * @apiParam (请求参数说明) {int} id  店铺id
     * @apiSuccessExample {json} 返回样例:
     * {"info":{"id":1,"name":"修之家","area":"铜官区","address":"","imgs":[{"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}}]},"comment_count":1,"score":5,"collection":1:"phone_check":1}
     * @apiSuccess (返回参数说明) {int} id 店铺id
     * @apiSuccess (返回参数说明) {String} name 店铺名称
     * @apiSuccess (返回参数说明) {String} phone 商家手机号
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 详细地址
     * @apiSuccess (返回参数说明) {String} type 需求类别：1 | 维修；2 | 家政
     * @apiSuccess (返回参数说明) {String} imgs 商家资料图片
     * @apiSuccess (返回参数说明) {String} head_url 头像
     * @apiSuccess (返回参数说明) {int} comment_count 评论数
     * @apiSuccess (返回参数说明) {int} score 店铺分数
     * @apiSuccess (返回参数说明) { int} collection 是否收藏：1 是；0 | 否
     * @apiSuccess (返回参数说明) { int} phone 用户是否可以拨打电话：1 是；2 | 否
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getShopInfoForNormal()
    {
        $id = $this->request->param('id');
        $info = ShopService::getInfoForNormal($id);

        return json($info);
    }

    /**
     * @api {GET} /api/v1/shops/list/cms  108-CMS-商家管理-获取待审核列表/商家列表（管理员/加盟商）
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shops/list/cms?page=1&size=20&type=1
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {int} type  列表类别：1 | 待审核；2 | 已审核
     * @apiSuccessExample {json} 返回样例:
     * {"total":3,"per_page":"20","current_page":1,"last_page":1,"data":[{"shop_id":10,"u_id":8,"type":1,"name":"鑫鑫保姆","city":"玉树藏族自治州"},{"shop_id":8,"u_id":17,"type":2,"name":"家政测试","city":"枣庄市"},{"shop_id":1,"u_id":1,"type":1,"name":"修之家","city":"铜陵市"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int}  shop_id 店铺id
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {String} name 商铺名称
     * @apiSuccess (返回参数说明) {int} type 1 | 维修店铺；2 | 家政店铺
     * @apiSuccess (返回参数说明) {String} city 城市
     * @apiSuccess (返回参数说明) {String} area 区
     * @param int $page
     * @param int $size
     * @param string $key
     * @param int $type
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function ShopsForCMS($page = 1, $size = 20, $key = '', $type = 1)
    {
        $list = (new ShopListService())->getShops($page, $size, $type, $key);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/shop/info/cms 109-CMS-商家管理-获取店铺信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  商家列表-进入
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/info/cms?id=8
     * @apiParam (请求参数说明) {int} id  店铺id
     * @apiSuccessExample {json} 返回样例:
     * {"id":9,"u_id":19,"type":1,"name":"兄弟庆典","address":"台儿庄区兴中路劳动局北临","province":"山东省","city":"枣庄市","area":"台儿庄区","phone":"13581129678","phone_sub":"13963262516","id_number":"370405198408126088","create_time":"2018-10-24 09:40:56","head_url":"https:\/\/mengant.cn\/static\/imgs\/20181024\/0f575244d0f25d33bd642366b832206a.jpg","state":4,"des":null,"bond_balance":0,"imgs":[{"img_id":240,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181024\/accf0ed8a4a45f276a88b2a518170a90.jpg"}},{"img_id":242,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181024\/35b05e19b41e65902c57113ed4742989.jpg"}}]}
     * @apiSuccess (返回参数说明) {int} id 店铺id
     * @apiSuccess (返回参数说明) {String} name 店铺名称
     * @apiSuccess (返回参数说明) {String} phone 商家手机号
     * @apiSuccess (返回参数说明) {String} phone 备用号码
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 详细地址
     * @apiSuccess (返回参数说明) {String} bond_balance 保证金余额
     * @apiSuccess (返回参数说明) {String} type 需求类别：1 | 维修；2 | 家政
     * @apiSuccess (返回参数说明) {String} imgs 商家资料图片
     * @apiSuccess (返回参数说明) {String} head_url 头像
     * @apiSuccess (返回参数说明) {String} create_time 申请时间
     *
     * @param $id
     * @return \think\response\Json
     */
    public function shopInfoForCMS($id)
    {
        $info = ShopService::getShopInfoForCms($id);
        return json($info);

    }

    /**
     * @api {GET} /api/v1/shop/frozen  110-后台冻结商铺
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/shop/frozen?id=1&state=1
     * @apiParam (请求参数说明) {int} id  商家id
     * @apiParam (请求参数说明) {int} state   状态 1 | 开启； 2| 禁用
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @return \think\response\Json
     * @throws ShopException
     */
    public function shopFrozen($id, $state)
    {
        $res = ShopT::update(['frozen' => $state], ['id' => $id]);
        if (!$res) {
            throw new  ShopException(
                ['code' => 401,
                    'msg' => '冻结操作失败',
                    'errorCode' => 600020
                ]
            );
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/shop/service  148-CMS获取指定商品信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定商品信息
     * http://mengant.cn/api/v1/shop/service?id=8
     * @apiParam (请求参数说明) {int} id  服务id
     * @apiSuccessExample {json} 返回样例:
     * {"id":8,"shop_id":5,"c_id":2,"area":"郊区","name":"来啦","price":18,"unit":"次","state":1,"cover":"https:\/\/mengant.cn\/static\/imgs\/20181024\/46cc115becfe587229b21244c008bc74.jpg","create_time":"2018-10-24 06:20:33","update_time":"2018-10-24 06:20:33","extend":2,"des":"咻咻咻","imgs":[{"img_id":198,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181024\/18e4c9e311a14e1b9d2c1fd4b625f6a7.jpg"}}],"shop":{"id":5,"address":"铜都大道北","phone":"18956225230","shop_name":"维修小家", "city": "铜陵市"}}
     * @apiSuccess (返回参数说明) {string} address 商户地址
     * @apiSuccess (返回参数说明) {string} phone 商户电话
     * @apiSuccess (返回参数说明) {String} city 所在城市
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     * @apiSuccess (返回参数说明) {int} type 服务类型：1 | 家政服务；2 | 维修服务
     * @apiSuccess (返回参数说明) {String} des 服务描述
     * @apiSuccess (返回参数说明) {String} city 城市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {String} phone 联系方式
     * @apiSuccess (返回参数说明) {String} head_url 店铺头像
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} imgs->img_url->url 服务图片地址
     *
     * @param $id
     * @return \think\response\Json
     */
    public function getTheService($id)
    {
        $info = ServicesT::getServiceForCMS($id);
        return json($info);

    }

    /**
     * @api {GET} /api/v1/shop/staff/ready 150-加盟商-商家员工头像待审核列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription url用于点击用于查看操作显示内容
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/shop/staff/ready?page=1&size=1
     * @apiParam (请求参数说明) {int} page  页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":11,"per_page":"1","current_page":10,"last_page":11,"data":[{"staff_id":4,"city":"广州市","shop_name":"福招","shop_type":2,"address":"广东省广州市越秀区地铁1号线,地铁2号线","phone":"15876297678","url":"https:\/\/mengant.cn\/static\/imgs\/20181023\/accb5832f7fa6344c66815d39920cb9a.jpg","shop_img_id":4,"create_time":"2018-10-23 23:29:34"}]}
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} staff_id 店铺和与员工头像关联id
     * @apiSuccess (返回参数说明) {String} city 城市
     * @apiSuccess (返回参数说明) {String} shop_name 店铺名称
     * @apiSuccess (返回参数说明) {int} shop_type 店铺列别：1 | 维修服务；2| 家政服务
     * @apiSuccess (返回参数说明) {String} address 地址
     * @apiSuccess (返回参数说明) {String} phone 手机号
     * @apiSuccess (返回参数说明) {String} url 头像地址
     *
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getStaffsForJoin($page = 1, $size = 20)
    {
        $list = StaffV::getList($page, $size);

        return json($list);
    }

    /**
     * @api {POST} /api/v1/shop/staff/handel  151-加盟商-商家人脸审核-状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  加盟商-商家人脸审核-状态操：删除/拒绝
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":3
     * }
     * @apiParam (请求参数说明) {int} id 用户id
     * @apiParam (请求参数说明) {int} state  状态: 3 | 删除；4 | 拒绝
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @param $state
     * @return \think\response\Json
     * @throws ShopException
     */
    public function staffHandel($id, $state)
    {
        $res = ShopStaffImgT::update(['state' => $state], ['id' => $id]);
        if (!$res) {
            throw new  ShopException(
                ['code' => 401,
                    'msg' => '员工头像状态操作失败',
                    'errorCode' => 600023
                ]
            );
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/shop/image/handel  200-商家删除图片
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  商家删除图片
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 图片商家关联id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws ShopException
     */
    public function shopImageHandel($id)
    {
        $res = ShopImgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new  ShopException(
                ['code' => 401,
                    'msg' => '商家图片删除操作失败',
                    'errorCode' => 600023
                ]
            );
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/service/image/handel  201-商家删除服务图片
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  商家删除图片
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 图片服务关联id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws ShopException
     */
    public function serviceImageHandel($id)
    {
        $res = ServicesImgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new  ShopException(
                ['code' => 401,
                    'msg' => '服务图片删除操作失败',
                    'errorCode' => 600024
                ]
            );
        }
        return json(new SuccessMessage());

    }



    /**
     * @api {POST} /api/v1/shop/service/update  202-商家修改服务
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序商家新增服务
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * "c_id": 1,
     * "name": "修电脑",
     * "area": "天河区",
     * "price": 500,
     * "unit": "次",
     * "cover": 1,
     * "des": "什么电脑都会修",
     * "extend": 1,
     * "imgs": "1,2,3",
     * }
     * @apiParam (请求参数说明) {int} id 服务id
     * @apiParam (请求参数说明) {int} c_id 类别id
     * @apiParam (请求参数说明) {String} name 服务名称
     * @apiParam (请求参数说明) {String} des 服务描述
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {int} price 价格
     * @apiParam (请求参数说明) {String} unit 单位
     * @apiParam (请求参数说明) {String} cover 封面图id
     * @apiParam (请求参数说明) {int} extend 是否推广：1 | 推广；2 | 不推广
     * @apiParam (请求参数说明) {String} imgs 图片id/新增时传入，多个用逗号隔开
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function updateService()
    {
        //(new ServiceValidate())->goCheck();
        $params = $this->request->param();
        TokenService::getCurrentTokenVar('shop_id');
        ShopService::updateService($params);
        return json(new SuccessMessage());

    }




}