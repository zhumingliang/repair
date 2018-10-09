<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 上午1:58
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CategoryT;
use app\lib\enum\CommonEnum;
use app\lib\exception\CategoryException;
use app\lib\exception\SuccessMessage;

class Category extends BaseController
{
    /**
     * @api {POST} /api/v1/category/save  28-管理员新增分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增分类
     * @apiExample {post}  请求样例:
     *    {
     *       "type": 1
     *       "name": "钢筋工瓦工"
     *       "order": 50
     *     }
     * @apiParam (请求参数说明) {int} type    分类上级：1 | 家政；2| 维修
     * @apiParam (请求参数说明) {String} name    分类名称
     * @apiParam (请求参数说明) {int} order    排序，最大值100
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws CategoryException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        \app\api\service\Token::getCurrentUid();
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $id = CategoryT::create($params);
        if (!$id) {
            throw  new CategoryException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/category/handel  29-管理员分类状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除分类
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 分类id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws CategoryException
     */
    public function handel()
    {
        $params = $this->request->param();
        $id = CategoryT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new CategoryException(['code' => 401,
                'msg' => '操作引导图状态失败',
                'errorCode' => 120002
            ]);
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/category/update  30-管理员修改分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员修改分类
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "type": 1,
     *       "name": "修改",
     *       "order": 40
     *     }
     * @apiParam (请求参数说明) {int} id    分类id
     * @apiParam (请求参数说明) {int} type    分类上级：1 | 家政；2| 维修
     * @apiParam (请求参数说明) {String} name    分类名称
     * @apiParam (请求参数说明) {String} order    排序，最大值100
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws CategoryException
     */
    public function update()
    {
        $params = $this->request->param();
        $id = CategoryT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new CategoryException(['code' => 401,
                'msg' => '修改分类失败',
                'errorCode' => 120003
            ]);

        }
        return json(new  SuccessMessage());


    }


    /**
     * @api {GET} /api/v1/category/mini/list 31-小程序获取分类列表
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription  小程序获取分类列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/category/mini/list?type=1
     * @apiParam (请求参数说明) {int} type    分类类别：1 | 家政；2 | 维修
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"name":"钢筋工瓦工"},{"id":2,"name":"跑腿服务"},{"id":3,"name":"少儿培训"}]
     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String} name 分类名称
     * @param $type
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListForMini($type)
    {
        $list = CategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->where('type', '=', $type)
            ->field('id,name')
            ->order('order desc')
            ->select();
        return json($list);
    }


    /**
     * @api {GET} /api/v1/category/cms/list 32-CMS获取分类列表
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取分类列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/category/list?page=1&size=20
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {
     * "total": 1,
     * "per_page": "15",
     * "current_page": 1,
     * "last_page": 1,
     * "data": [
     * {
     * "id": 1,
     * "name": "钢筋工瓦工",
     * "type": 1,
     * "order": 30
     * }
     * ]
     * }
     *
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String} name 分类名称
     * @apiSuccess (返回参数说明) {int} type 分类类别 1 | 家政；2 | 维修
     * @apiSuccess (返回参数说明) {int} order 排序
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getListForCMS($page, $size)
    {
        $list = CategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->field('id,name,type,order')
            ->order('order desc')
            ->paginate($size, false, ['page' => $page]);
        return json($list);
    }


    /**
     * @api {GET} /api/v1/category  33-CMS获取指定分类信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定分类信息
     * http://mengant.cn/api/v1/category?id=1
     * @apiParam (请求参数说明) {int} id  分类id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"name":"钢筋工瓦工","type":1,"order":30}
     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String} name 分类名称
     * @apiSuccess (返回参数说明) {int} type 分类类别 1 | 家政；2 | 维修
     * @apiSuccess (返回参数说明) {int} order 排序
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getTheCategory($id)
    {
        $category =  CategoryT::get($id)
            ->hidden(['create_time', 'update_time']);
        return json($category);
    }


}