<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;

class Receive extends Model
{

    //查询可领取工作单列表
    public function availableList($data)
    {
      $sql = "select a.city from userinfo  a where userid = '{$data['userid']}'";
      $res = Db::query($sql);
      if ($res['0']['city'] == null) {
        throw new Exception('该用户没有设置区域代码', '500');
      }
      $area_code = trim($res['0']['city']);
     
        //工单号
      if (isset($data['work_order_no']) && strlen($data['work_order_no'])) {
            $where = "and a.work_order_no LIKE '%{$data['work_order_no']}%'";
      } else {
            $where = '';
      }
        $sql = "select 
        trim(a.work_order_no) work_order_no,
        a.longitude,
        a.latitude,
        b.mcht_name,
        a.mcht_addr,
        to_char(to_date(a.plan_date, 'YYYYMMDD'), 'YYYY-MM-DD') plan_date,
        a.status,
        case
                when substr(a.work_order_no, 0, 2) like '00%' then
                 '装机'
                when substr(a.work_order_no, 0, 2) like 'WH%' then
                 '维护'
                else
                 '巡检'
              end work_type
   from WORKORDER a, mcht_info b,virterm_info c 
  where 1 = 1
    and substr(c.area, 0, 4)  = '$area_code'  
    and a.mcht_id = b.mcht_id
    and a.term_no = c.term_no(+)
    and a.status in ('0') 
    $where";
   
    $res = Db::query($sql);
    $new_list = [];
   
    foreach ($res as $k => $v){
      if ($data['app_longitude'] && $data['app_latitude'] && $v['longitude'] && $v['latitude']) {
            $distance = $this->getdistance($data['app_longitude'],$data['app_latitude'],$v['latitude'],$v['latitude']);
            $v['distance'] = round($distance,2);
            if ($v['distance']>20) {
              $v['distance'] = '';
            }
            }else{
        $v['distance'] = '';
      }
     
      $new_list[]= $v;
    }
    return $new_list;
    }

    //领取工单
    public function getWorkOrder($data)
    {

        // $sql = "select count(*)as count  from WORKORDER a
        //     where a.work_id = '{$data['empid']}'
        //      and  substr(a.work_order_no, 0, 2) like 'XJ%'
        //      and a.status not in('0','3','4','A')";
        // $check_count = Db::query($sql);
        // if ($check_count != null) {
        //     if ($check_count['0']['count'] >= 10) {
        //         throw new Exception('你目前可领取巡检工单数量已经超过限制', 500);
        //     }
        // }
        // print_r($check_count);
        // die;
        $sql1 = "select a.work_order_no,a.status from WORKORDER a
             where a.work_order_no = '{$data['work_order_no']}'";
        $check_status = Db::query($sql1);
        if ($check_status == null) {
            throw new Exception('没有找到工作单', 500);
        }

        if ($check_status['0']['status'] != 0) {
            throw new Exception('当前工作单状态不允许领取', 500);
        }
        $date = date("Ymd",time());
        $get_work_order_sql = "UPDATE WORKORDER set work_id = '{$data['empid']}',plan_date ='$date', status='1' where work_order_no='{$data['work_order_no']}'";
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

    /**
 * 求两个已知经纬度之间的距离,单位为米
 * 
 * @param lng1 $ ,lng2 经度
 * @param lat1 $ ,lat2 纬度
 * @return float 距离，单位千米  (求米把结果*1000)
 */
function getdistance($lng1, $lat1, $lng2, $lat2) {
  // 将角度转为狐度
  $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
  $radLat2 = deg2rad($lat2);
  $radLng1 = deg2rad($lng1);
  $radLng2 = deg2rad($lng2);
  $a = $radLat1 - $radLat2;
  $b = $radLng1 - $radLng2;
  $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
  return $s;
} 
}
