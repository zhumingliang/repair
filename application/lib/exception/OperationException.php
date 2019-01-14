<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 1:56 AM
 */

namespace app\lib\exception;


class OperationException extends BaseException
{
    public $code = 401;
    public $msg = '新增操作失败';
    public $errorCode = 40001;

}