<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/30
 * Time: 12:59 AM
 */

namespace app\api\service;


use app\api\model\FormidT;
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
        $data = [
            "touser" => $this->openid,
            "template_id" => $template_id,
            "page" => "index",
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
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
        $res = Curl::postToData($url, $data);

        $res_obj = json_decode($res);
        print_r($res_obj);
        //$this->saveRecord($data, $res_obj);

    }

    public function sendToNormal()
    {
        $template_id = 'pmETPUOVHoDTngu4gJ8C5B39-AS3KX7_42Tshez2s18';
        $at = new AccessToken();
        $access_token = $at->get();
        $params = $this->params;
        $data = [
            "touser" => $this->openid,
            "template_id" => $template_id,
            "page" => "index",
            "form_id" => $this->form_id,
            "keyword1" => [
                "value" => $params['shop_name'],
            ],
            "keyword2" => [
                "value" => $params['demand'],
            ],
            "keyword3" => [
                "value" => $params['time_begin'],
            ],
            "keyword4" => [
                "value" => $params['phone'],
            ]
        ];
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
        $res = Curl::postToData($url, $data);

        $res_obj = json_decode($res);
        print_r($res_obj);
        //$this->saveRecord($data, $res_obj);

    }

    private function formHandel($form_id)
    {
        FormidT::update(['state', CommonEnum::STATE_IS_FAIL], ['form_id', $form_id]);

    }


}