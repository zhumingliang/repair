<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:13 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\GoodsFormatT;
use app\api\model\GoodsImgT;
use app\api\model\GoodsOrderCommentV;
use app\api\model\GoodsT;
use app\api\model\GoodsV;
use app\api\service\GoodsService;
use app\api\validate\GoodsValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Goods extends BaseController
{
    /**
     * @api {POST} /api/v1/goods/save  315-新增积分商城商品
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 新增积分充值规则
     * @apiExample {post}  请求样例:
     *    {
     *       "c_id": 1,
     *       "name": "笔记本",
     *       "money": 1000,
     *       "score": 100000,
     *       "address": "安徽铜陵",
     *       "banner": "1,2",
     *       "show": "3,4,5",
     *       "cover": 2,
     *       "format": "尺寸,149x69x24mm;颜色,灰色",
     *     }
     * @apiParam (请求参数说明) {int} c_id   充值金额：单位分
     * @apiParam (请求参数说明) {String} name   商品名称
     * @apiParam (请求参数说明) {int} money   原价
     * @apiParam (请求参数说明) {int} score   所需积分
     * @apiParam (请求参数说明) {String} address   发货地
     * @apiParam (请求参数说明) {int} cover   商品封面图id
     * @apiParam (请求参数说明) {String} banner   商品轮播图：id,id,id
     * @apiParam (请求参数说明) {String} show   商品展示图：id,id,id
     * @apiParam (请求参数说明) {String} format   商品规格参数，数据格式：参数名称,详情;参数名称,详情
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function save()
    {
        (new GoodsValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        (new GoodsService())->save($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/goods/update  316-修改积分商城商品
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 修改积分商城商品
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "c_id": 1,
     *       "name": "笔记本",
     *       "money": 1000,
     *       "score": 100000,
     *       "address": "安徽铜陵",
     *       "banner": "1,2",
     *       "show": "3,4,5",
     *       "format": "尺寸,149x69x24mm;颜色,灰色",
     *     }
     * @apiParam (请求参数说明) {int} c_id   充值金额：单位分
     * @apiParam (请求参数说明) {String} name   商品名称
     * @apiParam (请求参数说明) {int} money   原价
     * @apiParam (请求参数说明) {int} score   所需积分
     * @apiParam (请求参数说明) {String} address   发货地
     * @apiParam (请求参数说明) {String} banner   "修改时增加新的商品轮播图"：id,id,id
     * @apiParam (请求参数说明) {String} show   "修改时增加新的商品展示图"：id,id,id
     * @apiParam (请求参数说明) {String} format "修改时增加新的商品规格参数"，数据格式：参数名称,详情;参数名称,详情
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function update()
    {
        (new GoodsValidate())->scene('update')->goCheck();
        $params = $this->request->param();
        (new GoodsService())->update($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/goods/format/update  317-修改商品参数规格
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改商品参数规格
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "name": "参数名称"
     *       "detail": "详情"
     *     }
     * @apiParam (请求参数说明) {String} id    规格id
     * @apiParam (请求参数说明) {String} name    参数名称
     * @apiParam (请求参数说明) {String} detail    详情
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function updateFormat()
    {
        (new GoodsValidate())->scene('update')->goCheck();
        $params = $this->request->param();
        $res = GoodsFormatT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '修改操作失败',
                'errorCode' => 160011
            ]);
        }

    }

    /**
     * @api {POST} /api/v1/goods/format/delete  318-删除商品参数规格
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改商品参数规格
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {String} id    规格id
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function formatDelete()
    {
        (new GoodsValidate())->scene('update')->goCheck();
        $params = $this->request->param();
        $res = GoodsFormatT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '删除操作失败',
                'errorCode' => 160011
            ]);
        }

    }

    /**
     * @api {POST} /api/v1/goods/image/delete  319-删除商品图片
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除商品图片
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1
     *     }
     * @apiParam (请求参数说明) {String} id    商品图片关联id
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function GoodsImageDelete()
    {
        (new GoodsValidate())->scene('update')->goCheck();
        $params = $this->request->param();
        $res = GoodsImgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '删除操作失败',
                'errorCode' => 160011
            ]);
        }

    }

    /**
     * @api {POST} /api/v1/goods/handel  320-商品状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除/上架/下架商品
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "state": 3
     *     }
     * @apiParam (请求参数说明) {int} id    商品id
     * @apiParam (请求参数说明) {int} state  1 | 下架；2| 上架；3 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function goodsHandel()
    {
        (new GoodsValidate())->scene('update')->goCheck();
        $params = $this->request->param();
        $res = GoodsT::update(['state' => $params['state']], ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException([
                'code' => 401,
                'msg' => '商品状态操作失败',
                'errorCode' => 160011
            ]);
        }

    }

    /**
     * @api {GET} /api/v1/goods/info  321-获取指定积分商品信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定积分商品信息
     * http://mengant.cn/api/v1/banner?id=1
     * @apiParam (请求参数说明) {int} id  商品id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"name":"笔记本","c_id":1,"money":2000,"score":200000,"address":"安徽铜陵","state":1,"create_time":"2019-01-15 16:01:39","imgs":[{"id":1,"img_id":1,"type":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"id":2,"img_id":2,"type":1,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"id":3,"img_id":3,"type":2,"img_url":{"url":"https:\/\/mengant.cn\/12"}},{"id":4,"img_id":4,"type":2,"img_url":{"url":"https:\/\/mengant.cn\/\/var\/www\/html\/repair\/public\/static\/imgs\/20181011\/465eef9ed0b58150f04c280adf59529d.jpg"}},{"id":5,"img_id":5,"type":2,"img_url":{"url":"https:\/\/mengant.cn\/\/var\/www\/html\/repair\/public\/static\/imgs\/20181011\/e62d792f98deb4c14a400b88b5d1cf32.jpg"}}],"format":[{"id":1,"name":"尺寸","detail":"149x69x24mm"},{"id":2,"name":"颜色","detail":"灰色"}],"category":{"id":1,"name":"数码"}}     * @apiSuccess (返回参数说明) {String} title    标题
     * @apiSuccess (返回参数说明) {int} id   商品id
     * @apiSuccess (返回参数说明) {String} name   商品名称
     * @apiSuccess (返回参数说明) {int} money   原价
     * @apiSuccess (返回参数说明) {int} score   所需积分
     * @apiSuccess (返回参数说明) {String} address   发货地
     * @apiSuccess (返回参数说明) {String} sell_num   月销量
     * @apiSuccess (返回参数说明) {String} imgs->type  商品图片类别：1 | 轮播图；2 | 详情图
     * @apiSuccess (返回参数说明) {String} imgs->img_url->url  商品图片地址
     * @apiSuccess (返回参数说明) {String} catetory->name  类别名称
     * @apiSuccess (返回参数说明) {String} format->name  参数规格-名称
     * @apiSuccess (返回参数说明) {String} format->detail  参数规格-详情
     * @param $id
     * @return \think\response\Json
     */
    public function getGoods($id)
    {
        $info = (new GoodsService())->getGoods($id);
        return json($info);

    }

    /**
     * @api {GET} /api/v1/goods/list/cms 322-CMS获取积分商品列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/list/cms?page=1&size=20&key=
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {String} key 关键字查询
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"name":"笔记本","cover":"","money":"20.0000","address":"铜陵","score":200000,"state":1,"update_time":"2019-01-15 16:01:39","category":"数码","sell_num":"0"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 商品id
     * @apiSuccess (返回参数说明) {String} name 商品名称
     * @apiSuccess (返回参数说明) {String} cover 商品封面图
     * @apiSuccess (返回参数说明) {String} address 发货地
     * @apiSuccess (返回参数说明) {float} money 原价
     * @apiSuccess (返回参数说明) {int} score 所需积分
     * @apiSuccess (返回参数说明) {String} category 类别
     * @apiSuccess (返回参数说明) {String} update_time 上架时间
     * @apiSuccess (返回参数说明) {int} sell_num  月销售量
     * @apiSuccess (返回参数说明) {int} state 商品状态状态：1 | 下架； 2 | 上架
     *
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getListForCMS($page = 1, $size = 20, $key = '')
    {
        $list = GoodsV::getListForCMS($page, $size, $key);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/goods/list/mini 323-小程序获取积分商品列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/list/mini?page=1&size=20
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"name":"笔记本","cover":"","money":"20.00","score":200000}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 商品id
     * @apiSuccess (返回参数说明) {String} name 商品名称
     * @apiSuccess (返回参数说明) {String} cover 商品封面图
     * @apiSuccess (返回参数说明) {float} money 原价
     * @apiSuccess (返回参数说明) {int} score 所需积分
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getListForMINI($page = 1, $size = 20)
    {
        $list = GoodsT::getListForMINI($page, $size);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/goods/comment 343-小程序获取积分商品评论列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/goods/comment?id=1&page=1&size=20
     * @apiParam (请求参数说明) {int} id 商品id
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":10,"current_page":1,"last_page":1,"data":[{"id":1,"o_id":1,"content":"很好","type":2,"state":1,"create_time":"2019-01-18 09:55:06","update_time":"2019-01-18 09:55:06","u_id":1,"score":3,"g_id":1,"nickName":"盟蚁","avatarUrl":"","imgs":[{"c_id":1,"img_id":1,"img_url":{"url":"https:\/\/mengant.cn\/1212"}},{"c_id":1,"img_id":2,"img_url":{"url":"https:\/\/mengant.cn\/121"}},{"c_id":1,"img_id":3,"img_url":{"url":"https:\/\/mengant.cn\/12"}}]}]}     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 商品id
     * @apiSuccess (返回参数说明) {String} nickName 评论者昵称
     * @apiSuccess (返回参数说明) {String} avatarUrl 评论者头像
     * @apiSuccess (返回参数说明) {String} score 评价星级
     * @apiSuccess (返回参数说明) {String} content 评价内容
     * @apiSuccess (返回参数说明) {String} type 评价类别：1 好评；2 | 中评；3 | 差评
     * @apiSuccess (返回参数说明) {String} imgs 图片
     * @apiSuccess (返回参数说明) {String} imgs->img_url->url 评论图片地址

     *
     * @param $id
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getGoodsComment($id, $page = 1, $size = 10)
    {
        $list = GoodsOrderCommentV::getComment($id, $page, $size);
        return json($list);

    }


}