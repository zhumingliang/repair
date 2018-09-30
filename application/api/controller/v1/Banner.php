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
     *       "img": "base64"
     *     }
     * @apiParam (请求参数说明) {String} title    标题
     * @apiParam (请求参数说明) {String} des    轮播图内容
     * @apiParam (请求参数说明) {int} category    轮播图内容：管理员上传时：1 |  平台 ，2 | 外链；加盟商上传时：3 | 家政 ； 4 | 维修
     * @apiParam (请求参数说明) {String} url    图片外链地址：当category=2时，传入此参数，其余情况无需传入
     * @apiParam (请求参数说明) {String} img    上传图片base64，当category=1，3，4时，传入此参数，其余情况无需传入
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
     * @api {POST} /api/v1/collection/handel  14-轮播图状态操作
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
     * @apiParam (请求参数说明) {String} img    上传图片base64，当category=1，3，4时，传入此参数，其余情况无需传入
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

    public function getListForMini()
    {

    }

}