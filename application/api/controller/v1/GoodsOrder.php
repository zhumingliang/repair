<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 4:04 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\GoodsOrderCommentT;
use app\api\model\GoodsOrderT;
use app\api\service\ExpressService;
use app\api\service\GoodsOrderService;
use app\api\validate\GoodsOrderValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class GoodsOrder extends BaseController
{
    /**
     * @api {POST} /api/v1/goods/order/save  328-新增积分兑换商品订单
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增积分兑换商品订单
     * @apiExample {post}  请求样例:
     *    {
     *       "g_id": 1
     *       "score": 1000
     *       "count": 1
     *       "a_id": 1
     *     }
     * @apiParam (请求参数说明) {int} g_id   商品id
     * @apiParam (请求参数说明) {int} score   总积分
     * @apiParam (请求参数说明) {int} count   数量
     * @apiParam (请求参数说明) {int} a_id   地址id
     * @apiSuccessExample {json} 返回样例:
     * {"res":1}
     * @apiSuccess (返回参数说明) {int} res 新增结果：1 | 新增成功；0 | 积分不够
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function saveOrder()
    {
        (new GoodsOrderValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        $res = (new GoodsOrderService())->save($params);
        return json([
            'res' => $res
        ]);

    }

    /**
     * @api {POST} /api/v1/goods/order/express/update  329-修改用户订单积分规则
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增用户订单积分规则
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "express": "顺丰快递",
     *       "express_no": "sf",
     *       "express_code": "213123121"
     *     }
     * @apiParam (请求参数说明) {int} id   订单id
     * @apiParam (请求参数说明) {String} express   快递名称
     * @apiParam (请求参数说明) {String} express_no   快递别称
     * @apiParam (请求参数说明) {String} express_code   订单号
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function updateExpress()
    {
        (new GoodsOrderValidate())->scene('express_update')->goCheck();
        $params = $this->request->param();
        $params['send_time'] = date('Y-m-d H:i:s');
        $params['status'] = 2;
        $res = GoodsOrderT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 100002
            ]);

        }
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/goods/order/list/cms 330-CMS获取积分兑换订单列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 获取积分兑换订单列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/order/list/cms?type=1&page=1&size=20
     * @apiParam (请求参数说明) {int}  type 订单类别：1 | 全部；2 | 未发货；3 | 已完成
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"u_id":1,"code_number":"1111","express":"百世快递","express_code":"71519121793117","express_no":"ht","score":2000,"count":1,"phone":"18956225230","name":"朱明良","create_time":"2019-02-28 22:48:00","status":1,"comment_id":1,"goods_name":"笔记本","cover":""},{"id":2,"u_id":1,"code_number":"C116495803663369","express":"","express_code":"","express_no":"","score":10,"count":1,"phone":"18956225230","name":"朱明良","create_time":"2019-01-16 22:39:40","status":1,"comment_id":0,"goods_name":"笔记本","cover":""}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 订单id
     * @apiSuccess (返回参数说明) {String} code_number 订单号
     * @apiSuccess (返回参数说明) {String} goods_name 商品名称
     * @apiSuccess (返回参数说明) {int} count 商品数量
     * @apiSuccess (返回参数说明) {int} score 所需积分
     * @apiSuccess (返回参数说明) {String} express 快递名称
     * @apiSuccess (返回参数说明) {String} express_no 快递类别
     * @apiSuccess (返回参数说明) {String} express_code 快递单号
     * @apiSuccess (返回参数说明) {String} name 下单人姓名
     * @apiSuccess (返回参数说明) {String} phone 下单人手机号
     * @apiSuccess (返回参数说明) {String} create_time 交易时间
     * @apiSuccess (返回参数说明) {int} status 订单状态： 1 | 待发货 2 | 待收货；3 | 确认收货
     * @apiSuccess (返回参数说明) {int} comment_id 评论id：0 未评论；>0 已经评论
     * @param $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getListForCMS($type, $page = 1, $size = 10)
    {
        (new GoodsOrderValidate())->scene('list')->goCheck();
        $list = (new GoodsOrderService())->getListForCMS($type, $page, $size);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/goods/order/list/mini 331-小程序获取积分兑换订单列表
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 获取积分兑换订单列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/order/list/mini?type=1&page=1&size=20
     * @apiParam (请求参数说明) {int}  type 订单类别：1 | 全部；2 | 待发货；3 | 待收货；4 | 已完成
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"code_number":"1111","cover":"","count":1,"score":2000,"status":1,"comment_id":1,"create_time":"2019-02-28 22:48:00","express":"百世快递","express_code":"71519121793117","express_no":"ht"},{"id":2,"code_number":"C116495803663369","cover":"","count":1,"score":10,"status":1,"comment_id":0,"create_time":"2019-01-16 22:39:40","express":"","express_code":"","express_no":""}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 订单id
     * @apiSuccess (返回参数说明) {String} goods_name 商品名称
     * @apiSuccess (返回参数说明) {int} count 商品数量
     * @apiSuccess (返回参数说明) {int} score 所需积分
     * @apiSuccess (返回参数说明) {String} cover 封面图
     * @apiSuccess (返回参数说明) {String} express 快递名称
     * @apiSuccess (返回参数说明) {String} express_no 快递类别
     * @apiSuccess (返回参数说明) {String} express_code 快递单号
     * @apiSuccess (返回参数说明) {int} status 订单状态： 1 | 待发货 2 | 待收货；3 | 确认收货
     * @apiSuccess (返回参数说明) {int} comment_id 评论id：0 未评论；>0 已经评论
     * @param $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getListForMINI($type, $page = 1, $size = 10)
    {
        (new GoodsOrderValidate())->scene('list')->goCheck();
        $list = (new GoodsOrderService())->getListForMINI($type, $page, $size);
        return json($list);


    }


    /**
     * @api {POST} /api/v1/goods/order/comment  332-积分兑换订单-用户评价
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription 用户评价
     * @apiExample {post}  请求样例:
     *    {
     *       "o_id": 1,
     *       "content": "商品很好。",
     *       "type": 5
     *       "score": 5
     *       "imgs": 1,2,3
     *     }
     * @apiParam (请求参数说明) {int} o_id  订单id
     * @apiParam (请求参数说明) {int} type  评价类别：1  | 好评；2| 中评；3 | 差评
     * @apiParam (请求参数说明) {String} content  评价内容
     * @apiParam (请求参数说明) {String} score  分数：每颗星星代表一分
     * @apiParam (请求参数说明) {String} imgs  评论图片id：逗号隔开
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function comment()
    {
        (new GoodsOrderValidate())->scene('comment')->goCheck();
        $params = $this->request->param();
        (new GoodsOrderService())->saveComment($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/goods/order/comment/handel  333-积分订单评论状态操作-删除
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
     * @throws OperationException
     */
    public function commentHandel($id)
    {
        $id = GoodsOrderCommentT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$id) {
            throw new OperationException(
                [
                    'code' => 401,
                    'msg' => '评论状态修改失败',
                    'errorCode' => 160009
                ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/goods/order/info/cms 334-CMS获取积分兑换订单信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 获取积分兑换信息
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/order/info/cms?id=1
     * @apiParam (请求参数说明) {int}  id 订单id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"code_number":"1111","count":1,"score":2000,"create_time":"2019-02-28 22:48:00","express":"","express_code":"","status":1,"comment_id":1,"send_time":null,"comment":{"id":1,"o_id":1,"content":"很好","type":2,"state":1,"create_time":"2019-01-18 09:55:06","update_time":"2019-01-18 09:55:06","u_id":1,"score":3,"imgs":[{"c_id":1,"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"c_id":1,"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"c_id":1,"img_id":3,"img_url":{"url":"https:\/\/mengant.cn\/12"}}]},"address":{"id":1,"name":"朱明良","phone":"18956225230"},"goods":{"id":1,"name":"笔记本"}}
     * @apiSuccess (返回参数说明) {int} id 订单id
     * @apiSuccess (返回参数说明) {String} code_number 订单号
     * @apiSuccess (返回参数说明) {int} count 商品数量
     * @apiSuccess (返回参数说明) {int} score 所需积分
     * @apiSuccess (返回参数说明) {String} address->name 下单人姓名
     * @apiSuccess (返回参数说明) {String} address->phone 下单人手机号
     * @apiSuccess (返回参数说明) {String} goods->name 商品名称
     * @apiSuccess (返回参数说明) {String} create_time 交易时间
     * @apiSuccess (返回参数说明) {String} send_time 发货时间
     * @apiSuccess (返回参数说明) {int} status 订单状态： 1 | 待发货 2 | 待收货；3 | 确认收货
     * @apiSuccess (返回参数说明) {int} comment_id 评论id：0 未评论；>0 已经评论
     * @apiSuccess (返回参数说明) {String} comment->content 评价内容
     * @apiSuccess (返回参数说明) {String} comment->score 评价星级
     * @apiSuccess (返回参数说明) {String} comment->type 评价类别：1 好评；2 | 中评；3 | 差评
     * @apiSuccess (返回参数说明) {String} comment->imgs 图片
     * @param $id
     * @return \think\response\Json
     */
    public function getTheOrderForCMS($id)
    {
        $info = (new GoodsOrderService())->getTheOrderForCMS($id);
        return json($info);

    }

    /**
     * @api {GET} /api/v1/goods/order/info/mini 335-小程序获取积分兑换订单信息
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription小程序获取积分兑换订单信息
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/order/info/mini?id=1
     * @apiParam (请求参数说明) {int}  id 订单id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"code_number":"1111","count":1,"score":2000,"create_time":"2019-02-28 22:48:00","express":"ht","express_code":"71519121793117","status":1,"comment_id":1,"send_time":null,"express_no":"","comment":{"id":1,"o_id":1,"content":"很好","type":2,"state":1,"create_time":"2019-01-18 09:55:06","update_time":"2019-01-18 09:55:06","u_id":1,"score":3,"imgs":[{"c_id":1,"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"c_id":1,"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"c_id":1,"img_id":3,"img_url":{"url":"https:\/\/mengant.cn\/12"}}]},"express_info":[{"no":"71519121793117","brand":"ht","data":[{"time":"2019-01-18 00:38:09","context":"汕头市汕头市【汕头转运中心】，正发往【芜湖转运中心】"},{"time":"2019-01-18 00:36:00","context":"汕头市到汕头市【汕头转运中心】"},{"time":"2019-01-17 22:19:30","context":"汕头市汕头市【汕头】，【乐亿多\/18923931705】已揽收"},{"time":"2019-01-17 21:37:36","context":"汕头市到汕头市【汕头】"}],"order":"desc","status":"sending","res":0}],"address":{"id":1,"name":"朱明良","phone":"18956225230","province":"安徽省","city":"铜陵市","area":"铜官区","detail":"高速地产2"},"goods":{"id":1,"name":"笔记本","cover":"","money":2000}}
     * ls* @apiSuccess (返回参数说明) {int} id 订单id
     * @apiSuccess (返回参数说明) {String} code_number 订单号
     * @apiSuccess (返回参数说明) {int} count 商品数量
     * @apiSuccess (返回参数说明) {int} score 所需积分
     * @apiSuccess (返回参数说明) {String} address->name 下单人姓名
     * @apiSuccess (返回参数说明) {String} address->phone 下单人手机号
     * @apiSuccess (返回参数说明) {String} goods->name 商品名称
     * @apiSuccess (返回参数说明) {String} create_time 交易时间
     * @apiSuccess (返回参数说明) {String} send_time 发货时间
     * @apiSuccess (返回参数说明) {int} status 订单状态： 1 | 待发货 2 | 待收货；3 | 确认收货
     * @apiSuccess (返回参数说明) {int} comment_id 评论id：0 未评论；>0 已经评论
     * @apiSuccess (返回参数说明) {String} comment->content 评价内容
     * @apiSuccess (返回参数说明) {String} comment->score 评价星级
     * @apiSuccess (返回参数说明) {String} comment->type 评价类别：1 好评；2 | 中评；3 | 差评
     * @apiSuccess (返回参数说明) {String} comment->imgs 图片
     * @apiSuccess (返回参数说明) {String} express_info 物流信息
     * @apiSuccess (返回参数说明) {String} no 订单号
     * @apiSuccess (返回参数说明) {String} brand 快递类别
     * @apiSuccess (返回参数说明) {String} data 物流信息
     * @apiSuccess (返回参数说明) {String} time 所需积分
     * @apiSuccess (返回参数说明) {String}context 下单人姓名
     * @param $id
     * @return \think\response\Json
     */
    public function getTheOrderForMINI($id)
    {
        $info = (new GoodsOrderService())->getTheOrderForMINI($id);
        return json($info);
    }

    /**
     * @api {GET} /api/v1/goods/express/info 336-查看指定订单物流信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription小程序获取积分兑换订单信息
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/express/info?express_no=ht&express_code=71519121793117
     * @apiParam (请求参数说明) {String}  express_no 快递类别
     * @apiParam (请求参数说明) {String}  express_code 订单号
     * @apiSuccessExample {json} 返回样例:
     * [{"no":"71519121793117","brand":"ht","data":[{"time":"2019-01-18 21:11:33","context":"芜湖市到芜湖市【芜湖转运中心】"},{"time":"2019-01-18 00:38:09","context":"汕头市汕头市【汕头转运中心】，正发往【芜湖转运中心】"},{"time":"2019-01-18 00:36:00","context":"汕头市到汕头市【汕头转运中心】"},{"time":"2019-01-17 22:19:30","context":"汕头市汕头市【汕头】，【乐亿多\/18923931705】已揽收"},{"time":"2019-01-17 21:37:36","context":"汕头市到汕头市【汕头】"}],"order":"desc","status":"sending","res":0}]
     * @apiSuccess (返回参数说明) {String} no 订单号
     * @apiSuccess (返回参数说明) {String} brand 快递类别
     * @apiSuccess (返回参数说明) {String} data 物流信息
     * @apiSuccess (返回参数说明) {String} time 所需积分
     * @apiSuccess (返回参数说明) {String}context 下单人姓名
     * @param string $express_no
     * @param string $express_code
     * @return \think\response\Json
     */
    public function getExpressInfo($express_no, $express_code)
    {
        $info = (new GoodsOrderService())->getExpressInfo($express_no, $express_code);
        return json($info);

    }

    /**
     * @api {POST} /api/v1/goods/order/receive/confirm 337-用户确认收货
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 用户确认收货
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {int} id   订单id
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     */
    public function receiveConfirm($id)
    {
        $res = GoodsOrderT::update(['status' => 3], ['id' => $id]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 100002
            ]);

        }
        return json(new SuccessMessage());

    }


}