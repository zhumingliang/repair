<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/4/4
 * Time: 下午10:35
 */

namespace app\api\service;


use app\api\model\LogT;
use think\facade\Log;

class LogService
{


    /**
     * @param $msg
     */
    public static function Log($msg)
    {
        Log::record($msg, 'error');
        LogT::create(['msg' => $msg]);

    }

}