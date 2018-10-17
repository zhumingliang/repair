<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 1:28 AM
 */

namespace app\lib\exception;


class BondException extends BaseException
{
    public $code = 401;
    public $msg = '新增保证金订单失败';
    public $errorCode = 190001;
}