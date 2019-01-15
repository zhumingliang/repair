<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:26 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\GoodsCategoryT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class GoodsCategory extends BaseController
{
    /**
     * @api {POST} /api/v1/goods/category/save  177-新增商品分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  新增商品分类
     * @apiExample {post}  请求样例:
     *    {
     *       "name": "数码"
     *     }
     * @apiParam (请求参数说明) {String} name    分类名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws OperationException
     * @throws \think\Exception
     */
    public function save()
    {
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $id = GoodsCategoryT::create($params);
        if (!$id) {
            throw  new OperationException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/goods/category/handel  178-商品分类状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除分类
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 分类id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     */
    public function handel()
    {
        $params = $this->request->param();
        $id = GoodsCategoryT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new OperationException(
                [
                    'code' => 401,
                    'msg' => '删除操作失败',
                    'errorCode' => 120002
                ]);
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/goods/category/update  179-修改商品分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改分类信息
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "name": "修改"
     *     }
     * @apiParam (请求参数说明) {int} id    分类id
     * @apiParam (请求参数说明) {String} name    分类名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     */
    public function update()
    {
        $params = $this->request->param();
        $id = GoodsCategoryT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new OperationException(
                [
                    'code' => 401,
                    'msg' => '删除操作失败',
                    'errorCode' => 120002
                ]);

        }
        return json(new  SuccessMessage());


    }


    /**
     * @api {GET} /api/v1/goods/category/list 180-获取商品分类列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取商品分类列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/category/list
     * @apiSuccessExample {json} 返回样例:
     * [
     * {
     * "id": 1,
     * "name": "数码"
     * }
     * ]
     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String} name 分类名称
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = GoodsCategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->field('id,name')
            ->order('create_time desc')
            ->select();
        return json($list);
    }


}