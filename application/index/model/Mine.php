<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;

class Mine extends Model
{

    //查询可领取工作单列表
    public function getPerformance($data)
    {
      
        if (isset($data['period_type']) && strlen($data['period_type'])) {
            switch ($data['period_type']) {
                    //本周
                case '1':
                    $sql = "select case
                when substr(a.work_order_no, 0, 2) like '00%' then
                 '装机'
                when substr(a.work_order_no, 0, 2) like 'WH%' then
                 '维护'
                else
                 '巡检'
              end work_type,
              count(1) cnt
         from WORKORDER a 
        where a.work_id = '{$data['work_id']}' and a.plan_date is not null
        and (to_date(a.work_date,'YYYY/MM/DD') between TRUNC(NEXT_DAY(SYSDATE-8,1)+1) AND TRUNC(NEXT_DAY(SYSDATE-8,1)+7)+1)
        group by 
        case 
          when substr(a.work_order_no, 0, 2) like '00%' then '装机' 
          when substr(a.work_order_no, 0, 2) like 'WH%' then '维护' 
          else '巡检' 
        end";
                    break;
                    //本月    
                case '2';
                    $sql = "select case
                when substr(a.work_order_no, 0, 2) like '00%' then
                 '装机'
                when substr(a.work_order_no, 0, 2) like 'WH%' then
                 '维护'
                else
                 '巡检'
              end work_type,
              count(1) cnt
         from WORKORDER a 
        where a.work_id = '{$data['work_id']}' and a.plan_date is not null
        and substr(a.work_date,0,6)=TO_CHAR(SYSDATE,'YYYYMM')
        group by 
        case 
          when substr(a.work_order_no, 0, 2) like '00%' then '装机' 
          when substr(a.work_order_no, 0, 2) like 'WH%' then '维护' 
          else '巡检' 
        end";
                    break;
                case '3';
                    //上月
                    $sql = "select case
                when substr(a.work_order_no, 0, 2) like '00%' then
                 '装机'
                when substr(a.work_order_no, 0, 2) like 'WH%' then
                 '维护'
                else
                 '巡检'
              end work_type,
              count(1) cnt
         from WORKORDER a 
        where a.work_id = '{$data['work_id']}' and a.plan_date is not null
        and substr(a.work_date,0,6)=TO_CHAR(ADD_MONTHS(SYSDATE,-1),'YYYYMM')
        group by 
        case 
          when substr(a.work_order_no, 0, 2) like '00%' then '装机' 
          when substr(a.work_order_no, 0, 2) like 'WH%' then '维护' 
          else '巡检' 
        end";
                    break;
            }
        } else {
            //本周
            $sql = "select case
            when substr(a.work_order_no, 0, 2) like '00%' then
            '装机'
            when substr(a.work_order_no, 0, 2) like 'WH%' then
            '维护'
            else
            '巡检'
            end work_type,
            count(1) cnt
            from WORKORDER a 
            where a.work_id = '{$data['work_id']}' and a.plan_date is not null
            and (to_date(a.work_date,'YYYY/MM/DD') between TRUNC(NEXT_DAY(SYSDATE-8,1)+1) AND TRUNC(NEXT_DAY(SYSDATE-8,1)+7)+1)
            group by 
            case 
            when substr(a.work_order_no, 0, 2) like '00%' then '装机' 
            when substr(a.work_order_no, 0, 2) like 'WH%' then '维护' 
            else '巡检' 
            end";
        }

        $res = Db::query($sql);
        return $res;
    }

    //领取工单
    public function getWorkOrder($data)
    {

        $sql = "select count(*)as count  from WORKORDER a
            where a.work_id = '{$data['empid']}'
             and  substr(a.work_order_no, 0, 2) like 'XJ%'
             and a.status not in('0','3','4','A')";
        $check_count = Db::query($sql);
        if ($check_count != null) {
            if ($check_count['0']['count'] >= 10) {
                throw new Exception('你目前可领取巡检工单数量已经超过限制', 500);
            }
        }
        // print_r($check_count);
        // die;
        $sql1 = "select a.work_order_no,a.status from WORKORDER a
             where a.work_order_no = '{$data['work_order_no']}'
              and  substr(a.work_order_no, 0, 2) like 'XJ%'";
        $check_status = Db::query($sql1);
        if ($check_status == null) {
            throw new Exception('没有找到工作单', 500);
        }

        if ($check_status['0']['status'] != 0) {
            throw new Exception('当前工作单状态不允许领取', 500);
        }
        $get_work_order_sql = "UPDATE WORKORDER set work_id = '{$data['empid']}', status='1' where work_order_no='{$data['work_order_no']}'";
        $update = Db::execute($get_work_order_sql);
        if (!$update) {
            throw new Exception('领取工作单失败', 500);
        }
    }

    //查看已领取工作单详情
    public function getDetails(array $data = null)
    {
        $work_order_no = $data['work_order_no'];
        $sql = "select *
        from v_workorder a
                where 1 = 1
                and a.work_order_no = '{$work_order_no}'
                and a.work_id = '{$data['empid']}'";

        $work_order_info = Db::query($sql);
        if ($work_order_info == null) {
            throw new Exception('没有找到工作单', 500);
        }
        if ($work_order_info['0']['status'] == '0') {
            throw new Exception('还没领取的工单不能查看详情', 500);
        }
        return $work_order_info;
    }
}
