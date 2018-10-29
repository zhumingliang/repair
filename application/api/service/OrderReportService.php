<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/28
 * Time: 4:10 PM
 */

namespace app\api\service;


use app\api\model\DemandOrderV;
use app\api\model\ServiceOrderV;

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
        } else if ($report_type == 2) {

            $list = DemandOrderV::allForReport($key, $page, $size);
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
                return ServiceOrderV::allForReport($key, $page, $size);
                break;
            default:
                return $list;

        }


    }



    public function exportBookRuleList()
    {
        $book = D('BookRule');
        $obj = $book->getRuleList(I('get.page'),
            I('get.rows'), I('get.key'), 1);

        $header = array(
            '预定ID',
            '开始时间',
            '定场周期',
            '定场数量',
            '场地名称',
            '定场下单人',
            '定场操作人',
            '状态'
        );

        $file_name = '批量定场信息列表' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($obj['rows'], $header, $file_name);


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

}