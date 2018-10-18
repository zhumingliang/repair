<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 11:38 AM
 */

namespace app\lib\exception;


class WithdrawException extends BaseException
{
    public $code = 401;
    public $msg = '新增提现记录失败';
    public $errorCode = 200001;

}