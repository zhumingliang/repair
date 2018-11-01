<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/7
 * Time: 9:54 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CommentV;
use app\api\model\OrderCommentT;
use app\api\service\CommentService;
use app\api\validate\CommentValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\CircleException;
use app\lib\exception\SuccessMessage;

class Comment extends BaseController
{
    /**
     * @api {GET} /api/v1/comment/service 48-小程序获取指定服务评论
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  (推广服务/家政/维修模块点击进入)
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/comment/service?id=5&page=1&size=15
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} id 服务id
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"15","current_page":1,"last_page":1,"data":[{"content":"这次服务很满意","create_time":"2018-10-08 00:11:56","imgs":[{"c_id":1,"img_id":1,"img_url":{"url":"http:\/\/repair.com\/1212"}},{"c_id":1,"img_id":2,"img_url":{"url":"http:\/\/repair.com\/121"}}],"user":{"id":1,"nickName":"盟蚁","avatarUrl":""}}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {String} content 评论内容
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     * @apiSuccess (返回参数说明) {Obj} imgs 评论图片对象
     * @apiSuccess (返回参数说明) {String} url 图片地址
     * @apiSuccess (返回参数说明) {Obj} user 评论用户对象
     * @apiSuccess (返回参数说明) {String} nickName 用户昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl 用户头像
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getCommentForService()
    {
        (new CommentValidate())->scene('service')->goCheck();
        $id = $this->request->param('id');
        $page = $this->request->param('page');
        $size = $this->request->param('size');
        $list = CommentService::getCommentForService($id, $page, $size);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/comment/list      158-加盟商获取评论列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  小区管理员获取列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/comment/list/list?page=1&size=20
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"1","current_page":1,"last_page":1,"data":[{"id":16,"shop_name":"修之家","create_time":"2018-10-30 07:52:38","content":"好评","province":"安徽省","city":"铜陵市","area":"铜官区"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 评论id
     * @apiSuccess (返回参数说明) {String} shop_name  店铺名称
     * @apiSuccess (返回参数说明) {String} create_time 时间
     * @apiSuccess (返回参数说明) {String} content 评论时间
     * @param $page
     * @param $size
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getCommentsForCMS($page, $size)
    {

        $province = TokenService::getCurrentTokenVar('province');
        $city = TokenService::getCurrentTokenVar('city');
        $area = TokenService::getCurrentTokenVar('area');
        $sql = preJoinSqlForGetDShops($province, $city, $area);

        $list = CommentV:: whereRaw($sql)
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/comment  159-获取指定评论内容
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/comment?id=17
     * @apiParam (请求参数说明) {int} id 评论id
     * @apiSuccessExample {json} 返回样例:
     * {"id":17,"o_id":26,"content":"不满意","state":1,"create_time":"2018-10-30 07:57:16","update_time":"2018-10-30 07:57:16","u_id":21,"s_id":11,"score_type":2,"score":3,"order_type":1,"imgs":[{"o_id":17,"img_id":342,"img_url":{"url":"https:\/\/mengant.cn\/static\/imgs\/20181030\/480a67d781d384e52f19acec606b69b7.jpg"}}],"shop":{"id":11,"name":"李福招的店铺哦"}}
     * @apiSuccess (返回参数说明) {int} id 评论id
     * @apiSuccess (返回参数说明) {String} shop->name 店铺名称
     * @apiSuccess (返回参数说明) {String} content 评论内容
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     * @apiSuccess (返回参数说明) {Obj} imgs 评论图片对象
     * @apiSuccess (返回参数说明) {String} imgs->imgUrl->url 图片地址
     */
    public function getTheComment($id)
    {
        $comment = OrderCommentT::getComment($id);
        return json($comment);

    }

    /**
     * @api {POST} /api/v1/comment/handel  160-评论状态操作-删除
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除指定评论
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 评论id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws CircleException
     */
    public function commentHandel($id)
    {
        $id = OrderCommentT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$id) {
            throw new CircleException(['code' => 401,
                'msg' => '评论状态修改失败',
                'errorCode' => 160009
            ]);
        }
        return json(new SuccessMessage());

    }


}