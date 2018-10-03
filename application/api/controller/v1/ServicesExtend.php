<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 4:00 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\ExtendService;
use app\api\validate\ExtendValidate;
use app\lib\exception\SuccessMessage;

class ServicesExtend extends BaseController
{
    /**
     * @api {GET} /api/v1/extend/cms/list 35-CMS获取推广商品列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取推广商品列表（管理员/加盟商）
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/extend/list?page=1&size=20&type=1&keyW="家政"
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} type 推广数据类别：1 | 待审核；2 | 审核通过
     * @apiParam (请求参数说明) {String} keyW 关键字查询
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"extend_id":1,"s_id":5,"shop_name":"修之家","service_name":"修五金","type":1,"province":"安徽省","city":"铜陵市","area":"铜官区","state":1,"create_time":"2018-09-26 22:51:02"}]}
     * @apiSuccess (返回参数说明) {int} extend_id 推广记录id
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {int} type 服务上级分类：1 | 家政；2 | 维修
     * @apiSuccess (返回参数说明) {String} shop_name 商品名称
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getListForCMS()
    {
        (new ExtendValidate())->scene('list')->goCheck();
        $type = $this->request->param('type');
        $page = $this->request->param('page');
        $size = $this->request->param('size');
        $keyW = $this->request->param('keyW');
        $list = ExtendService::getList($type, $page, $size,$keyW);
        return json($list);


    }

    /**
     * @api {POST} /api/v1/category/handel  36-推广商品状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商 操作推广商品状态
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "type":2
     * }
     * @apiParam (请求参数说明) {int} id 推广记录id
     * @apiParam (请求参数说明) {int} type 操作类别：2 | 通过；3 | 拒绝；4 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function handel()
    {
        (new ExtendValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        ExtendService::handel($params['id'], $params['type']);
        return json(new  SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/extend/service  37-CMS获取指定推广商品信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定推广商品信息
     * http://mengant.cn/api/v1/extend/service?id=1
     * @apiParam (请求参数说明) {int} id  推广记录id
     * @apiSuccessExample {json} 返回样例:
     * {"extend_id":1,"s_id":5,"shop_name":"修之家","service_name":"修五金","type":1,"province":"安徽省","city":"铜陵市","address":"","area":"铜官区","phone":"1895622530","price":1000,"des":"五金大神","head_url":"http:\/\/repair.com\/","create_time":"2018-09-26 22:51:01"}
     * @apiSuccess (返回参数说明) {int} extend_id 推广记录id
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {String} shop_name 店铺名称
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     * @apiSuccess (返回参数说明) {int} type 服务类型：1 | 家政服务；2 | 维修服务
     * @apiSuccess (返回参数说明) {String} des 服务描述
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {String} phone 联系方式
     * @apiSuccess (返回参数说明) {String} head_url 店铺头像
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getTheService($id)
    {
        $service = ExtendService::getTheService($id);
        return json($service);
    }





}