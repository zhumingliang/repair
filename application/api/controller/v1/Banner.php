<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/30
 * Time: 上午12:14
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\BannerT;
use app\api\service\BannerService;
use app\api\validate\BannerValidate;
use app\lib\exception\BannerException;
use app\lib\exception\SuccessMessage;

class Banner extends BaseController
{
    /**
     * @api {POST} /api/v1/banner/save  13-管理员/加盟商新增轮播图
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商新增轮播图
     * @apiExample {post}  请求样例:
     *    {
     *       "title": "一号轮播图"
     *       "des": "我是一号轮播图！"
     *       "url": "http://xxxxx"
     *       "category": 1
     *       "img":1
     *     }
     * @apiParam (请求参数说明) {String} title    标题
     * @apiParam (请求参数说明) {String} des    轮播图内容
     * @apiParam (请求参数说明) {int} category    轮播图内容：管理员上传时：1 |  平台 ，2 | 外链；加盟商上传时：3 | 家政 ； 4 | 维修
     * @apiParam (请求参数说明) {String} url    图片外链地址：当category=2时，传入此参数，其余情况无需传入
     * @apiParam (请求参数说明) {String} img    上传图片id，当category=1，3，4时，传入此参数，其余情况无需传入
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\BannerException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new BannerValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        BannerService::save($params);
        return json(new  SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/banner/handel  14-轮播图状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员删除系统轮播图/管理员审核加盟商添加轮播图/加盟商删除轮播图
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":2
     * }
     * @apiParam (请求参数说明) {int} id  轮播图id
     * @apiParam (请求参数说明) {String} state   状态类别：2 审核通过；3| 审核不通过；4|删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @throws BannerException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel()
    {
        (new BannerValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        $id = BannerT::update(['state' => $params['state']], ['id' => $params['id']]);
        if (!$id) {
            throw new BannerException(['code' => 401,
                'msg' => '操作轮播图状态失败',
                'errorCode' => 100002
            ]);
        }

    }

    /**
     * @api {POST} /api/v1/banner/update  15-管理员/加盟商修改轮播图
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商新增轮播图
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "title": "一号轮播图"
     *       "des": "我是一号轮播图！"
     *       "url": "http://xxxxx"
     *       "category": 1
     *       "img": "base64"
     *     }
     * @apiParam (请求参数说明) {String} id    轮播图id
     * @apiParam (请求参数说明) {String} title    标题
     * @apiParam (请求参数说明) {String} des    轮播图内容
     * @apiParam (请求参数说明) {int} category    轮播图内容：管理员上传时：1 |  平台 ，2 | 外链；加盟商上传时：3 | 家政 ； 4 | 维修
     * @apiParam (请求参数说明) {String} url    图片外链地址：当category=2时，传入此参数，其余情况无需传入
     * @apiParam (请求参数说明) {String} img    上传图片id，当category=1，3，4时，传入此参数，其余情况无需传入
     *
     * @return \think\response\Json
     * @throws BannerException
     * @throws \app\lib\exception\ParameterException
     */
    public function update()
    {
        (new BannerValidate())->scene('update')->goCheck();
        $params = $this->request->param();
        BannerService::update($params);
        return json(new  SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/banner/mini/list 16-小程序用户获取轮播图
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户获取轮播图（首页轮播图/家政/维修模块轮播图）
     *
     * @apiExample {get}  获取小程序首页轮播图请求样例:
     * http://mengant.cn/api/v1/banner/mini/list?type=1
     *
     * @apiExample {get}  获取小程序家政/维修模块轮播图请求样例:
     * http://mengant.cn/api/v1/banner/mini/list?province=安徽省&city=铜陵市&area=铜官区&type=2&category=4
     * @apiParam (请求参数说明) {int}  type 轮播图类别：1 | 首页轮播图；2 | 家政/维修模块轮播图
     * @apiParam (请求参数说明) {int}  province 用户地理位置-省
     * @apiParam (请求参数说明) {int}  city 用户地理位置-市
     * @apiParam (请求参数说明) {int}  area 用户地理位置-区
     * @apiParam (请求参数说明) {int}  category  轮播图类别：3 | 家政；4 维修
     * @apiSuccessExample {json} 返回样例:
     * [{"id":3,"title":"3号轮播图","des":"我是3号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/35626BD6-0641-FBF3-8076-F50A3278BD35.jpg"},{"id":2,"title":"2号轮播图","des":"我是2号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/B524F6BF-4A5A-2BC0-25C6-7D417F7210FF.jpg"},{"id":1,"title":"一号轮播图","des":"我是一号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/7CDCF0B5-A028-297D-C8A9-D10B97B8ADD6.jpg"}]
     * @apiSuccess (返回参数说明) {int} id 轮播图id
     * @apiSuccess (返回参数说明) {String} title 标题
     * @apiSuccess (返回参数说明) {String} des 描述
     * @apiSuccess (返回参数说明) {String} url 轮播图地址
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListForMini()
    {
        (new BannerValidate())->scene('list_mini')->goCheck();
        $params = $this->request->param();
        if ($params['type'] == BannerService::JOIN) {
            (new BannerValidate())->scene('list_mini_join')->goCheck();
        }
        $list = BannerService::getListForMini($params);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/banner/cms/list 17-CMS获取轮播图列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取轮播图列表（首页轮播图/家政/维修模块轮播图）
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/banner/mini/list?type=1&page=1&size=20
     * @apiParam (请求参数说明) {int}  type 轮播图类别：1 | 首页轮播图；2 | 家政/维修模块轮播图
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 获取首页轮播图列表返回样例:
     * {"total":3,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":3,"title":"3号轮播图","des":"我是3号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/35626BD6-0641-FBF3-8076-F50A3278BD35.jpg"},{"id":2,"title":"2号轮播图","des":"我是2号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/B524F6BF-4A5A-2BC0-25C6-7D417F7210FF.jpg"},{"id":1,"title":"一号轮播图","des":"我是一号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/7CDCF0B5-A028-297D-C8A9-D10B97B8ADD6.jpg"}]}
     * @apiSuccessExample {json} 获取家政/维修模块轮播图列表返回样例:
     * {"total":4,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":4,"category":3,"title":"加盟商-1号轮播图","des":"加盟商-1号轮播图","url":"http:\/\/repair.com\/static\/imgs\/DB0D0420-2E9E-EC0B-95E7-2A141A0F4747.jpg","create_time":"2018-10-02 19:06:19","province":"安徽省","city":"铜陵市","area":"铜官区","state":1},{"id":5,"category":3,"title":"加盟商-2号轮播图","des":"加盟商-2号轮播图","url":"http:\/\/repair.com\/static\/imgs\/13C0BA32-1875-9F20-7F41-70FE18F8DC90.jpg","create_time":"2018-10-02 21:32:43","province":"安徽省","city":"铜陵市","area":"铜官区","state":1},{"id":6,"category":3,"title":"加盟商-3号轮播图","des":"加盟商-3号轮播图","url":"http:\/\/repair.com\/static\/imgs\/2538A6FD-815D-E547-DEA9-E5ADCD93101A.jpg","create_time":"2018-10-02 21:32:58","province":"安徽省","city":"铜陵市","area":"铜官区","state":1},{"id":7,"category":4,"title":"3号轮播图","des":"3号轮播图","url":"http:\/\/repair.com\/static\/imgs\/781F55D1-AC49-BD27-16B4-0C02C997DEBE.jpg","create_time":"2018-10-02 21:33:42","province":"安徽省","city":"铜陵市","area":"铜官区","state":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 轮播图id
     * @apiSuccess (返回参数说明) {String} title 标题
     * @apiSuccess (返回参数说明) {String} des 描述
     * @apiSuccess (返回参数说明) {String} url 轮播图地址
     * @apiSuccess (返回参数说明) {String} url 轮播图地址
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} state 轮播图状态：1 | 待审核； 2 | 审核通过；3 | 审核不通过；
     *
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getListForCMS()
    {
        (new BannerValidate())->scene('list_mini')->goCheck();
        $params = $this->request->param();

        $list = BannerService::getListForCMS($params);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/banner  18-CMS获取指定banner信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定banner信息:首页轮博徒/家政/维修
     * @apiDescription  CMS获取指定分类信息
     * http://mengant.cn/api/v1/banner?id=1
     * @apiParam (请求参数说明) {int} id  轮播图id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"title":"一号轮播图","des":"我是一号轮播图！","url":"http:\/\/repair.com\/static\/imgs\/7CDCF0B5-A028-297D-C8A9-D10B97B8ADD6.jpg","category":1,"state":2}     * @apiSuccess (返回参数说明) {String} title    标题
     * @apiSuccess (返回参数说明) {String} des    轮播图内容
     * @apiSuccess (返回参数说明) {int} category    轮播图内容：管理员上传时：1 |  平台 ，2 | 外链；加盟商上传时：3 | 家政 ； 4 | 维修
     * @apiSuccess (返回参数说明) {String} url    图片外链地址：当category=2时，传入此参数，其余情况无需传入
     * @apiSuccess (返回参数说明) {int} state 轮播图状态：1 | 待审核； 2 | 审核通过；3 | 审核不通过；
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception\DbException
     */
    public function getTheBanner()
    {
        (new BannerValidate())->scene('update')->goCheck();
        $id = $this->request->param('id');
        $banner = BannerT::get($id)
            ->hidden(['type', 'u_id', 'create_time', 'update_time']);
        return json($banner);

    }

}