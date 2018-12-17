<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 4:00 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\IndexCmsV;
use app\api\model\IndexServiceT;
use app\api\model\ServiceAllV;
use app\api\model\ServiceV;
use app\api\model\ShopServiceV;
use app\api\service\ExtendService;
use app\api\validate\ExtendValidate;
use app\api\validate\PagingParameter;
use app\lib\enum\CommonEnum;
use app\lib\exception\ExtendException;
use app\lib\exception\SuccessMessage;
use app\api\service\Token as TokenService;

class ServicesExtend extends BaseController
{
    /**
     * @api {GET} /api/v1/extend/cms/list 35-CMS获取推广商品列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取推广商品列表（管理员/加盟商）
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/extend/list?page=1&size=20&type=1&keyW="家政"
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} type 推广数据类别：1 | 待审核；2 | 审核通过
     * @apiParam (请求参数说明) {String} keyW 关键字查询
     * @apiSuccessExample {json} 返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"extend_id":1,"s_id":5,"shop_name":"修之家","service_name":"修五金","type":1,"province":"安徽省","city":"铜陵市","area":"铜官区","state":1,"create_time":"2018-09-26 22:51:02"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} extend_id 推广记录id
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {int} type 服务上级分类：1 | 家政；2 | 维修
     * @apiSuccess (返回参数说明) {String} shop_name 商品名称
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} create_time 创建时间
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getListForCMS()
    {
        (new ExtendValidate())->scene('list')->goCheck();
        $type = $this->request->param('type');
        $page = $this->request->param('page');
        $size = $this->request->param('size');
        $keyW = $this->request->param('keyW');
        $list = ExtendService::getList($type, $page, $size, $keyW);
        return json($list);


    }

    /**
     * @api {POST} /api/v1/extend/handel  36-推广商品状态操作
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员/加盟商 操作推广商品状态
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * "type":2
     * }
     * @apiParam (请求参数说明) {int} id 推广记录id
     * @apiParam (请求参数说明) {int} type 操作类别：2 | 通过；3 | 拒绝；4 | 删除
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function handel()
    {
        // (new ExtendValidate())->scene('handel')->goCheck();
        $params = $this->request->param();
        ExtendService::handel($params['id'], $params['type']);
        return json(new  SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/extend/service  37-CMS获取指定推广商品信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取指定推广商品信息
     * http://mengant.cn/api/v1/extend/service?id=1
     * @apiParam (请求参数说明) {int} id  推广记录id
     * @apiSuccessExample {json} 返回样例:
     * {"extend_id":1,"s_id":5,"shop_name":"修之家","service_name":"修五金","type":1,"province":"安徽省","city":"铜陵市","address":"","area":"铜官区","phone":"1895622530","price":1000,"des":"五金大神","head_url":"http:\/\/repair.com\/","create_time":"2018-09-26 22:51:01"}
     * @apiSuccess (返回参数说明) {int} extend_id 推广记录id
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {String} shop_name 店铺名称
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     * @apiSuccess (返回参数说明) {int} type 服务类型：1 | 家政服务；2 | 维修服务
     * @apiSuccess (返回参数说明) {String} des 服务描述
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {String} phone 联系方式
     * @apiSuccess (返回参数说明) {String} head_url 店铺头像
     * @apiSuccess (返回参数说明) {String} create_time 发布时间
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getTheService($id)
    {
        $service = ExtendService::getTheService($id);
        return json($service);
    }

    /**
     * @api {GET} /api/v1/service/index 45-小程序首页服务列表-家政服务/维修服务
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序首页推广列表-家政服务
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/extend/index?&area="铜官区"
     * @apiParam (请求参数说明) {String} area 用户地址位置--区
     * @apiSuccessExample {json} 返回样例:
     * [{"s_id":1,"sell_money":"0","sell_num":"0","name":"修五金1","cover":"https:\/\/mengant.cn\/static\/imgs\/B9439BE2-857E-22D2-D058-CFE57315EEAE.jpg","type":1},{"s_id":2,"sell_money":"0","sell_num":"0","name":"修五金2","cover":"https:\/\/mengant.cn\/static\/imgs\/5782AD69-9B21-2B94-DCCA-6AD299AF32E1.jpg","type":1},{"s_id":5,"sell_money":"10000","sell_num":"1","name":"修五金4","cover":"https:\/\/mengant.cn\/static\/imgs\/CE41DE68-9E89-B6C1-E63D-57149CC54BBF.jpg","type":1}]
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {int} sell_money 总销量
     * @apiSuccess (返回参数说明) {int} sell_num 成交额
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {String} cover 服务封面
     * @apiSuccess (返回参数说明) {int} extend 是否推广 1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {int} type 服务类别：1 | 家政；2 | 维修
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getServiceIndex()
    {
        (new ExtendValidate())->scene('index')->goCheck();
        $params = $this->request->param();
        $list = ExtendService::getIndexServiceList($params['area']);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/extend/repair 46-小程序首页服务列表-获取更多
     * @apiGroup  UNUSED
     * @apiVersion 1.0.1
     * @apiDescription  小程序首页推广列表-维修服务/家政服务
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/service/index/more?page=1&size=20&area="铜官区"&c_id=0&type=1
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {String} area 用户地址位置--区
     * @apiParam (请求参数说明) {int} type  1 | 维修服务；2 | 家政服务
     * @apiParam (请求参数说明) {int} c_id 服务类别id，首页获取推广列表时默认为0，点击更多时，选择对应的类别传入对应的c_id
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":"10","current_page":1,"last_page":1,"data":[{"s_id":1,"sell_money":"0","sell_num":"0","name":"修五金","cover":"http:\/\/repair.com\/static\/imgs\/B9439BE2-857E-22D2-D058-CFE57315EEAE.jpg","area":"铜官区"},{"s_id":5,"sell_money":"10000","sell_num":"1","name":"修五金","cover":"http:\/\/repair.com\/static\/imgs\/CE41DE68-9E89-B6C1-E63D-57149CC54BBF.jpg","area":"铜官区"}]}     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} s_id 服务id
     * @apiSuccess (返回参数说明) {int} sell_money 总销量
     * @apiSuccess (返回参数说明) {int} sell_num 成交额
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {String} cover 服务封面
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\exception\DbException
     */

    public function getRepairList()
    {
        (new PagingParameter())->goCheck();
        $params = $this->request->param();
        $list = ExtendService::getRepairList($params['area'], $params['page'], $params['size'], $params['c_id'], $params['type']);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/extend/mini/service  47-小程序查看服务详情
     * @apiGroup  MINI
     * @apiVersion 1.0.1
     * @apiDescription  小程序查看服务详情(推广服务/家政/维修模块点击进入)
     * http://mengant.cn/api/v1/extend/mini/service?id=1
     * @apiParam (请求参数说明) {int} id  推广记录id
     * @apiSuccessExample {json} 返回样例:
     * {"id":4,"shop_id":1,"name":"修五金","area":"台儿庄区","des":"五金大神","price":1000,"unit":"ci","collection":1,"phone_check":1,"imgs":[{"img_id":1,"img_url":{"url":"http:\/\/repair.com\/1212"}},{"img_id":2,"img_url":{"url":"http:\/\/repair.com\/121"}}],"shop":{"id":1,"address":"","phone":"1895622530"}}
     * @apiSuccess (返回参数说明) {int} id 服务id
     * @apiSuccess (返回参数说明) {int} shop_id 店铺id
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {String} des 服务描述
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {int} price 价格
     * @apiSuccess (返回参数说明) {int} collection 用户是否收藏该服务：>0 | 收藏；0 | 未收藏
     * @apiSuccess (返回参数说明) {int} phone_check 用户是否 可以拨打电话；·1 | 是；2 | 否
     * @apiSuccess (返回参数说明) {String} unit 单位
     * @apiSuccess (返回参数说明) {String} imgs 轮播图
     * @apiSuccess (返回参数说明) {int} img_id 图片id
     * @apiSuccess (返回参数说明) {String} url 图片地址
     *
     * @param $id
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getServiceForMini($id)
    {
        $info = ExtendService::getServiceForMini($id);
        return json($info);
    }

    /**
     * @api {GET} /api/v1/services 149-管理员-商家服务列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员-商家服务列表
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/services?page=1&size=20&key=""
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {String} key 关键字
     * @apiSuccessExample {json} 返回样例:
     * {"total":8,"per_page":"1","current_page":1,"last_page":8,"data":[{"shop_id":5,"u_id":12,"service_id":6,"type":2,"shop_name":"维修小家","service_name":"修电视","city":"铜陵市","extend":0}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} shop_id 商户id
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {int} type 服务类别:1 | 维修服务；2| 家政服务
     * @apiSuccess (返回参数说明) {String} shop_name 店铺名称
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     * @apiSuccess (返回参数说明) {String} city 所在城市
     * @apiSuccess (返回参数说明) {String} extend 是否推广: 1 | 推广；2 | 不推广
     *
     * @param int $page
     * @param int $size
     * @param string $key
     * @return \think\response\Json
     */
    public function getServiceForCMS($page = 1, $size = 20, $key = '')
    {
        $list = ShopServiceV::services($page, $size, $key);
        return json($list);


    }

    /**
     * @api {GET} /api/v1/index/services/all   182-首页服务设置-获取所有服务
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * http://mengant.cn/api/v1/index/services/all?type=1
     * @apiParam (请求参数说明) {int} type  服务类别：1 | 维修服务；2 家政服务
     * @apiSuccessExample {json} 返回样例:
     * [{"id":20,"service_name":"哈哈"}]
     * @apiSuccess (返回参数说明) {int} id 服务id
     * @apiSuccess (返回参数说明) {String} service_name 服务名称
     *
     * @param $type
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getServicesForIndex($type)
    {
        $sql_join = preJoinSqlForGetDShops(TokenService::getCurrentTokenVar('province'), TokenService::getCurrentTokenVar('city'),
            TokenService::getCurrentTokenVar('area'));
        $list = ServiceAllV::where('type', $type)
            ->whereRaw($sql_join)
            ->field('id,service_name,shop_name')
            ->order('create_time desc')
            ->select();
        return json($list);

    }


    /**
     * @api {POST} /api/v1/index/service/save  183-首页服务设置-新增首页服务
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *     }
     * @apiParam (请求参数说明) {int} id  服务id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @return \think\response\Json
     * @throws ExtendException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function indexServiceSave($id)
    {
        $params = [
            's_id' => $id,
            'state' => CommonEnum::STATE_IS_OK,
            'province' => TokenService::getCurrentTokenVar('province'),
            'city' => TokenService::getCurrentTokenVar('city'),
            'area' => TokenService::getCurrentTokenVar('area'),
        ];

        $res = IndexServiceT::create($params);
        if (!$res->id) {
            throw new ExtendException(
                ['code' => 401,
                    'msg' => '新增服务展示失败',
                    'errorCode' => 130003
                ]
            );

        }

        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/index/service/handel  184-首页服务设置-删除首页服务
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *     }
     * @apiParam (请求参数说明) {int} id  服务列表id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @param $id
     * @return \think\response\Json
     * @throws ExtendException
     */
    public function indexHandel($id)
    {
        $res = IndexServiceT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new ExtendException(
                ['code' => 401,
                    'msg' => '首页服务展示删除失败',
                    'errorCode' => 130003
                ]
            );
        }

        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/index/service/list 185-首页服务设置-获取首页服务列表（维修/家政）
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  CMS获取推广商品列表（管理员/加盟商）
     *
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/index/service/list?page=1&size=20&type=1
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {int} type 服务类别：1 | 维修；2 | 家政
     * @apiSuccessExample {json} 返回样例:
     * {"total":2,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":4,"shop_id":5,"state":1,"name":"修电视","cover":"static\/imgs\/20181023\/4781690b9f11d05f691f7173a443e78d.jpg","type":2,"province":"安徽省","city":"铜陵市","area":"郊区","shop_name":"维修小家","c_id":1},{"id":5,"shop_id":5,"state":1,"name":"哈哈","cover":"https:\/\/mengant.cn\/static\/imgs\/20181103\/55cc695367853af39c139972b5c598d1.jpg","type":2,"province":"安徽省","city":"铜陵市","area":"郊区","shop_name":"维修小家","c_id":5}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 首页记录id
     * @apiSuccess (返回参数说明) {int} shop_id 商家id
     * @apiSuccess (返回参数说明) {String} name 服务名称
     * @apiSuccess (返回参数说明) {String} province 省
     * @apiSuccess (返回参数说明) {String} city 市
     * @apiSuccess (返回参数说明) {String} area 区
     * @apiSuccess (返回参数说明) {String} shop_name 店铺名称
     * @param $page
     * @param $size
     * @param $type
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getIndexListForCMS($page = 1, $size = 20, $type)
    {
        $sql_join = preJoinSqlForGetDShops(TokenService::getCurrentTokenVar('province'), TokenService::getCurrentTokenVar('city'),
            TokenService::getCurrentTokenVar('area'));

        $list = IndexCmsV::where('state', CommonEnum::STATE_IS_OK)
            ->where('type', $type)
            ->whereRaw($sql_join)
            ->paginate($size, false, ['page' => $page]);

        return json($list);


    }


}