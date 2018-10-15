<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/15
 * Time: 11:33 PM
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 401;
    public $msg = '店铺接单失败';
    public $errorCode = 150001;

}