<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/2
 * Time: 4:12 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AuthGroup;
use app\api\model\AuthRule;
use app\api\service\AuthService;
use app\lib\enum\CommonEnum;
use app\lib\exception\AuthException;
use app\lib\exception\SuccessMessage;

class Auth extends BaseController
{
    /**
     * @api {POST} /api/v1/auth/group/save  172-权限管理-新增分组
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员-权限管理-新增分组
     * @apiExample {post}  请求样例:
     * {
     * "title": "业务组",
     * "description": "业务组的操作权限"
     * }
     * @apiParam (请求参数说明) {String} title 分组名称
     * @apiParam (请求参数说明) {String} description  描述名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $title
     * @param $description
     * @return \think\response\Json
     * @throws AuthException
     */
    public function addGroup($title, $description)
    {
        $res = AuthGroup::create([
            'title' => $title,
            'description' => $description
        ]);
        if (!$res->id) {
            throw  new AuthException();
        }
        return json(new SuccessMessage());
    }


    /**
     * 173-权限管理-获取分组列表
     * @return \think\response\Json
     */
    public function groups()
    {
        $list = AuthGroup::where('state', '<', CommonEnum::DELETE);
        return json($list);

    }

    /**
     * 174-权限管理-分组状态操作
     * @param $id
     * @param $status
     * @return \think\response\Json
     * @throws AuthException
     */
    public function groupHandel($id, $status)
    {
        $res = AuthGroup::update(['status' => $status], ['id' => $id]);
        if (!$res) {
            throw  new AuthException([
                'code' => 401,
                'msg' => '操作分组状态失败',
                'errorCode' => 250002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * 175-权限管理-访问授权
     * @return \think\response\Json
     */
    public function authRules()
    {
        /*  //$list=
          $a = [
              '反馈未查看',
              '反馈已查看列表'
          ];
          $list[] = array();
          foreach ($a as $k => $v) {
              $list[] = [
                  'title' => $v,
                  'name' => $v,
                  'state' => 1,
                  'parent_id' =>61,
                  'type' => 3,
              ];
          }
          $au = new AuthRule();
          $au->saveAll($list);
          return json(new SuccessMessage());*/
        /*      $url=[
                  '../Commercial/Service.html',
                  '../Commercial/Serviced.html',
                  '../Commercial/Store.html',
                  '../Commercial/Stored.html',
                  '../Commercial/HouseKeepingMaintain.html',
                  '../Setting/HomePageBanner.html',
                  '../Setting/BootPage.html',
                  '../Setting/AllianceBanner.html',
                  '../Setting/BootPageDisplayType.html',
                  '../Setting/TermService.html',
                  '../Setting/AboutUs.html',
                  '../Setting/Guide.html',
                  '../Setting/BusinessAgreement.html',
                  '../Setting/DemandHall.html',
                  '../Setting/Invoice.html',
                  '../Setting/RedPackage.html',
                  '../Setting/RedPackageStrategy.html',
                  '../Setting/CircleClassly.html',
                  '../Setting/CircleUnVerify.html',
                  '../Setting/CircleVerifySetting.html',
                  '../Setting/Circle.html',
                  '../Setting/Message.html',
                  '../Setting/Classify.html',
                  '../Setting/OrderTime.html',
                  '../Setting/AllianceCommission.html',
                  '../Setting/Phone.html',
                  '../Setting/StoreLevel.html',
                  '../Setting/PriceGuide.html',
                  '../Setting/Sensitive.html',
                  '../Setting/Unit.html',
                  '../Order/CityOrder.html',
                  '../Order/PlatFormOrder.html',
                  '../User/UserInformation.html',
                  '../User/Authority.html',
                  '../User/UserBehavior.html',
                  '../User/Alliance.html',
                  '../User/AllianceWithdrow.html',
                  '../User/AllianceWithdrowFinish.html',
                  '../User/AreaUser.html',
                  '../User/AllianceWithdrow.html',
                  '../User/AllianceWithdrowFinish.html',
                  '../User/FeedBack.html',
                  '../User/FeedBacked.html'

                  ];

              $list = AuthRule::where('type', 3)->select()->toArray();

              foreach ($list as $k => $v) {
                  AuthRule::update(['condition'=>$url[$k]],['id'=>$v['id']]);
              }*/
        /*    $data = [
            'title' => '服务优惠设置',
            'name' => '服务优惠设置',
            'condition' => '../Setting/Circle.html',
            'state' => 1,
            'parent_id' => 74,
            'type' => 2,
        ];

        AuthRule::create($data);
        return json(new SuccessMessage());*/

        $rules = (new AuthService())->authRules();
        return json($rules);

    }

    /**
     * @param $id
     * @param $rules
     * @return \think\response\Json
     * @throws AuthException
     */
    public function groupRuleSave($id, $rules)
    {
        $res = AuthGroup::update(['rules' => $rules], ['id' => $id]);
        if (!$res) {
            throw  new AuthException([
                'code' => 401,
                'msg' => '分组授权失败',
                'errorCode' => 250004
            ]);
        }
        return json(new SuccessMessage());

    }


}