<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/27
 * Time: 10:53 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\JoinCommissionT;
use app\api\validate\JoinCommissionValidate;
use app\api\validate\PagingParameter;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SystemException;

class JoinCommission extends BaseController
{
    /**
     * @api {POST} /api/v1/system/join/save  129-系统设置-加盟商佣金设置-新增
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增加盟商佣金设置
     * @apiExample {post}  请求样例:
     *    {
     *       "province": "安徽省"
     *       "city": "铜陵市"
     *       "区": "铜官区"
     *       "discount": 20
     *     }
     * @apiParam (请求参数说明) {String} province    省
     * @apiParam (请求参数说明) {String} city    市
     * @apiParam (请求参数说明) {String} area    区
     * @apiParam (请求参数说明) {int} discount  佣金
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws SystemException
     * @throws \app\lib\exception\ParameterException
     */
    public function save()
    {
        (new JoinCommissionValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        //检查是否已经新增该区域设置
        $count = JoinCommissionT::where('province', $params['province'])
            ->where('city', $params['city'])
            ->where('area', $params['area'])
            ->where('state', CommonEnum::STATE_IS_OK)
            ->count('id');
        if ($count){
            throw new SystemException(
                [
                    'code' => 401,
                    'msg' => '该城市已经添加佣金设置',
                    'errorCode' => 140015
                ]
            );
        }

        $res = JoinCommissionT::create($params);
        if (!$res->id) {
            throw new SystemException(
                [
                    'code' => 401,
                    'msg' => '新增加盟商佣金设置失败',
                    'errorCode' => 140013
                ]
            );

        }
        return json(new  SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/system/join/handel  130-系统设置-加盟商佣金设置-状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除加盟商佣金设置
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 城市佣金id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws SystemException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel()
    {
        (new JoinCommissionValidate())->scene('handel')->goCheck();
        $id = $this->request->param('id');
        $res = JoinCommissionT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new SystemException(
                [
                    'code' => 401,
                    'msg' => '删除加盟商佣金设置失败',
                    'errorCode' => 140014
                ]
            );

        }
        return json(new  SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/system/join/list 131-系统设置-加盟商佣金设置-列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取加盟商佣金设置列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/system/join/list?page=1&size=20
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {
     * "total": 1,
     * "per_page": "15",
     * "current_page": 1,
     * "last_page": 1,
     * "data":[{"id":1"province":"安徽省","city":"铜陵市","area":"铜官区","discount":20}]
     * }
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 佣金id
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} discount 佣金
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        (new PagingParameter())->goCheck();
        $page = $this->request->param('page');
        $size = $this->request->param('size');
        $list = JoinCommissionT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->hidden(['state,create_time,update_time'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return json($list);

    }

}