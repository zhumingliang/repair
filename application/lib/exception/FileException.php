<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/18
 * Time: 1:28 AM
 */

namespace app\lib\exception;


class FileException extends BaseException
{
    public $code = 401;
    public $msg = '新增系统文档设置失败';
    public $errorCode = 210001;
}