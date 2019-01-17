<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/16
 * Time: 11:12 PM
 */

namespace app\api\service;


class ExpressService
{
    public function getInfo()
    {
        $host = "https://kop.kuaidihelp.com/api";
        $method = "POST";
        $headers = array();
       //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodys = [
            "app_id" => '50001',
            "method" => 'express.info.get',
            "sign" => "bdf3b5f50865ac813cbdfd6c9b572b79",
            "ts" => '1524209949',
            "data" => '{ "waybill_no":"3832883261957", "exp_company_code":"韵达","result_sort":"0"}'
        ];
        $bodys = http_build_query($bodys);
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
        var_dump(curl_exec($curl));
    }

}