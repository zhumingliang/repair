<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/28
 * Time: 4:10 PM
 */

namespace app\api\service;


use app\api\model\DemandOrderV;

class OrderReportService
{
    public function demandReportForAdmin($key, $report_type, $page, $size)
    {
        $list = array();
        if ($report_type == 1) {

        } else if ($report_type == 2) {

        }

        return $list;


    }

    public function serviceReportForAdmin($key, $report_type, $page, $size)
    {
        switch ($report_type) {
            case OrderEnum::DEMAND_NORMAL_TAKING:
                return DemandOrderV::takingList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_PAY:
                return DemandOrderV::payList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_CONFIRM:
                return DemandOrderV::confirmList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_COMMENT:
                return DemandOrderV::commentList($u_id, $page, $size);
                break;
            case OrderEnum::DEMAND_NORMAL_COMPLETE:
                return DemandOrderV::completeList($u_id, $page, $size);
                break;

        }


    }


}