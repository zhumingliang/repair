<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/4
 * Time: 1:56 AM
 */

namespace app\lib\exception;


class SystemException extends BaseException
{
    public $code = 401;
    public $msg = '新增城市优惠失败';
    public $errorCode = 140001;


}