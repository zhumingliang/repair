<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/19
 * Time: 上午1:29
 */

namespace app\lib\exception;


class ShopException extends BaseException
{
    public $code = 401;
    public $msg = '新增商铺申请失败';
    public $errorCode = 60001;

}