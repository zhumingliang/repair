<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/23
 * Time: 1:50 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SystemUnitT;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SystemException;

class Unit extends BaseController
{

    /**
     * @api {GET} /api/v1/units/mini 107-商家现在服务时,获取服务单位
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/units/mini
     * @apiParam (请求参数说明) {int} id  店铺id
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"name":"次"}]
     * @apiSuccess (返回参数说明) {int} id id 单位id
     * @apiSuccess (返回参数说明) {String} name 单位名称
     *
     * @return \think\response\Json
     */
    public function getUnitsForMini()
    {
        $units = SystemUnitT::getUnitsForMini();
        return json($units);
    }

    /**
     * @api {POST} /api/v1/units/save  132-新增单位
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增单位
     * @apiExample {post}  请求样例:
     *    {
     *       "name": "月"
     *     }
     * @apiParam (请求参数说明) {String} name  单位名称
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $name
     * @return \think\response\Json
     * @throws SystemException
     */
    public function save($name)
    {
        $params = [
            'name' => $name,
            'state' => CommonEnum::STATE_IS_OK
        ];
        $id = SystemUnitT::create($params);
        if ($id) {
            throw  new SystemException();
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/units/handel  133-系统设置-单位设置-状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员单位设置
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 单位id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @return \think\response\Json
     * @throws SystemException
     */
    public function handel($id)
    {
        $res = SystemUnitT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new SystemException(
                [
                    'code' => 401,
                    'msg' => '删除单位失败',
                    'errorCode' => 14016
                ]
            );

        }
        return json(new  SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/units/list/cms 134-系统设置-单位设置-列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取单位设置列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/units/list/cms?page=1&size=20
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {
     * "total": 1,
     * "per_page": "15",
     * "current_page": 1,
     * "last_page": 1,
     * "data":[{"id":1"name":"月"}]
     * }
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 单位id
     * @apiSuccess (返回参数说明) {String} name 单位名称
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getUnitsForCMS($page=1,$size=20)
    {
        $list = SystemUnitT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->hidden(['state,create_time,update_time'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return json($list);

    }


}