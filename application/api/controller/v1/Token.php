<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/5/27
 * Time: 上午9:53
 */

namespace app\api\controller\v1;


use app\api\model\TestT;
use app\api\service\AdminToken;
use app\api\service\UserToken;
use app\api\validate\TokenGet;
use app\lib\exception\SuccessMessage;
use think\Controller;
use think\facade\Cache;
use app\api\validate\UserInfo;
use  app\api\service\UserInfo as UserInfoService;


class Token extends Controller
{
    /**
     * @api {GET} /api/v1/token/admin  3-CMS获取登陆token
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  后台用户登录
     * @apiExample {post}  请求样例:
     *    {
     *       "phone": "18956225230",
     *       "pwd": "a123456"
     *     }
     * @apiParam (请求参数说明) {String} phone    用户手机号
     * @apiParam (请求参数说明) {String} pwd   用户密码
     *
     * @apiSuccessExample {json} 返回样例:
     * {"u_id":1,"username":"管理员","token":"bde274895aa23cff9462d5db49690452"}
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {int} username 管理员名称
     * @apiSuccess (返回参数说明) {int} username 管理员名称
     * @apiSuccess (返回参数说明) {String} token 口令令牌，每次请求接口需要传入，有效期 2 hours

     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function getAdminToken()
    {
        $phone=$this->request->param('phone');
        $pwd=$this->request->param('pwd');
        (new TokenGet())->scene('pc')->goCheck();
        $at = new AdminToken($phone, $pwd);
        $token = $at->get();
        return json($token);
    }

    /**
     * @api {GET} /api/v1/token/loginOut  4-CMS退出登陆
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription CMS退出当前账号登陆。
     * @apiExample {get}  请求样例:
     * http://test.mengant.cn/api/v1/token/loginOut
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     */
    public function loginOut()
    {
        $token = \think\facade\Request::header('token');
        Cache::rm($token);
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/token/user  1-小程序端获取登录token
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  微信用户登录获取token。
     * 前端判断返回数据，如果用户信息缓存了并且grade=2、3时 跳转绑定手机页面；
     * 否则依据type值，进行下一步操作
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/token/user?code=mdksk
     * @apiParam (请求参数说明) {String} code    小程序code
     *
     * @apiSuccessExample {json} 返回样例:
     *{"token":"f4ad56e55cad93833180186f22586a08","type":1}
     * @apiSuccess (返回参数说明) {Sting} token 口令令牌，每次请求接口需要传入，有效期 2 hours
     * @apiSuccess (返回参数说明) {int} type 数据库是否缓存小程序用户信息
     * type=1时，表示已缓存
     * type=2 表示没有缓存数据，需要请求userInfo接口
     *
     * @param string $code
     * @return \think\response\Json
     * @throws ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \app\lib\exception\WeChatException
     * @throws \think\Exception
     */
    public function getUserToken($code = '')
    {
        (new TokenGet())->scene('wx')->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get();
        return json($token);

    }


}