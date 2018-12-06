<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/30
 * Time: 12:59 AM
 */

namespace app\api\service;


use app\api\model\FormidT;
use app\api\model\LogT;
use app\lib\enum\CommonEnum;
use zml\tp_tools\Curl;

class WxTemplate
{

    private $form_id;
    private $params;
    private $openid;

    public function __construct($openid, $form_id, $params)
    {
        $this->openid = $openid;
        $this->form_id = $form_id;
        $this->params = $params;

    }

    public function sendToShop()
    {
        $template_id = 'gSnVVqhEtmO6f4d6VJPZPg4qP172f_RdvC3_RB1h-Ds';
        $at = new AccessToken();
        $access_token = $at->get();
        $params = $this->params;
        $page = 'pages/order-detail/index?id='.$params['id'].'&type=1&state=1&template=1';
        $data = [
            "touser" => $this->openid,
            "template_id" => $template_id,
            "page" => $page,
            "form_id" => $this->form_id,
            "data" => [
                "keyword1" => [
                    "value" => $params['server'],
                ],
                "keyword2" => [
                    "value" => $params['time_begin'],
                ],
                "keyword3" => [
                    "value" => $params['phone'],
                ],
                "keyword4" => [
                    "value" => $params['time'],
                ],
                "keyword5" => [
                    "value" => $params['remark'],
                ]
            ]
        ];
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$access_token";
        $res = Curl::postCurl($url, $data,"json");
        LogT::create(['msg'=>$res]);
        $res_obj = json_decode($res);
        if ($res_obj->errcode == 0) {
            return true;
        }
        return false;

    }

    public function sendToNormal()
    {
        $template_id = 'pmETPUOVHoDTngu4gJ8C5B39-AS3KX7_42Tshez2s18';
        $at = new AccessToken();
        $access_token = $at->get();
        $params = $this->params;
        $page = 'pages/order-detail/index?id='.$params['id'].'&type=2&state=2&template=1';
        $data = array(
            "touser" => $this->openid,
            "template_id" => $template_id,
            "page" => $page,
            "form_id" => $this->form_id,
            "data" => array(
                "keyword1" => array(
                    "value" => $params['shop_name']
                ),
                "keyword2" => array(
                    "value" => $params['demand'],
                ),
                "keyword3" => array(
                    "value" => $params['time'],
                ),
                "keyword4" => array(
                    "value" => $params['phone'],
                )
            )
        );
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$access_token";
        $res = Curl::postCurl($url, $data, 'json');
        $res_obj = json_decode($res);
        LogT::create(['msg'=>$res]);
        if ($res_obj->errcode == 0) {
            return true;
        }
        return false;
        //$this->saveRecord($data, $res_obj);

    }


}