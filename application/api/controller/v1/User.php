<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 上午9:40
 */

namespace app\api\controller\v1;

use app\api\model\ImgT;
use app\api\model\UserT;
use app\api\model\UserV;
use app\api\service\ImageService;
use app\api\service\UserService;
use app\api\validate\UserInfo;

use app\api\controller\BaseController;
use  app\api\service\UserInfo as UserInfoService;
use app\lib\exception\SuccessMessage;
use \app\api\service\Token as TokenService;
use app\lib\exception\UserInfoException;

class User extends BaseController
{
    /**
     * @api {POST} /api/v1/user/info 2-小程序用户信息获取并解密和存储
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  后台用户登录
     * @apiExample {post}  请求样例:
     *    {
     *       "iv": "wx4f4bc4dec97d474b",
     *       "encryptedData": "CiyLU1Aw2Kjvrj"
     *     }
     * @apiParam (请求参数说明) {String} iv    加密算法的初始向量
     * @apiParam (请求参数说明) {String} encryptedData   包括敏感数据在内的完整用户信息的加密数据
     *
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @param $iv
     * @param $encryptedData
     * @return \think\response\Json
     * @throws ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \app\lib\exception\WeChatException
     * @throws \think\Exception
     */
    public function userInfo($iv, $encryptedData)
    {
        (new UserInfo())->scene('encrypted')->goCheck();
        $user_info = new UserInfoService($iv, $encryptedData);
        $user_info->saveUserInfo();
        return json(new SuccessMessage());
        /*
                $appid = 'wxd11ca80b97562519';
                $sessionKey = 'q8E0dSZWz9oFfCFZwlISyA==';

                $encryptedData="MNxlGRub5DEw+scX/2NiOL906tAZhkAcqGL8JeXJJkkBqAAgG/1tD2wmukeAxOa5FaeJiMFgquVOXQ/2RrpJHxR7iQ5Z808e7q1MbwlxuCAoqqvnD9XQkAiaVk9Ozf/x/MwlGo5ssJbBgwAEnVcK9XtP/hOibWHCAF+4WgUAkHH1mynjJnFHJYY/bC0uIS3kIFz/xiuA46HScGCHqEXmpSRxh1QVcJgRcXIS8Tiz4p1oCXSGVxUrCfl+Bfk09dHUH8ev3FB/tCpiFOngwr7gZshSvtGbTDbvzesCaMoTBLepIWN4/ZLx4zDu9ntAT7uoatYAJzt1YkS9sRIfUzNMPFHloj2BBFC6yj7alv3iIQXszukf9e6gVQ2gR3fNrqGXHHgJJfd3Pz54dAQWkDjgo7eEWsoqXPF0ONuy3Te+Q7zTk6L0JGmmohN7ijwkixYzQRiEU0CkvUIF01vu9lbgwpWdzLh9iTvg0ATMIJf5Tgc=
        ";

                $iv = 'g2n/InvQRsZnml2KAFTVGA==';

                $pc = new \wxmsg\WXBizDataCrypt($appid, $sessionKey);
                $errCode = $pc->decryptData($encryptedData, $iv, $data );
                echo $errCode;*/
    }

    /**
     * @api {POST} /api/v1/user/update  19-用户信息编辑
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户编辑自己的信息,修改了的字段才传入。
     * @apiExample {POST}  请求样例:
     * {
     * "avatarUrl": adadsasdvadvadf,
     * "nickName": "朱明良",
     * "phone": "18956225230",
     * "address": 广州市天河区,
     * }
     * @apiParam (请求参数说明) {String} avatarUrl  用户头像id
     * @apiParam (请求参数说明) {String} nickName    用户昵称
     * @apiParam (请求参数说明) {String} phone    联系方式
     * @apiParam (请求参数说明) {String} address    所在地
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @return \think\response\Json
     * @throws UserInfoException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function infoUpdate()
    {
        $u_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        if (key_exists('avatarUrl', $params)) {
            $avatarUrl = config('setting.img_prefix') . ImageService::getImageUrl($params['avatarUrl']);
            $params['avatarUrl'] = $avatarUrl;
        }
        $res = UserT::update($params, ['id' => $u_id]);
        if (!$res) {
            throw new UserInfoException(
                [
                    ['code' => 401,
                        'msg' => '用户信息修改失败',
                        'errorCode' => 30003
                    ]
                ]
            );

        }
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/user/list 148-管理员-用户信息列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员反馈管理列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/user/list?page=1&size=20&key=''
     * @apiParam (请求参数说明) {int} page  当前页码
     * @apiParam (请求参数说明) {int} size  每页条数
     * @apiParam (请求参数说明) {String} key  关键字
     * @apiSuccessExample {json} 返回样例:
     * {"total":16,"per_page":"1","current_page":10,"last_page":16,"data":[{"id":15,"openid":"o3jy05CFV4WWXfylU_EYhF3st61g","nickName":"Hey","login_count":0,"update_time":"2018-10-23 13:33:49"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 用户id
     * @apiSuccess (返回参数说明) {String} openid 帐户名
     * @apiSuccess (返回参数说明) {String} nickName  昵称
     * @apiSuccess (返回参数说明) {int} login_count 登录次数
     * @apiSuccess (返回参数说明) {String} update_time 最后登录时间
     * @apiSuccess (返回参数说明) {int} type 用户类别 ：1 | 平台用户；2 | 小程序用户
     * @apiSuccess (返回参数说明) {String} phone 用户手机号
     * @param int $page
     * @param int $size
     * @param string $key
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getUsers($page = 1, $size = 20, $key = '')
    {

        $list = UserV::field('id,openid,nickName,login_count,update_time,state,phone,type')
            ->where('state', '<', 3)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->where('id|nickName', 'like', '%' . $key . '%');
                }
            })
            ->paginate($size, false, ['page' => $page]);
        return json($list);

    }

    /**
     * @api {POST} /api/v1/user/bind  342-小程序用户绑定邀请码
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户绑定邀请码
     * @apiExample {POST}  请求样例:
     * {
     * "code": "dadkfa",
     * }
     * @apiParam (请求参数说明) {String} code  邀请码
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $code
     * @return \think\response\Json
     * @throws UserInfoException
     */
    public function bind($code)
    {
        (new UserService())->bindCode($code);
        return json(new SuccessMessage());


    }

}