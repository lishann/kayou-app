<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;
use think\facade\Cache;
use think\cache\driver\Redis;

class Workorders extends Model
{

  //工作单类型
  public function work_order_type($work_order_no)
  {
    $work_order_no = substr($work_order_no, 0, 2);
      switch ($work_order_no) {
        case "WH";
          $work_type = '维护';
          break;
        case "00";
          $work_type = '装机';
          break;
        case "XJ";
          $work_type = '巡检';
          break;
      }
      return $work_type;
  }

//工作单查询
public function doQuery($data)
{
 
  if (!(isset($data['empid'])) || !(strlen($data['empid']))) {
    throw new Exception('用户工号不能为空', 500);
  }
  //装机
  if (isset($data['type']) && strlen($data['type'])) {
    switch ($data['type']) {
        //装机
      case '1';
        $where1 = "and substr(a.work_order_no,0,2)like '00%'";
        break;
        //维护
      case '2';
        $where1 = "and substr(a.work_order_no,0,2)like 'WH%'";
        break;
        //巡检
      case '3';
        $where1 = "and substr(a.work_order_no,0,2)like 'XJ%'";
        break;
        //任务数
      default:
        $where1 = '';
        break;
    }
  } else {
    $where1 = "";
  }

  if (isset($data['status']) && strlen($data['status'])) {
    switch ($data['status']) {
        //已派单
      case '1';
        $where2 = "and a.status = '1'";
        break;
        //已签收
      case '2';
        $where2 = "and a.status = '2'";
        break;
        //已回访
      case '3';
        $where2 = "and a.status = '3'";
        break;
        //已执行
      case '6';
        $where2 = "and a.status = '6'";
        break;
      default:
        $where2 = '';
        break;
    }
  } else {
    $where2 = "";
  }


  if (isset($data['attributes']) && strlen($data['attributes'])) {
    switch ($data['attributes']) {
        //加急
      case '1';
        $where3 = "and a.SPEED_UP = '1'";
        break;
        //自助
      case '2';
        $where3 = "and a.SELF_FLAG = '1'";
        break;
        //暂缓
      case '3';
        $where3 = "and a.status = '8'";
        break;
      default:
        $where3 = '';
        break;
    }
  } else {
    $where3 = "";
  }

  $plan_date = date("Ymd", time());
  //工作单时间
  if (isset($data['sender_date']) && strlen($data['sender_date'])) {
    $where4 = "and a.plan_date = '{$data['sender_date']}'";
  } else {
    $where4 = "and a.plan_date = '{$plan_date}'";
  }

  //工单号
  if (isset($data['work_order_no']) && strlen($data['work_order_no'])) {
    $where5 = "and a.work_order_no LIKE '%{$data['work_order_no']}%'";
  } else {
    $where5 = '';
  }

  //工单状态
  if (isset($data['flag']) && strlen($data['flag'])) {
    switch ($data['flag']) {
        //未完成
      case '1';
        $where6 = 'and work_date is null';
        break;
        //已完成
      case '2';
        $where6 = 'and work_date is not null';
        break;
        //任务数
      default:
        $where6 = '';
        break;
    }
  } else {
    $where6 = '';
  }
  $sql = "select 
  trim(a.work_order_no) work_order_no,
     b.mcht_name,
     trim(a.mcht_id) mcht_id,
     trim(a.term_no) term_no,
     a.sign_name,
     a.status,
     a.SPEED_UP,
     a.SELF_FLAG,
     a.transfer_flag,
     a.mcht_addr,
     c.transfer_status,
     to_char(to_date(a.work_date, 'YYYYMMDD'), 'YYYY-MM-DD') work_date,
     to_char(to_date(a.plan_date, 'YYYYMMDD'), 'YYYY-MM-DD') plan_date,
     a.plan_date as plan_date_str
  from WORKORDER a,mcht_info b ,pms_notice c 
    where 1=1
    and a.work_id = '{$data['empid']}'
    and a.mcht_id = b.mcht_id
    and a.work_order_no = c.work_order_no
    and a.status not in ('0','5','A')
    $where1 $where2  $where3 $where4 $where5 $where6
    order by a.work_date desc,a.speed_up,a.work_order_no";
  
//   $sql = "select 
//   trim(a.work_order_no) work_order_no,
//   a.work_id,
//   a.plan_date as plan_date_str,
//   a.sender_date,
//   a.status,
//   a.work_flag,
//   a.mcht_name,
//   a.addr,
//   a.linkman,
//   a.telno,
//   a.plan_train_num,
//   a.work_date,
//   a.sign_date,
//   a.sign_name,
//   a.paper_type,
//   a.device_type,
//   a.print_flag,
//   a.print_flag_name,
//   a.work_name,
//   a.mcht_addr,
//   a.work_flag_name,
//   a.deposit_flag,
//   a.city_name city,
//   a.install_flag,
//   a.note,
//   a.acq_inst_id,
//   a.acq_inst_id2,
//   a.params,
//   a.status_name,
//   a.acq_inst_name,
//   a.acq_inst_name2,
//   a.sender_name,
//   b.big_area_name,
//   a.gzdnr,
//   a.grade,
//   a.term_no,
//   a.para_name
// from v_workorder_1 a, big_area b
// where 1 = 1
// and a.work_id = '{$data['empid']}'

// and a.big_area_id2 = b.big_area_id(+)
//     $where1 $where2  $where3 $where4 $where5 $where6
//     order by a.work_date desc,a.speed_up,a.work_order_no";
    // print_r($sql);
    // die;
  $workorder = Db::query($sql);
  $sql1 = "
  select
      sum(case
            when a.work_date is not null then
             1
            else
             0
          end) finish,
      sum(case
            when a.work_date is null then
             1
            else
             0
          end) unfinish,
      sum(case
            when a.work_date is not null then
             1
            else
             0
          end + case
            when a.work_date is null then
             1
            else
             0
          end) sum
       from (SELECT * FROM  WORKORDER a  where a.work_id = '{$data['empid']}' and a.status not in ('0','5','A') $where4) a";

  $ret['sum'] = Db::query($sql1);
  $ret['list'] = [];
  if ($workorder) {
    $now_date = date("Ymd", time());
    foreach ($workorder as $k => $v) {
      $v['time_out'] = $v['plan_date_str'] < $now_date ? 1 : 0;
      $v['work_order_flag'] = $v['work_date'] == null ? "待完成" : "已完成";
      $work_order_no = substr($v['work_order_no'], 0, 2);
      $v['work_type'] = $this->work_order_type($work_order_no);
      //工单状态(0-待领取，1-已派单，2-已签收，3-已回访，4-已评审，5:已取消， 6：已出库 ，7:暂不装机，8-暂缓装机， 9-已入库 ，A-已作废，B-延期)
      switch ($v['status']) {
        case "B";
          $v['status'] = '已延期';
          $v['status_code'] = 'B';
          break;
        case "A";
          $v['status'] = '已作废';
          $v['status_code'] = 'A';
          break;
        case "9";
          $v['status'] = '已入库';
          $v['status_code'] = '9';
          break;
        case "8";
          $v['status'] = '暂缓装机';
          $v['status_code'] = '8';
          break;
        case "7";
          $v['status'] = '暂不装机';
          $v['status_code'] = '7';
          break;
        case "6";
          $v['status'] = '已出库';
          $v['status_code'] = '6';
          break;
        case "5";
          $v['status'] = '已取消';
          $v['status_code'] = '5';
          break;
        case "4";
          $v['status'] = '已评审';
          $v['status_code'] = '4';
          break;
        case "3";
          $v['status'] = '已回访';
          $v['status_code'] = '3';
          break;
        case "2";
          $v['status'] = '已签收';
          $v['status_code'] = '2';
          break;
        case "1";
          $v['status'] = '已派单';
          $v['status_code'] = '1';
          break;
        case "0";
          $v['status'] = '待领取';
          $v['status_code'] = '0';
          break;
        default:
          $v['status'] = '其他';
          $v['status_code'] = '';
      }
      array_push($ret['list'], $v);
    }
  }
  return $ret;
}

  //查询工作单详情
  public function getDetails($data)
  {
    $work_order_no = $data['work_order_no'];
    // $sql = "select * from VIEW_WORKORDER_DETAIL_LS WHERE work_order_no ='{$work_order_no}'";
    $sql = "select 
    a.sender_id,
    a.sender_name,
    a.plan_date,
    a.work_order_no,
    a.mcht_id,
    a.term_no,
    a.self_flag,
    a.status  as status_code,
    case
    when a.work_flag like '1%' then
     '装机'
    when a.work_flag like '__________1%' then
     '撤机'
    when substr(a.work_flag, 2, 2) || substr(a.work_flag, 7) like '%1%' then
     '维护'
    else
     '巡检'
  end work_flag_name,
  case
  when a.work_date is null then
   '未完成'
  when a.work_date is not null then
   '已完成'
  end work_order_flag,
  a.work_id,
  a.work_name,
  a.device_type,
    a.mcht_name2 as mcht_name,
    a.addr,
    a.addr2,
    a.linkman,
    a.mobile,
    a.term_type,
    a.term_inves,
    a.mcht_no,
    a.terminal_no,
    a.psam_no,
    c.device_no,
    e.deposit_no,
    e.deposit_amount,
    a.note,
    a.sales_manager_id,
    a.city,
    a.job_num,
    a.paper_type,
    a.work_flag,
    a.deposit,
    a.sales_manager_telno,
    a.expand_manager_id,
    a.expand_manager_telno,
    a.acq_inst_id,
    a.acq_inst_id2,
    a.acq_inst_name2,
    a.big_area_name2,
    a.big_area_name,
    a.paper_num,
    a.city_name,
    a.zjbh,
    a.jpbh,
    a.zjbh2,
    a.jpbh2,
    a.zjxh,
    a.jpxh,
    a.zjxh2,
    a.jpxh2,
    a.unionpay_app_open_flag,
    a.grade,
    a.visit_note,
    a.business_scope,
    a.cert_name,
    a.mcht_name3,
    a.install_date,
    a.acct_ins_name2,
    a.acct_ins_name4,
    a.fjxh,
    a.fjbh,
    a.fjxh2,
    a.fjbh2,
    a.install_flag,
    a.task_note
    from v_workorder a,
    term_info             b,
    devicelist            c,
    deviceinfo            d,
    virterm_deposit_info_view e
    where 1=1 
    and a.work_order_no = '{$work_order_no}'  
    and a.term_no = e.term_no(+)
    and a.term_no = b.term_no(+)
    and a.term_no = c.term_no(+)
    and c.device_id = d.device_id(+)";
   
    $res = Db::query($sql);
    if (count($res) !=0) {
      $term_no = trim($res['0']['term_no']);
      $mcht_id = trim($res['0']['mcht_id']);
    } else {
      throw new Exception("这个工作单出现关联数据错误，无法查看详情", '500');
    }
    
    // print_r($res);
    // die;
    $sql1 = "select 
    a.paramter_name,
    a.busi_type,
    a.term_no,
    a.mcht_name,
    a.mcht_no,
    a.terminal_no,
    a.psam_no,
    a.bind_telno,
    a.bill_id,
    a.settle_bank_name,
    a.settle_name,
    a.settle_pan,
    a.mcc,
    a.service_type
    from view_term_params a where 1=1 
    and a.term_no = '{$term_no}'  order by a.paramter_name";
    $res1 = Db::query($sql1);
   
    $sql2 = "select 
    a.service_type,
    a.signrate_id,
    a.start_date,
    a.end_date,
    a.settle_cycle_type,
    a.note,
    b.service_name,
    c.signrate_name,
    a.payer,
    a.kp_flag
    from virterm_info_fee a,service_type b,
    service_fee c where 1=1 and a.term_no = '{$term_no}'  
    and a.service_type = b.service_type and a.signrate_id = c.signrate_id";
    $res2 = Db::query($sql2);
    // print_r($res2);
    // die;
    if ($res!=null  && $res1!=null && $res2!=null) {
      $info = array_merge($res['0'],$res1['0'],$res2['0']);
    }
    $info = $res;
   
    $field =
      'work_order_no,
      filetype,
      file_name,
      file_url,
      create_date';

    $sql1 = "select $field  from workorder_es where work_order_no ='{$data['work_order_no']}' and filetype = '0'";
    $work_order_image = Db::query($sql1);
    $date = date('Ymd');
    $sql2 = "SELECT a.file_type, b.file_path, b.file_name, b.WORK_ORDER_NO, b.create_date
from (select file_type, max(create_date) create_date
      from (select *
              from (SELECT a.WORK_ORDER_NO,
                           a.filetype      as file_type,
                           a.file_name     as file_name,
                           a.file_url      as file_path,
                           a.create_date
                      from WORKORDER_ES a
                     where a.WORK_ORDER_NO in
                           (SELECT WORK_ORDER_NO
                              from WORKORDER
                             where mcht_id = '{$mcht_id}'
                             and work_date = '{$date}'))
             order by file_type, create_date desc)
     group by file_type) a,
   (SELECT a.WORK_ORDER_NO,
           a.filetype      as file_type,
           a.file_name     as file_name,
           a.file_url      as file_path,
           a.create_date
      from WORKORDER_ES a
     where a.WORK_ORDER_NO in
           (SELECT WORK_ORDER_NO from WORKORDER where mcht_id = '{$mcht_id}' and work_date = '{$date}')) b
where a.file_type = b.file_type
and a.create_date = b.create_date
and a.file_type not in '0'";

    $select3 = Db::query($sql2);
    $where1 = '';
    if (!$data['sign_name']) {
      $where1 = "and a.file_type <> '0'";
    }

    $sql3 = "SELECT a.file_type,b.file_path, b.file_name, b.WORK_ORDER_NO,b.create_date
from (select file_type, max(create_date) create_date
      from (select *
              from (SELECT a.WORK_ORDER_NO,
                           a.filetype      as file_type,
                           a.file_name     as file_name,
                           a.file_url      as file_path,
                           a.create_date
                      from WORKORDER_ES a
                     where a.WORK_ORDER_NO in
                           (SELECT WORK_ORDER_NO
                              from WORKORDER
                             where term_no = '{$term_no}'))
             order by file_type, create_date desc)
     group by file_type) a,
   (SELECT a.WORK_ORDER_NO,
           a.filetype      as file_type,
           a.file_name     as file_name,
           a.file_url      as file_path,
           a.create_date
      from WORKORDER_ES a
     where a.WORK_ORDER_NO in
           (SELECT WORK_ORDER_NO from WORKORDER where term_no = '{$term_no}')) b
where a.file_type = b.file_type
and a.create_date = b.create_date
and a.file_type not in '0' $where1";
    $images = Db::query($sql3);

    // $data['images'] = $images;
    $data['images'] = $select3;
    if ($work_order_image != null) {
      $data['images'][] = $work_order_image;
    }


    // print_r($data['images']);
    // die;
    //合并图片组
    foreach ($images as $k => $v) {
      $n = 0;
      $arr_type[] = $v['file_type'];
      foreach ($select3 as $kk => $vv) {
        if ($v['file_type'] == $vv['file_type']) {
          $n++;
        } else {
          continue;
        }
      }

      if ($n == 0) {
        $data['images'][] = $v;
      }
    }
    // foreach ($data as $k => $v) {
    //   print_r($v);
    //   die;
    // }
    // $sql1 = "select a.file_url,a.filetype
    //         from workorder_es a
    //                 where 1 = 1
    //                 and a.work_order_no = '{$work_order_no}'";
    $ret['info'] = Db::query($sql);
    $ret['info'] = $info;
    $ret['image'] = $data;
    return $ret;
  }

  //执行工作单
  public function toExecute(array $data = null)
  {

    $scrap_sql = "select a.status from workorder a where work_order_no='{$data['work_order_no']}'";
    $check_scrap = Db::query($scrap_sql);
    if ($check_scrap['0']['status'] == 'A') {
      throw new Exception("糟糕 ！ 这个工作单被作废了。", '500');
    }
    Db::startTrans(); //开启事务
    try {
      // print_r($data['images']);exit;
      //unset($data['images']);
      //dlog($data, '执行工作单-参数');
      $arr_url = [];
      if (count($data['images']) == 0) {
        throw new Exception("必须上传现场照片", '500');
      }

      foreach ($data['images'] as $k => $v) {
        dlog($v['file_type'], '执行工作单-参数');
        if (substr($data['work_order_no'], 0, 2) == 'WH' || substr($data['work_order_no'], 0, 2) == 'XJ') {
          if ($v['file_path'] == '') {
            if ($v['file_type'] == '6') {
              throw new Exception("维护或巡检必须上传门头照", '500');
            }
            continue; // 跳出本次循环
          }
        } else {
           //执行失败必须上传门头照
        if ($data['install_flag'] == 0) {
          if ($v['file_type'] == '6' && $v['file_path'] == '') {
            throw new Exception("工单执行失败必须上传门头照", '500');
          }
          continue; // 跳出本次循环
        }
          if ($v['file_path'] == '') {
            if ($v['file_type'] == '6') {
              throw new Exception("装机单必须上传门头照", '500');
            }
            if ($v['file_type'] == '7') {
              throw new Exception("装机单必须上传收银台图片", '500');
            }
            if ($v['file_type'] == '8') {
              throw new Exception("装机单必须上传内景图", '500');
            }
            if ($v['file_type'] == '10') {
              throw new Exception("装机单必须上传签购单", '500');
            }
            if ($v['file_type'] == '11') {
              throw new Exception("装机单必须上传商户签名", '500');
            }
            // continue; // 跳出本次循环
          }
        }
       
      }
      $path = config('outwork')['yyj_url'];
      $url = '/storage/admintwo/upload/';
      //上传新图片
      foreach ($data['images'] as $k => $v) {
        $base64_url = '';
        if (strpos($v['file_path'], 'base64') !== false) {
          $type = trim($v['file_type']) . '_';
          // if ($v['fPath'] != '') {
          $base64_url = base64_image_content($v['file_path'], $path, $url . $data['work_order_no'] . '/', $type);
          $arr_url[] = $base64_url;
          if (!$base64_url) {
            throw new Exception("图片保存失败", '500');
          }
          // }
          //保存图片
          $sql = "INSERT into workorder_es(work_order_no,file_url,filetype,file_name,create_id) 
          values('{$data['work_order_no']}','{$base64_url}','{$v['file_type']}','{$v['file_text']}','{$data['work_id']}')";
          $insert_images = Db::execute($sql);
          if (!$insert_images) {
            throw new Exception("图片保存失败1", '500');
          }
        }
      }

      $work_order_arr = [
        'linkman2' => $data['linkman2'] == null ? '' : $data['linkman2'],
        'mcht_addr2' => $data['mcht_addr2'] == null ? '' : $data['mcht_addr2'],
        'telno2' => $data['telno2'] == null ? '' : $data['telno2'],
        'install_flag' => $data['install_flag'],
        'task_note' => $data['task_note'],
        'longitude' => $data['longitude'],
        'latitude' => $data['latitude'],
        'work_date' => date('Ymd'),
        'work_time' => date('His'),
        'work_id' => $data['work_id'],
      ];

      foreach ($work_order_arr as $k => $v) {
        if ($v || $v == '0') {
          $value[] = $k . "='" . $v . "'";
        }
      }
      $value = implode(',', $value);
      $update_sql = "UPDATE WORKORDER set $value where work_order_no = '{$data['work_order_no']}'";
      $update_work_order = Db::execute($update_sql);

      //删除重复的图片类型
      $sql = "select * from WORKORDER_ES t where t.work_order_no = '{$data['work_order_no']}'
              and (t.filetype, t.create_date) not in
              (select filetype, max(create_date) create_date from (select * from (SELECT a.WORK_ORDER_NO,a.filetype,a.create_date
              from WORKORDER_ES a where a.WORK_ORDER_NO = '{$data['work_order_no']}')order by filetype, create_date desc)
              group by filetype)";
      $repeat_images = Db::query($sql);
      $delete_repeat_images = 1;
      if ($repeat_images != null) {
        foreach ($repeat_images as $k => $v) {
          if (file_exists($path . $v['file_url'])) {
            unlink($path . $v['file_url']);
          }
          $delete_repeat_images_sql = "delete from workorder_es where work_order_no = '{$data['work_order_no']}' and filetype = '{$v['filetype']}' and file_url = '{$v['file_url']}'";
          $delete_repeat_images = Db::execute($delete_repeat_images_sql);
        }
      }

      if (!$insert_images || !$update_work_order || !$delete_repeat_images) {
        throw new Exception("工作单执行失败", 500);
        dlog(Db::getLastSql());
      }
      Db::commit(); //提交事务

    } catch (\Throwable $th) {
      Db::rollback(); //回滚事务
      if ($arr_url) {
        foreach ($arr_url as $k => $v) {
          if (file_exists($path . $v)) {
            unlink($path . $v);
          }
        }
      }
      $arr = explode('(', $th->getMessage());
      dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
      throw new Exception("工作单执行失败：" . $arr[0], 500);
    }
  }

  //工单延期
  public function toPostpone(array $data = null)
  {
    //判断延期工单是否为装机工单并且是加急
    $date = date('Ymd', time());
    $sql = "select a.plan_date,a.speed_up,a.delay_times from workorder a where work_order_no='{$data['work_order_no']}'";
    $check = Db::query($sql);
    if (!$check) {
      throw new Exception('没有找到工作单', 500);
    }
    if (substr($data['work_order_no'], 0, 2) == '00' && $check['0']['plan_date'] == $date && $check['0']['speed_up'] == 1) {
      throw new Exception('当前工作单不允许延期，请尽快执行完成！', 500);
    }

    if ($check['0']['delay_times'] == 3) {
      throw new Exception('当前工单延期次数已达到3次，不能再延期了', 500);
    }

    if ($check['0']['delay_times'] == null) {
      $delay_times = 1;
    } else {
      $delay_times = $check['0']['delay_times'] + 1;
    }

    $update_sql = "UPDATE workorder SET delay_note = '{$data['delay_note']}',
    plan_date = '{$data['postpone_date']}',delay_times = '$delay_times',status = 'B'
     WHERE work_order_no='{$data['work_order_no']}'";

    $update = Db::execute($update_sql);
    if (!$update) {
      throw new Exception('工单延期失败', 500);
      dlog(Db::getLastSql() . "\n");
    }
  }

  //自助派单工作单作废
  public function toScrap(array $data = null)
  {
    $sql = "select a.self_flag,a.status,a.work_date,a.work_id from workorder a where 1=1 
    and work_order_no='{$data['work_order_no']}' and work_id='{$data['empid']}'";

    $check = Db::query($sql);
    if (!$check) {
      throw new Exception('没有找到工作单', 500);
    }
    if ($check['0']['self_flag'] != '1') {
      throw new Exception('当前工作单不是维护自助派单，不能作废！', 500);
    }
    if ($check['0']['status'] == 'A') {
      throw new Exception('当前工作单已经作废，请不要重复操作', 500);
    }

    $update_sql = "UPDATE workorder SET status = 'A' WHERE work_order_no='{$data['work_order_no']}'";
    $update = Db::execute($update_sql);
    if (!$update) {
      throw new Exception('自助派单作废失败', 500);
      dlog(Db::getLastSql() . "\n");
    }
  }

  //修改工作单内容
  public function doEdit(array $data = null)
  {
    dlog($data, 'edit_workorder-参数');
    $work_order_no = $data['work_order_no'];
    foreach ($data as $k => $v) {
      $val[] = $k . "='" . $v . "'";
    }

    $val = implode(',', $val);
    $sql = "UPDATE WORKORDER set $val where work_order_no='$work_order_no'";
    $update = Db::execute($sql);
    if (!$update) {
      throw new Exception('工作单内容修改失败', 500);
    }
  }

  //执行转单
  public function doTransfer(array $data = null)
  {
    $check_sql = "select * from pms_notice where work_order_no = '{$data['work_order_no']}'";
    $work_order_sql = "select a.work_order_no,a.mcht_addr,b.mcht_name from workorder a,mcht_info b
    where 1= 1
    and a.mcht_id = b.mcht_id
    and work_order_no = '{$data['work_order_no']}'";
    $work_order = Db::query($work_order_sql);
    if (!$work_order) {
      throw new Exception('没有找到对应工作单', 500);
    }
    $work_order_info = $work_order['0'];
    // $check_res = Db::query($check_sql);
    // if ($check_res) {
    //   throw new Exception('这个工作单已经转过了，别再转了！', 500);
    // }
    $sql = "select max(notice_id) notice_id from pms_notice";
    $notice_id = Db::query($sql)['0']['notice_id'];

    if ($notice_id == null) {
      $notice_id = 1;
    } else {
      $notice_id = intval($notice_id) + 1;
    }

    $work_order_type = substr($data['work_order_no'], 0, 2);
        switch ($work_order_type) {
          case "WH";
            $work_order_no_type = '维护';
            break;
          case "00";
            $work_order_no_type = '装机';
            break;
          case "XJ";
            $work_order_no_type = '巡检';
            break;
        }

    $sql = "insert into pms_notice  (
        notice_id,type,userid,work_order_no,content,
        send_flag,status,transfer,apply_time,
        transfer_status) 
        values
        ('{$notice_id}','1','{$data['userid']}','{$data['work_order_no']}','{$data['content']}',
        '0','0','{$data['transfer']}',to_char(sysdate,'YYYYMMDD'),
        '0')";

    dlog(Db::getLastSql(), '报错信息');
    $update = Db::execute($sql);
    if (!$update) {
      throw new Exception('转单请求提交失败', 500);
    }
    // // $key = '00000189';
    // Cache::lpush('key',1);
    // $a  = Cache::lpop('key');
    // print_r($a);
    // die;

    // $key = '00852';
    // $message = [
    //   'uid' => '00852',
    //   // 'uid' => '00000189',
    //   'work_order_no' => $data['work_order_no'],
    //   'work_order_type' => $work_order_no_type,
    //   'mcht_name' => $work_order_info['mcht_name'],
    //   'mcht_addr' => $work_order_info['mcht_addr'],
    //   'transfer' => $data['full_name']
    // ];
    // // print_r($message);
    // // die;
    // $value = serialize($message);
    // Cache::store('redis')->set($key,$value,5);
  }

  //转单待处理列表
  public  function getPendList($data)
  {
    $sql = "select a.notice_id,
       a.type,
       a.userid,
       b.full_name       as catch_name,
       d.work_order_no,
         case
         when d.work_flag like '1%' then
          '装机派单'
         when d.work_flag like '__________1%' then --v_workorder_1
          '撤机派单'
         when substr(d.work_flag, 2, 2) || substr(d.work_flag, 7) like '%1%' then
          '维护派单'
         else
          '巡检派单'
       end work_flag_name,
       e.mcht_name,
       d.mcht_addr,
       a.content,
       a.transfer,
       c.full_name       as transfer_name,
        to_char(to_date(a.apply_time, 'YYYYMMDD'), 'YYYY-MM-DD') apply_time,
       a.transfer_status
  from pms_notice a, userinfo b, userinfo c,workorder d,mcht_info e
 where 1 = 1
   and a.userid = b.userid
   and a.transfer = c.userid
   and a.work_order_no = d.work_order_no
   and d.mcht_id = e.mcht_id
   and a.userid = '{$data['userid']}'
   and a.type = '1'
   and a.status <> '2'
   and a.transfer_status = '0'
 order by a.apply_time desc";
    $list = Db::query($sql);
    if ($list == null) {
      throw new Exception('没有待处理的转单请求', '500');
    }
    return $list;
  }

  //处理转单请求
  public function doDealWith(array $data = null)
  {
    $check_sql = "select * from pms_notice where work_order_no = '{$data['work_order_no']}'";
    $check_res = Db::query($check_sql);

    // if ($check_res) {
    //   if ($check_res['0']['transfer_status'] != 0) {
    //     throw new Exception('这个工作单已经处理过了', 500);
    //   }
    // } else {
    //   throw new Exception('没有找到当前工作单', 500);
    // }
    //接收
    if ($data['type'] == 1) {
      //查询工作单是否有对应的押金条
      $sql = "select a.deposit_no from deposit_info a, workorder b
     where b.work_order_no = '{$data['work_order_no']}' 
     and a.term_no = b.term_no
     and a.recipients_id = b.work_id";

      //查询工作单是否有对应的发票
      $sql1 = "select a.invoice_no
      from machine_invoice a, workorder b
     where b.work_order_no = '{$data['work_order_no']}'
       and a.term_no = b.term_no(+)
       and a.tiket_bearer = trim(b.work_id)";

      $check_deposit  = Db::query($sql);
      $check_invoice = Db::query($sql1);
      if ($check_deposit) {
        $deposit_no = $check_deposit['0']['deposit_no'];
        $update_deposit_sql = "UPDATE deposit_info set recipients_id = '{$data['empid']}' where deposit_no='{$deposit_no}'";
      } else {
        unset($update_deposit_sql);
      }
      if ($check_invoice) {
        $invoice_no = $check_invoice['0']['invoice_no'];
        $update_invoice_sql = "UPDATE machine_invoice set tiket_bearer = '{$data['empid']}' where invoice_no='{$invoice_no}'";
      } else {
        unset($update_invoice_sql);
      }

      $update_work_order_sql = "UPDATE WORKORDER set work_id = '{$data['empid']}',transfer_flag = '1' where work_order_no='{$data['work_order_no']}'";
      $update_notice_sql = "UPDATE PMS_NOTICE set transfer_status = '1',decide_time = to_char(sysdate,'YYYYMMDD')  where work_order_no='{$data['work_order_no']}'";
      Db::startTrans();
      try {
        if (isset($update_deposit_sql)) {
          Db::execute($update_deposit_sql);
        }
        if (isset($update_invoice_sql)) {
          Db::execute($update_invoice_sql);
        }
        Db::execute($update_work_order_sql);
        Db::execute($update_notice_sql);
        Db::commit();
      } catch (\Throwable $th) {
        Db::rollback();
        $arr = explode('(', $th->getMessage());
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        throw new Exception("工作单执行失败：" . $arr[0], 500);
      }
      $result = '已接受';
    } elseif ($data['type'] == 2) {
      // if ($check_res['0']['transfer_status'] != 0) {
      //   throw new Exception('这个工作单已经处理过了', 500);
      // }

      $update_notice_sql = "UPDATE PMS_NOTICE set transfer_status = '2',decide_time = to_char(sysdate,'YYYYMMDD')  where work_order_no='{$data['work_order_no']}'";
      $add_notice =  Db::execute($update_notice_sql);
      if (!$add_notice) {
        throw new Exception('转单处理失败', 500);
        // dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
      }
      $result = '已拒绝';
    }
    $redis = new Redis();
    $work_order_no = substr($data['work_order_no'], 0, 2);
    $work_order_type = $this->work_order_type($work_order_no);
    $message['message'] = [
      'time'=> date("Y-m-d H:i",time()),
      'full_name'=>$data['full_name'],
      'work_order_type'=>$work_order_type,
      'work_order_no'=>$data['work_order_no'],
      'result'=>$result,
    ];
    $key = $data['transfer'];
    $total = $redis->lLen($key);
    if ($total == 0) {
      foreach ($message as $k => $v){
        $value  = serialize($v);
        $redis->lPush($key,$value);
      }
    }
  }

  //生成
}
