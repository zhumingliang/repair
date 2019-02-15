<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-01-30
 * Time: 11:11
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ForumImgT;
use app\api\model\ForumT;
use app\api\service\ForumService;
use app\api\validate\ForumValidate;
use app\api\validate\PagingParameter;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Forum extends BaseController
{
    /**
     * @api {POST} /api/v1/forum/save  347-新增帖子
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户新增帖子
     * @apiExample {post}  请求样例:
     * {
     * "title": "你的睡眠真的好吗？",
     * "content": "每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？你知道吗，过短的休息时间有害身体，过长的休息也会对生命造成危害",
     * "imgs": "1,2,3"
     * }
     * @apiParam (请求参数说明) {String} title 文章标题
     * @apiParam (请求参数说明) {String} content 文章内容
     * @apiParam (请求参数说明) {String} imgs 文章图片ID
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function save()
    {
        (new ForumValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        (new ForumService())->save($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/forum/update  348-修改帖子
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序用户修改帖子
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * "title": "你的睡眠真的好吗？",
     * "content": "每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？你知道吗，过短的休息时间有害身体，过长的休息也会对生命造成危害",
     * "imgs": "1,2,3"
     * }
     * @apiParam (请求参数说明) {int} id 帖子id
     * @apiParam (请求参数说明) {String} title 文章标题
     * @apiParam (请求参数说明) {String} content 文章内容
     * @apiParam (请求参数说明) {String} imgs 文章图片ID
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function update()
    {
        (new ForumValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        (new ForumService())->update($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/forum/handel  349-帖子状态操作
     * @apiGroup  COMMON
     * @apiVersion 1.0.1
     * @apiDescription  管理员审核帖子/用户删除帖子
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "state":2
     * }
     * @apiParam (请求参数说明) {int} id  帖子id
     * @apiParam (请求参数说明) {String} state   状态类别：2 审核通过；3| 审核不通过；4|删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function handel()
    {
        (new ForumValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        $id = ForumT::update(['state' => $params['state']], ['id' => $params['id']]);
        if (!$id) {
            throw new OperationException(
                ['code' => 401,
                    'msg' => '帖子状态失败',
                    'errorCode' => 100002
                ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/forum/image/delete  350-删除帖子图片
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除帖子图片
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {String} id    帖子图片关联id
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function forumImageDelete()
    {
        (new ForumValidate())->scene('id')->goCheck();
        $params = $this->request->param();
        $res = ForumImgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '删除操作失败',
                'errorCode' => 160011
            ]);
        }

    }

    /**
     * @api {POST} /api/v1/forum/comment/save  351-新增帖子评论
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序评论帖子
     * @apiExample {post}  请求样例:
     * {
     * "parent_id": 0,
     * "content": "我是一条评论",
     * "f_id": 1
     * }
     * @apiParam (请求参数说明) {int} parent_id 评论上一级id，如果没有则 0
     * @apiParam (请求参数说明) {String} content 评论内容
     * @apiParam (请求参数说明) {int}  f_id 帖子id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"id": 1}
     * @apiSuccess (返回参数说明) {int} id 评论id
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function saveComment()
    {
        (new ForumValidate())->scene('comment_save')->goCheck();
        $params = $this->request->param();
        $id = ForumService::saveComment($params);
        return json(['id' => $id]);
    }

    /**
     * @api {GET} /api/v1/forum/cms/list 352-CMS获取帖子列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription CMS获取帖子列表(全部/未审核)
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/forum/cms/list?page=1&size=20&type=1&key=
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} type   类别：1 | 待审核；2 | 全部
     * @apiParam (请求参数说明) {int} key   关键字查询
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"nickName":"盟蚁","avatarUrl":"","name_sub":null,"phone":null,"title":"你的睡眠真的好吗？","create_time":"2019-02-13 23:26:29","state":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 帖子id
     * @apiSuccess (返回参数说明) {String} nickName  昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl  头像
     * @apiSuccess (返回参数说明) {String} name_sub  真实名称
     * @apiSuccess (返回参数说明) {String} phone  手机号
     * @apiSuccess (返回参数说明) {String} title 标题
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     * @apiSuccess (返回参数说明) {int} state 状态：1 |  待审核；2 | 审核通过；3 | 审核不通过
     * @param int $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getForumListForCMS($type, $page = 1, $size = 10, $key = '')
    {
        $list = (new ForumService())->getForumListForCMS($type, $page, $size, $key);
        return json($list);

    }


    /**
     * @api {GET} /api/v1/forum/cms 353-CMS获取指定帖子内容
     * @apiVersion 1.0.1
     * @apiDescription CMS获取指定帖子内容
     * @apiExample {get}  请求样例:
     * https://mengant.cn/api/v1/forum/cms?id=4
     * @apiParam (请求参数说明) {int} id  帖子id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"title":"你的睡眠真的好吗？","content":"每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？","create_time":"2019-02-13 23:26:29","user":{"id":1,"nickName":"盟蚁","avatarUrl":"","name_sub":null,"phone":null},"imgs":[{"id":1,"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"id":2,"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"id":3,"img_id":3,"img_url":{"url":"https:\/\/mengant.cn\/12"}}]}
     * @apiSuccess (返回参数说明) {int} id 帖子id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} content 内容
     * @apiSuccess (返回参数说明) {obj} user 用户信息
     * @apiSuccess (返回参数说明) {String} nickName  昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl  头像
     * @apiSuccess (返回参数说明) {String} name_sub  真实名称
     * @apiSuccess (返回参数说明) {String} phone  手机号
     * @apiSuccess (返回参数说明) {obj} imgs 图片
     * @apiSuccess (返回参数说明) {String} img_url->url 图片地址
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getForumForCMS()
    {
        (new ForumValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        return json(ForumT::getForumForCMS($id));

    }


    /**
     * @api {GET} /api/v1/forum/mini/list 354-小程序获取论坛帖子列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 小程序获取论坛帖子列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/forum/mini/list?page=1&size=20&type=1
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} type   列表类别：2 | 全部帖子；1 | 我的帖子
     * @apiSuccessExample {json} 全部帖子返回样例:
     * {"total":2,"per_page":10,"current_page":1,"last_page":1,"data":[{"id":2,"u_id":1,"title":"哈哈","content":"哈哈","state":2,"nickName":"盟蚁","avatarUrl":"","create_time":"2019-02-16 00:29:30","c_count":0,"comment":[]},{"id":1,"u_id":1,"title":"你的睡眠真的好吗？","content":"每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？","state":2,"nickName":"盟蚁","avatarUrl":"","create_time":"2019-02-13 23:26:29","c_count":2,"comment":{"total":2,"per_page":5,"current_page":1,"last_page":1,"data":[{"f_id":1,"id":2,"parent_id":1,"nickName":"蚂蚁二号","avatarUrl":"dasda","content":"hhh","create_time":"2019-02-15 23:42:35","parent_name":"蚂蚁一号","parent_url":"rrr","parent_content":"hh"},{"f_id":1,"id":1,"parent_id":0,"nickName":"蚂蚁一号","avatarUrl":"rrr","content":"hh","create_time":"2019-02-15 23:41:55","parent_name":null,"parent_url":null,"parent_content":null}]}}]}     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccessExample {json} 我的帖子返回样例:
     * {"total":2,"per_page":10,"current_page":1,"last_page":1,"data":[{"id":2,"u_id":1,"title":"哈哈","content":"哈哈","state":2,"nickName":"盟蚁","avatarUrl":"","create_time":"2019-02-16 00:29:30","c_count":0},{"id":1,"u_id":1,"title":"你的睡眠真的好吗？","content":"每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？","state":2,"nickName":"盟蚁","avatarUrl":"","create_time":"2019-02-13 23:26:29","c_count":2}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 帖子id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} content 内容
     * @apiSuccess (返回参数说明) {String} nickName  昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl  头像
     * @apiSuccess (返回参数说明) {String} name_sub  真实名称
     * @apiSuccess (返回参数说明) {int} c_count  评论数
     * @apiSuccess (返回参数说明) {obj} comment  评论数据:m默认返回5条数据
     * @apiSuccess (返回参数说明) {obj} comment->content  评论内容
     * @apiSuccess (返回参数说明) {obj} comment->nickName  评论者昵称
     * @apiSuccess (返回参数说明) {obj} comment->avatarUrl  评论者头像
     * @apiSuccess (返回参数说明) {obj} comment->parent_name  被评论者昵称
     * @apiSuccess (返回参数说明) {obj} comment->parent_url  被评论者头像
     * @param int $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getForumListForMINI($type = 1, $page = 1, $size = 10)
    {
        $list = (new ForumService())->getForumListForMINI($type, $page, $size);
        return json($list);

    }


    /**
     * @api {GET} /api/v1/forum/comments/mini 355-小程序获取论坛帖子评论列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 小程序获取论坛帖子列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/forum/comments/mini?page=1&size=20&f_id=1
     * @apiParam (请求参数说明) {int} page  页数
     * @apiParam (请求参数说明) {int} size   每页数据条数
     * @apiParam (请求参数说明) {int} f_id   帖子id
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":5,"current_page":1,"last_page":1,"data":[{"f_id":1,"id":2,"parent_id":1,"nickName":"蚂蚁二号","avatarUrl":"dasda","content":"hhh","create_time":"2019-02-15 23:42:35","parent_name":"蚂蚁一号","parent_url":"rrr","parent_content":"hh"},{"f_id":1,"id":1,"parent_id":0,"nickName":"蚂蚁一号","avatarUrl":"rrr","content":"hh","create_time":"2019-02-15 23:41:55","parent_name":null,"parent_url":null,"parent_content":null}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {String} content  评论内容
     * @apiSuccess (返回参数说明) {String} nickName  评论者昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl  评论者头像
     * @apiSuccess (返回参数说明) {String} parent_name  被评论者昵称
     * @apiSuccess (返回参数说明) {String} parent_url  被评论者头像
     * @param $f_id
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getCommentsForMINI($f_id, $page = 1, $size = 5)
    {

        $list = (new ForumService())->getCommentsForMINI($f_id, $page, $size);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/forum/mini 356-小程序获取指定帖子信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序获取指定帖子信息
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/circle/mini?id=4
     * @apiParam (请求参数说明) {int} id  文章id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"title":"你的睡眠真的好吗？","content":"每天睡觉，你的睡眠真的健康吗？你的睡眠时间是科学的吗？","create_time":"2019-02-13 23:26:29","comment":{"total":2,"per_page":5,"current_page":1,"last_page":1,"data":[{"f_id":1,"id":2,"parent_id":1,"nickName":"蚂蚁二号","avatarUrl":"dasda","content":"hhh","create_time":"2019-02-15 23:42:35","parent_name":"蚂蚁一号","parent_url":"rrr","parent_content":"hh"},{"f_id":1,"id":1,"parent_id":0,"nickName":"蚂蚁一号","avatarUrl":"rrr","content":"hh","create_time":"2019-02-15 23:41:55","parent_name":null,"parent_url":null,"parent_content":null}]},"user":{"id":1,"nickName":"盟蚁","avatarUrl":"","name_sub":null,"phone":null},"imgs":[{"id":1,"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"id":2,"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"id":3,"img_id":3,"img_url":{"url":"https:\/\/mengant.cn\/12"}}]}
     * @apiSuccess (返回参数说明) {int} id 帖子id
     * @apiSuccess (返回参数说明) {String} title  标题
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @apiSuccess (返回参数说明) {String} content 内容
     * @apiSuccess (返回参数说明) {obj} comment 评论信息
     * @apiSuccess (返回参数说明) {int} comment->total 数据总数
     * @apiSuccess (返回参数说明) {int} comment->per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} comment->current_page 当前页码
     * @apiSuccess (返回参数说明) {int} comment->last_page 最后页码
     * @apiSuccess (返回参数说明) {String} comment->content  评论内容
     * @apiSuccess (返回参数说明) {String} comment->nickName  评论者昵称
     * @apiSuccess (返回参数说明) {String} comment->avatarUrl  评论者头像
     * @apiSuccess (返回参数说明) {String} comment->parent_name  被评论者昵称
     * @apiSuccess (返回参数说明) {String} comment->parent_url  被评论者头像
     * @apiSuccess (返回参数说明) {obj} imgs 图片
     * @apiSuccess (返回参数说明) {String} img_url->url 图片地址
     * @apiSuccess (返回参数说明) {obj} user 用户信息
     * @apiSuccess (返回参数说明) {String} nickName  昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl  头像
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getForumForMINI()
    {
        (new ForumValidate())->scene('id')->goCheck();
        $id = $this->request->param('id');
        $forum = (new ForumService())->getForumForMINI($id);
        return json($forum);


    }


}