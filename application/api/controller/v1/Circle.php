<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/8
 * Time: 10:17 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CircleCategoryT;
use app\api\model\CircleExamineT;
use app\api\service\CircleService;
use app\api\validate\CircleValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\CircleException;
use app\lib\exception\SuccessMessage;


class Circle extends BaseController
{

    /**
     * @api {POST} /api/v1/circle/category/save  50-CMS新增圈子类别
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增圈子分类
     * @apiExample {post}  请求样例:
     * {
     * "province": "广东省",
     * "city": "广州市",
     * "area": "天河区",
     * "name": "失物招领"
     * }
     * @apiParam (请求参数说明) {String} province 省
     * @apiParam (请求参数说明) {String} city 市
     * @apiParam (请求参数说明) {String} area 区
     * @apiParam (请求参数说明) {String} name 分类名称
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return SuccessMessage
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function saveCategory()
    {
        (new CircleValidate())->scene('category_save')->goCheck();
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $id = CircleCategoryT::create($params);
        if (!$id) {
            throw  new CircleException();
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/circle/category/handel  51-圈子类别状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除圈子类别
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 类别id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws \app\lib\exception\ParameterException
     * @throws CircleException
     */
    public function categoryHandel()
    {
        (new CircleValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        $id = CircleCategoryT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '操作圈子类别状态失败',
                'errorCode' => 160002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/circle/cms/category/list 52-CMS获取圈子类别列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取圈子类别列表（管理员-圈子分类列表/加盟商-新增圈子时获取分类列表）
     *
     * @apiExample {get}  管理员-圈子分类列表,请求样例:
     * http://mengant.cn/api/v1/circle/category/list?page=1&size=20
     * @apiExample {get}  加盟商-新增圈子时获取分类列表,请求样例:
     * http://mengant.cn/api/v1/circle/category/list
     * @apiSuccessExample {json} 管理员-圈子分类列表,返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"province":"安徽省","city":"铜陵市","area":"铜官区","name":"失物招领","create_time":"-0001-11-30 00:00:00"}]}
     * @apiSuccessExample {json} 加盟商-新增圈子时获取分类列表,返回样例:
     * [{"id":1,"name":"失物招领"}]
     *
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 类别id
     * @apiSuccess (返回参数说明) {int} name  类别名称
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     *
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryListForCms()
    {
        $params = $this->request->param();
        $list = CircleService::getCategoryListForCms($params);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/circle/mini/category/list 55-获取圈子类别列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  小程序获取圈子类别列表（圈子模块）
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/category/list?province=安徽省&city=铜陵市&area=铜官区
     * @apiParam (请求参数说明) {int}  province 用户地理位置-省
     * @apiParam (请求参数说明) {int}  city 用户地理位置-市
     * @apiParam (请求参数说明) {int}  area 用户地理位置-区
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"name":"失物招领"}]
     * @apiSuccess (返回参数说明) {int} id 类别id
     * @apiSuccess (返回参数说明) {int} name  类别名称
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryListForMini()
    {

        $params = $this->request->param();
        $list = CircleService::getCategoryListForMini($params);
        return json($list);


    }

    /**
     * @api {POST} /api/v1/circle/pass/set  53-修改圈子审核设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改圈子审核设置
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *       "type": 1
     *     }
     * @apiParam (请求参数说明) {int} id    设置id
     * @apiParam (请求参数说明) {int} type  设置类别：1 | 默认通过；2 | 默认需要审核
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws CircleException
     * @throws \app\lib\exception\ParameterException
     */
    public function circlePassSet()
    {
        (new CircleValidate())->scene('set')->goCheck();
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $id = CircleExamineT::update(['default' => $type], ['id' => $id]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '操作圈子类别状态失败',
                'errorCode' => 160002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/circle/pass/get 54-获取圈子审核设置
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取圈子审核设置
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/pass/get
     * @apiSuccessExample {json} 返回样例:
     * {"id":2,"default":1,"create_time":"2018-10-08 23:45:14","update_time":"2018-10-08 23:45:14"}
     * @apiSuccess (返回参数说明) {int} id 设置id
     * @apiSuccess (返回参数说明) {int} default  设置类别：1 | 默认通过；2 | 默认需要审核
     *
     * @return \think\response\Json
     * @throws CircleException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCirclePassSet()
    {
        $examine = new CircleExamineT();
        $obj = $examine->find();
        if (!$obj) {
            $examine->default = 1;
            $obj = $examine->save();
            if (!$obj) {
                throw new CircleException(['code' => 401,
                    'msg' => '新增圈子默认设置失败',
                    'errorCode' => 160003
                ]);
            }
            return json($examine);
        }

        return json($obj);

    }


}