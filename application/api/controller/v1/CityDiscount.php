<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/4
 * Time: 1:51 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CityDiscountT;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SystemException;

class CityDiscount extends BaseController
{

    /**
     * @api {POST} /api/v1/city/discount/save  38-新增城市优惠
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增城市优惠
     * @apiExample {post}  请求样例:
     *    {
     *       "type": 1
     *       "province": "安徽省"
     *       "city": "铜陵市"
     *       "discount": 20
     *     }
     * @apiParam (请求参数说明) {int} type    优惠分类：1 | 平台统一设置；2 | 城市设置
     * @apiParam (请求参数说明) {String} province    省
     * @apiParam (请求参数说明) {String} city    市
     * @apiParam (请求参数说明) {int} discount   优惠
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws SystemException
     * @throws \think\Exception
     */
    public function save()
    {
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        //检查是否已经新增该区域设置
        $count = CityDiscountT::where('province', $params['province'])
            ->where('city', $params['city'])
            ->where('state', CommonEnum::STATE_IS_OK)
            ->count('id');
        if ($count){
            throw new SystemException(
                [
                    'code' => 401,
                    'msg' => '该城市已经添加优惠设置',
                    'errorCode' => 140015
                ]
            );
        }


        $id = CityDiscountT::create($params);
        if ($id) {
            throw  new SystemException();
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/city/discount/handel  39-城市优惠状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除城市优惠
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 城市优惠id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws SystemException
     */
    public function handel()
    {
        $params = $this->request->param();
        $id = CityDiscountT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new SystemException(['code' => 401,
                'msg' => '城市优惠状态失败',
                'errorCode' => 140002
            ]);
        }

    }

    /**
     * @api {POST} /api/v1/city/discount/update  40-管理员修改城市优惠
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员修改城市优惠
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "province": "安徽省"
     *       "city": "铜陵市"
     *       "discount": 20
     *     }
     * @apiParam (请求参数说明) {int} id    优惠id
     * @apiParam (请求参数说明) {String} province    省
     * @apiParam (请求参数说明) {String} city    市
     * @apiParam (请求参数说明) {int} discount   优惠
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws SystemException
     */
    public function update()
    {
        $params = $this->request->param();
        $id = CityDiscountT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new SystemException (['code' => 401,
                'msg' => '修改城市优惠失败',
                'errorCode' => 140003
            ]);

        }
        return json(new  SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/city/discount/list 41-获取城市优惠列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取城市优惠列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/city/discount/list?page=1&size=20
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {
     * "total": 1,
     * "per_page": "15",
     * "current_page": 1,
     * "last_page": 1,
     * "data":[{"id":1,"type":2,"province":"安徽省","city":"铜陵市","discount":20},{"id":1,"type":1,"province":"","city":"","discount":20}]
     * }
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 优惠id
     * @apiSuccess (返回参数说明) {int} type 分类：1 | 平台统一优惠；2 | 城市优惠
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {int} discount 优惠折扣
     *
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getList($page, $size)
    {
        $list = CityDiscountT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->hidden(['state,create_time,update_time'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return json($list);
    }


}