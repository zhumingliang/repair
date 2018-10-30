<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/28
 * Time: 4:10 PM
 */

namespace app\api\service;


use app\api\model\DemandOrderV;
use app\api\model\OrderReportV;
use app\api\model\ServiceOrderV;
use app\api\model\SystemTimeT;
use app\lib\enum\CommonEnum;
use app\lib\enum\OrderEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\OrderException;

class OrderReportService
{
    /**
     * type=1 全部状态
     * type=2 未完成
     * type=3 已完成
     * @param $key
     * @param $report_type
     * @param $page
     * @param $size
     * @return array|\think\Paginator
     * @throws \think\exception\DbException
     */
    public function demandReportForAdmin($key, $report_type, $page, $size)
    {
        $list = array();
        if ($report_type == 2) {

            $list = DemandOrderV::noCompleteForReport($key, $page, $size);
        } else if ($report_type == 3) {

            $list = DemandOrderV::completeForReport($key, $page, $size);
        } else if ($report_type == 1) {

            $list = DemandOrderV::allForReport($key, $page, $size);
            $data = $list['data'];
            $list['data'] = $this->prefixDemandOrderState($data);
        }

        return $list;


    }

    /**
     * report_type=1 未完成
     * report_type=2 待评价
     * report_type=3 已完成
     * report_type=3 全部
     * @param $key
     * @param $report_type
     * @param $page
     * @param $size
     * @return mixed|\think\Paginator
     * @throws \think\exception\DbException
     */
    public function serviceReportForAdmin($key, $report_type, $page, $size)
    {
        $list = array();
        switch ($report_type) {
            case 1:
                return ServiceOrderV::noCompleteForReport($key, $page, $size);
                break;
            case 2:
                return ServiceOrderV::readyCommentForReport($key, $page, $size);
                break;
            case 3:
                return ServiceOrderV::completeForReport($key, $page, $size);
                break;
            case 4:
                $list = ServiceOrderV::allForReport($key, $page, $size);
                $data = $list['data'];
                $list['data'] = $this->prefixServiceOrderState($data);
                return $list;
                break;
            default:
                return $list;

        }


    }

    /**
     * 处理需求订单状态
     * @param $list
     * @return mixed
     */
    private function prefixDemandOrderState($list)
    {
        if (count($list)) {
            foreach ($list as $k => $v) {
                if ($v['pay_id'] == CommonEnum::ORDER_STATE_INIT) {
                    $list[$k]['order_state'] = "未完成";
                } else {
                    if ($this->checkComment($list)) {
                        $list[$k]['order_state'] = "待评价";
                        continue;
                    }

                    if ($this->checkComplete($list)) {
                        $list[$k]['order_state'] = '已完成';
                        continue;
                    }

                    $list[$k]['order_state'] = '未完成';

                }
            }

        }
        return $list;

    }

    /**
     * 处理服务订单状态
     * @param $list
     * @return mixed
     */
    private function prefixServiceOrderState($list)
    {
        if (count($list)) {
            foreach ($list as $k => $v) {
                if ($v['pay_id'] == CommonEnum::ORDER_STATE_INIT) {
                    $list[$k]['order_state'] = "未完成";
                } else {
                    if ($this->checkComment($list)) {
                        $list[$k]['order_state'] = "待评价";
                        continue;
                    }

                    if ($this->checkComplete($list)) {
                        $list[$k]['order_state'] = '已完成';
                        continue;
                    }

                    $list[$k]['order_state'] = '未完成';

                }
            }

        }
        return $list;

    }


    /**
     *  按城市导出数据
     * @param $province
     * @param $city
     * @param $time_begin
     * @param $time_end
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function exportReportForCity($province, $city, $time_begin, $time_end)
    {
        $list = OrderReportV::reportForCity($province, $city, $time_begin, $time_end);

        $header = array(
            '下单人id',
            '下单时间',
            '电话',
            '价格',
            '服务名称',
            '订单号',
            '红包金额',
            '区域'
        );

        $file_name = '城市订单导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


    /**
     * @param $time_begin
     * @param $time_end
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function exportReport($time_begin, $time_end)
    {
        if (Token::getCurrentTokenVar('grade') == UserEnum::USER_GRADE_ADMIN) {
            $list = OrderReportV::reportWithoutCity($time_begin, $time_end);

        } else {
            $province = Token::getCurrentTokenVar('province');
            $city = Token::getCurrentTokenVar('city');
            $area = Token::getCurrentTokenVar('area');
            $list = OrderReportV::reportForJoin($province, $city, $area, $time_begin, $time_end);

        }
        $header = array(
            '下单人id',
            '下单时间',
            '电话',
            '价格',
            '服务名称',
            '订单号',
            '红包金额',
            '城市'
        );
        $file_name = '订单导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }

    /**
     * 导出数据到CSV文件
     * @param array $list 数据
     * @param array $title 标题
     * @param string $filename CSV文件名
     */
    public function put_csv($list, $title, $filename)
    {
        $file_name = $filename;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        $file = fopen('php://output', "a");
        $limit = 1000;
        $calc = 0;
        foreach ($title as $v) {
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE', $v);
        }
        fputcsv($file, $tit);
        foreach ($list as $v) {

            $calc++;
            if ($limit == $calc) {
                ob_flush();
                flush();
                $calc = 0;
            }
            foreach ($v as $t) {
                $t = is_numeric($t) ? $t . "\t" : $t;
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE', $t);
            }
            fputcsv($file, $tarr);
            unset($tarr);
        }
        unset($list);
        fclose($file);
        exit();
    }


    private function checkComment($data)
    {
        if ($data['confirm_id'] == 1 && $data['comment_id'] == CommonEnum::ORDER_STATE_INIT) {
            return true;
        }
        return false;

    }

    private function checkComplete($data)
    {
        if ($data['comment_id'] != CommonEnum::ORDER_STATE_INIT) {
            return true;
        }
        $orderTime = SystemTimeT::getSystemOrderTime();
        $user_confirm = $orderTime['user_confirm'];
        $consult = $orderTime['consult'];
        $user_confirm_limit = date('Y-m-d H:i', strtotime('-' . $user_confirm . ' minute',
            time()));
        $consult_limit = date('Y-m-d H:i', strtotime('-' . $consult . ' minute',
            time()));

        if ($data['pay_id'] != CommonEnum::ORDER_STATE_INIT &&
            $data['confirm_id'] == CommonEnum::ORDER_STATE_INIT &&
            time() > $user_confirm_limit
        ) {

            return true;

        }

        if ($data['pay_id'] != CommonEnum::ORDER_STATE_INIT &&
            $data['confirm_id'] == 2 &&
            time() > $consult_limit
        ) {

            return true;

        }

        return false;

    }

}