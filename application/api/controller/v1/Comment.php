<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/7
 * Time: 9:54 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\CommentService;
use app\api\validate\CommentValidate;

class Comment extends BaseController
{
    /**
     * @api {GET} /api/v1/comment/service 48-小程序获取指定服务评论
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  (推广服务/家政/维修模块点击进入)
     *
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

}