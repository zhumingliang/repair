<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/21
 * Time: 下午3:57
 */

namespace app\api\validate;



class UserInfo extends BaseValidate
{
    protected $rule = [
        'encryptedData' => 'require|isNotEmpty',
        'iv' => 'require|isNotEmpty',
        ];

    protected $scene = [
        'encrypted' => ['encryptedData', 'iv']
    ];


}