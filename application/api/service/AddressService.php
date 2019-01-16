<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/15
 * Time: 11:25 PM
 */

namespace app\api\service;


use app\api\model\AddressT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;

class AddressService
{
    private $address_common = 2;
    private $address_default = 1;

    public function save($params)
    {
        $params['u_id'] = Token::getCurrentUid();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $this->checkDefaultAddress($params['type'], $params['u_id']);
        $res = AddressT::create($params);
        if (!$res) {
            throw  new OperationException();
        }
    }

    public function update($params)
    {
        $this->checkDefaultAddress($params['type'], Token::getCurrentUid());
        $res = AddressT::update($params, ['id' => $params['id']]);
        if (!$res) {
            throw  new OperationException();
        }
    }


    private function checkDefaultAddress($type, $u_id)
    {
        if ($type == $this->address_default) {
            $address = new AddressT();
            $info = $address->where('u_id', $u_id)->where('type', $this->address_default)->find();
            if ($info) {
                $info->type = $this->address_common;
                $info->save();
            }

        }
    }


}