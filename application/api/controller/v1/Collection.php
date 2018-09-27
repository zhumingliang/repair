<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:25
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\CollectionService;
use app\api\validate\CollectionValidate;
use app\lib\exception\SuccessMessage;

class Collection extends BaseController
{
    /**
     * @api {POST} /api/v1/collection/save  10-用户收藏服务/店铺
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户收藏服务/店铺
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "type":1
     * }
     * @apiParam (请求参数说明) {String} id  服务/店铺 id
     * @apiParam (请求参数说明) {String} type   收藏类别：1 服务；2| 店铺
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @param $id
     * @param $type
     * @return \think\response\Json
     * @throws \app\lib\exception\CollectionException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save($id, $type)
    {
        (new CollectionValidate())->scene('save')->goCheck();
        CollectionService::save($id, $type);
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/collection/handel  11-用户取消收藏服务/店铺
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户取消收藏服务/店铺
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "type":1
     * }
     * @apiParam (请求参数说明) {String} id  收藏 id
     * @apiParam (请求参数说明) {String} type   收藏类别：1 服务；2| 店铺
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @param $id
     * @param $type
     * @return \think\response\Json
     * @throws \app\lib\exception\CollectionException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel($id, $type)
    {
        (new CollectionValidate())->scene('save')->goCheck();
        TokenService::getCurrentUid();
        CollectionService::handel($id, $type);
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/collection/list 12-用户获取收藏列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户获取收藏列表（服务/店铺）
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/collection/list?page=1&size=10&type=1
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} type 收藏类别：1 | 服务，2 | 店铺
     * @apiSuccessExample {json} 获取服务收藏列表返回样例:
     * {
     * "total": 1,
     * "per_page": "15",
     * "current_page": 1,
     * "last_page": 1,
     * "data": [
     * {
     * "id": 1,
     * "s_id": 4,
     * "service": {
     * "id": 4,
     * "cover": "static/imgs/E72CCAE6-79A1-D88D-F755-48FE0DB381BC.jpg",
     * "name": "修五金",
     * "price": 1000
     * }
     * }
     * ]
     * }
     *
     *
     * @apiSuccessExample {json} 获取店铺收藏列表返回样例:
     * {
     * "total": 1,
     * "per_page": "15",
     * "current_page": 1,
     * "last_page": 1,
     * "data": [
     * {
     * "id": 1,
     * "s_id": 1,
     * "shop": {
     * "id": 1,
     * "head_url": "",
     * "name": "修之家",
     * "address": "",
     * "phone": "1895622530"
     * }
     * }
     * ]
     * }
     *
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 收藏列表id
     * @apiSuccess (返回参数说明) {int} s_id 服务id/店铺id
     * @apiSuccess (返回参数说明) {obj} service 服务对象
     * @apiSuccess (返回参数说明) {int} id 服务id
     * @apiSuccess (返回参数说明) {String} cover 服务介绍图片
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {obj} shop 店铺对象
     * @apiSuccess (返回参数说明) {int} id 店铺id
     * @apiSuccess (返回参数说明) {String} head_url 店铺介绍图片
     * @apiSuccess (返回参数说明) {String} name 店铺名称
     * @apiSuccess (返回参数说明) {String} address 店铺地址
     * @apiSuccess (返回参数说明) {String} phone 店铺联系方式
     *
     *
     * @param $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($type, $page = 1, $size = 15)
    {
        $list = CollectionService::getList($type, $page, $size);
        return json($list);


    }

}