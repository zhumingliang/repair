<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 11:46 PM
 */

namespace app\lib\exception;


class ExtendException extends BaseException
{
    public $code = 401;
    public $msg = '操作推广信息状态失败';
    public $errorCode = 130001;

}