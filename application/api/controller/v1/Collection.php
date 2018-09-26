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

}