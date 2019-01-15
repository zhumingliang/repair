<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 9:13 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\GoodsT;
use app\api\service\GoodsService;
use app\api\validate\GoodsValidate;

class Goods extends BaseController
{
    public function save()
    {
        (new GoodsValidate())->scene('save')->goCheck();
        $params = $this->request->param();
        (new GoodsService())->save($params);

    }

}