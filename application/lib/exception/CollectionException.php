<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/23
 * Time: 上午12:37
 */

namespace app\lib\exception;


class CollectionException extends BaseException
{
    public $code = 401;
    public $msg = '新增收藏失败';
    public $errorCode = 80001;

}