<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 11:12 PM
 */

namespace app\api\service;


use zml\tp_tools\Curl;

class ExpressService
{

    private $app_id = '102089';
    private $method = 'express.info.get';
    private $api_key = '1e3586d845f32d77ebb3e657ff56d7b7bc1153cc';
    private $sign = '';
    private $ts = '';
    private $post_data = '';
    private $waybill_no = '';
    private $exp_company_code = '';

    public function __construct($waybill_no, $exp_company_code)
    {
        $this->waybill_no = $waybill_no;
        $this->exp_company_code = $exp_company_code;
        $this->ts = time();
        $this->sign = md5($this->app_id . $this->method . $this->ts . $this->api_key);
        $this->post_data = $this->prePostData();
    }

    public function getInfo()
    {

        $host = "https://kop.kuaidihelp.com/api";
        $method = "POST";
        $headers = array();
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");

        $bodys = http_build_query($this->post_data);
        $url = $host;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $res = curl_exec($curl);
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
            list($header, $body) = explode("\r\n\r\n", $res, 2);
            return json_decode($body);
        }

    }

    private function prePostData()
    {
        $data = '{ "waybill_no":"%s", "exp_company_code":"%s","result_sort":"0"}';
        $data = sprintf($data, $this->waybill_no, $this->exp_company_code);
        $bodys = [
            "app_id" => $this->app_id,
            "method" => $this->method,
            "sign" => $this->sign,
            "ts" => $this->ts,
            "data" => $data
        ];
        return $bodys;

    }


}