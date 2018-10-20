<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/20
 * Time: 8:42 PM
 */

namespace app\lib\exception;


class OrderMsgException extends BaseException
{
    public $code = 401;
    public $msg = '保存消息记录失败';
    public $errorCode = 21001;

}