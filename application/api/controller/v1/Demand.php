<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午8:40
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\DemandT;
use app\api\service\DemandService;
use  app\api\service\Token as TokenService;
use app\api\validate\DemandValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\DemandException;
use app\lib\exception\SuccessMessage;

class Demand extends BaseController

{
    /**
     * @api {POST} /api/v1/demand/save  5-用户新增需求
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户新增需求
     * @apiExample {post}  请求样例:
     * {
     * "name": "朱明良",
     * "phone": "18956225230",
     * "des": "修马桶",
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "address": "石城大道",
     * "longitude": "1298281.12131",
     * "latitude": "21312.1212",
     * "time_begin": "23:02:40",
     * "time_end": "23:02:43",
     * "money": "10000",
     * "type": "1",
     * "imgs": "1,2,3",
     * }
     * @apiParam (请求参数说明) {String} name 发布人
     * @apiParam (请求参数说明) {String} phone 联系方式
     * @apiParam (请求参数说明) {String} des 需求描述
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} address 详细地址
     * @apiParam (请求参数说明) {String} longitude 经度
     * @apiParam (请求参数说明) {String} latitude 纬度
     * @apiParam (请求参数说明) {String} time_begin 开始时间
     * @apiParam (请求参数说明) {String} time_end 结束时间
     * @apiParam (请求参数说明) {int} money 金额，标准单位为元
     * @apiParam (请求参数说明) {String} type 需求类别：1 | 维修；2 | 家政
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
    public function save()
    {
        (new DemandValidate())->scene('save')->goCheck();
        $u_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $money = $params['money'] * 100;
        $params['u_id'] = $u_id;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['money'] = $money;
        $params['origin_money'] = $money;
        DemandService::save($params);
        return json(new  SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/demand/handel  70-小程序用户取消需求订单
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户取消需求订单
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 需求id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws DemandException
     */
    public function handel()
    {
        $params = $this->request->param();
        $id = DemandT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new DemandException(['code' => 401,
                'msg' => '操作需求状态失败',
                'errorCode' => 120002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/demand/list 71-小程序用户获取需求大厅类列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户获取需求大厅类列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/demand/list?type=1&province=安徽省&city=铜陵市&area=铜官山区&latitude=31.253411&longitude=121.518998&page=1&size=20
     * @apiParam (请求参数说明) {int} type 类别 ： 2 | 家政；1 | 维修
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} longitude 经度
     * @apiParam (请求参数说明) {String} latitude 纬度
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":3,"des":"修热水器","money":800,"latitude":"31.277117","longitude":"120.744587","distance":73.7,"area":"台儿庄区"}],"grade":1}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 需求id
     * @apiSuccess (返回参数说明) {String} des 需求描述
     * @apiSuccess (返回参数说明) {int} money 预算
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} grade 用户角色：1 | 普通小程序用户；2 | 商家
     * @apiSuccess (返回参数说明) {float} distance 需求发布地址和店铺当前位置的具体，单位：km
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getList()
    {
        (new DemandValidate())->scene('list')->goCheck();
        $params = $this->request->param();
        $list = DemandService::getList($params);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/demand 72-小程序用户指定需求信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户指定需求信息
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/demand?id=1
     * @apiParam (请求参数说明) {int} id 需求id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"name":"朱明良","phone":"18956225230","des":"修马桶","province":"安徽省","city":"铜陵市","area":"铜官山区","address":"高速","time_begin":"2018-10-01 08:00:00","time_end":"2018-10-01 12:00:00","origin_money":800,"imgs":[{"d_id":1,"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"d_id":1,"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"d_id":1,"img_id":3,"img_url":{"url":"https:\/\/mengant.cn\/12"}}]}
     * @apiSuccess (返回参数说明) {String} name 发布人
     * @apiSuccess (返回参数说明) {String} phone 联系方式
     * @apiSuccess (返回参数说明) {String} des 需求描述
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} address 详细地址
     * @apiSuccess (返回参数说明) {int} origin_money 酬金
     * @apiSuccess (返回参数说明) {String} time_begin 开始时间
     * @apiSuccess (返回参数说明) {String} time_end 结束时间
     * @apiSuccess (返回参数说明) {Obj} imgs 图片对象
     * @apiSuccess (返回参数说明) {String} url 图片地址
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTheDemand()
    {
        (new DemandValidate())->scene('handel')->goCheck();
        $id = $this->request->param('id');
        $demand = DemandT::with([
            'imgs' => function ($query) {
                $query->with(['imgUrl'])
                    ->where('state', '=', 1);
            }])
            ->where('id', $id)
            ->hidden(['create_time', 'type', 'update_time', 'u_id', 'longitude', 'state', 'latitude', 'money'])
            ->find();

        $demand['origin_money'] = $demand['origin_money'] / 100;


        return json($demand);


    }


}