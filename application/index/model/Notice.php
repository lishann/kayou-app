<?php
namespace app\index\model;

use think\Model;
use think\Db;
use Exception;


class Notice extends Model
{
  public function getTimeOut(array $data = null)
  {

    $now_date = date('Ymd',time());
    $count_sql = "select  
    sum(case
    when a.work_date is null then
     1
    else
     0
    end)count from workorder a where 1=1 and work_id = '{$data['empid']}' and a.plan_date < {$now_date}";
  

    $sql = "select 
    trim(a.work_order_no) work_order_no,
    trim(a.mcht_id) mcht_id,
    trim(b.mcht_name) mcht_name,
    to_char(to_date(a.plan_date, 'YYYYMMDD'), 'YYYY-MM-DD')plan_date,
    trim(a.delay_note) delay_note,
     case
    when substr(a.work_order_no, 0, 2) like '00%' then
     '装机工单'
     when a.self_flag = '1' then
     '自助派单'
    when substr(a.work_order_no, 0, 2) like 'WH%' then
     '维护工单'
    else
     '巡检工单'
    end work_type,
    ($now_date - a.plan_date)time_out_days
     from workorder a ,mcht_info b where 1=1 and a.mcht_id  = b.mcht_id
     and a.work_date is null
     and a.plan_date < {$now_date}
     and a.status <>'A'
     and work_id  = '{$data['empid']}'";
     $count = Db::query($count_sql)['0'];
     $list = Db::query($sql);
     if ($list == null) {
      return [];
     }
     $res['count']=$count['count'];
     $res['list']=$list;
     return $res;
  }

  //超时备注
  public function toRemark($data)
  {
    $check_sql = "select delay_times from workorder where work_order_no='{$data['work_order_no']}'";
    $delay_times = Db::query($check_sql);
    if ($delay_times['0']['delay_times'] >=3) {
      throw new Exception('当前工单的延期次数已超过3次', '500');
    }
    $sql = "update workorder set delay_note = '{$data['delay_note']}',delay_times = delay_times+1,
    plan_date = {$data['delay_date']} where work_order_no = '{$data['work_order_no']}'";
    $update = Db::execute($sql);
    if (!$update) {
      throw new Exception('工单延期和备注失败', '500');
    }
  }

}