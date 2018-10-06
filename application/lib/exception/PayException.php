<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/5
 * Time: 9:43 AM
 */

namespace app\lib\exception;


class PayException extends BaseException
{
    public $code = 401;
    public $msg = '支付订单类别异常';
    public $errorCode = 150001;

}