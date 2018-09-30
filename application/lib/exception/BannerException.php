<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/30
 * Time: 上午1:38
 */

namespace app\lib\exception;


class BannerException extends BaseException
{
    public $code = 401;
    public $msg = '新增轮播图失败';
    public $errorCode = 100001;

}