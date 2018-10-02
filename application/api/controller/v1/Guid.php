<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 上午1:00
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\GuidT;
use app\lib\enum\CommonEnum;
use app\lib\exception\GuidException;
use app\lib\exception\SuccessMessage;

class Guid extends BaseController
{

    /**
     * @api {POST} /api/v1/guid/save  24-管理员新增引导图
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员新增引导图
     * @apiExample {post}  请求样例:
     *    {
     *       "url": "http://xxxxx"
     *       "img": "base64"
     *     }
     * @apiParam (请求参数说明) {String} url    图片外链地址
     * @apiParam (请求参数说明) {String} img    上传图片base64，当category=1，3，4时，传入此参数，其余情况无需传入
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws GuidException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        \app\api\service\Token::getCurrentUid();
        $params = $this->request->param();
        $params['state'] = CommonEnum::READY;
        if (isset($params['img'])) {
            $params['url'] = base64toImg($params['img']);
        }
        $id = GuidT::create($params);
        if (!$id) {
            throw  new GuidException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/guid/handel  15-引导图状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除引导图
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id  轮播图id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws GuidException
     */
    public function handel()
    {
        $params = $this->request->param();
        $id = GuidT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new GuidException(['code' => 401,
                'msg' => '操作引导图状态失败',
                'errorCode' => 110002
            ]);
        }

    }


    /**
     * @api {POST} /api/v1/guid/update  26-管理员修改引导图
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员修改引导图
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "url": "http://xxxxx",
     *       "img": "base64"
     *     }
     * @apiParam (请求参数说明) {String} id    引导图id
     * @apiParam (请求参数说明) {String} url    图片外链地址
     * @apiParam (请求参数说明) {String} img    上传图片base64
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws GuidException
     */
    public function update()
    {
        $params = $this->request->param();
        if (isset($params['img'])) {
            $params['url'] = base64toImg($params['img']);
        }

        $id = GuidT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new GuidException(['code' => 401,
                'msg' => '修改引导图失败',
                'errorCode' => 110003
            ]);

        }
        return json(new  SuccessMessage());


    }


    /**
     * @api {GET} /api/v1/guid/list 27-引导图列表
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription  小程序/CMS获取引导图列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/guid/list
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"url":"http:\/\/repair.com\/static\/imgs\/35626BD6-0641-FBF3-8076-F50A3278BD35.jpg"},{"id":2,"url":"http:\/\/repair.com\/static\/imgs\/B524F6BF-4A5A-2BC0-25C6-7D417F7210FF.jpg"},{"id":1,"url":"http:\/\/repair.com\/static\/imgs\/7CDCF0B5-A028-297D-C8A9-D10B97B8ADD6.jpg"}]
     * @apiSuccess (返回参数说明) {int} id 引导图id
     * @apiSuccess (返回参数说明) {String} url 引导图地址
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = GuidT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->select();
        return json($list);
    }


    /**
     * @api {GET} /api/v1/guid  34-CMS获取指定引导图信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定引导图信息
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/guid?id=1
     * @apiParam (请求参数说明) {int} id  分类id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"url":"http:\/\/repair.com\/static\/imgs\/35626BD6-0641-FBF3-8076-F50A3278BD35.jpg"}
     * @apiSuccess (返回参数说明) {int} id 引导图id
     * @apiSuccess (返回参数说明) {String} url 引导图地址
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getTheGuid($id)
    {
        $category = GuidT::get($id)
            ->hidden(['create_time', 'update_time,state']);
        return json($category);
    }


}