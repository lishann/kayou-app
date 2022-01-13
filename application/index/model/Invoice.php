<?php

namespace app\index\model;

use think\Db;
use think\Model;
use Exception;

class Invoice extends Model
{
    //获取发票列表
    function getInvoiceList($data)
    {
      //已领取/未领取只显示押金条
      //全部显示押金条和服务费
        if (isset($data['invoice_no']) && strlen($data['invoice_no'])) {
            $where1 = "and a.deposit_no like '%{$data['invoice_no']}%'";
        } else {
            $where1 = "";
        }
        if (isset($data['status']) && strlen($data['status'])) {
            $where2 = "and a.status = '{$data['status']}'";
          } else {
            //$where2 = "and a.status in('0','4')";
            $where2 = "";
        }
        if ($where2 =='' && $where1 == '') {
          $deposit_sql  = "select b.work_order_no,
         c.mcht_name,
         a.deposit_no,
         a.deposit_amount,
         a.create_note,
         a.yjt_type,
         a.status,
         trim(a.recipients_dt) recipients_dt,
         trim(a.create_dt) create_dt
         from deposit_info a, workorder b, mcht_info c
         where 1 = 1
         and a.status in('0','4')
         and a.RECIPIENTS_ID = '{$data['empid']}'
         and a.term_no = b.term_no
         and b.mcht_id = c.mcht_id";
         $list = Db::query($deposit_sql);
       
         $new_list = [];
         if ($list != null) {
             foreach ($list as $k => $v) {
              $v['recipients_dt'] = $v['recipients_dt'] == null?'': date("Y-m-d", strtotime($v['recipients_dt']));
              $v['create_dt'] = $v['create_dt'] == null?'': date("Y-m-d", strtotime($v['create_dt']));
              $v['type'] = 1;
                array_push($new_list, $v);
             }
            
          //  /  return $new_list;
         }
       
         $service_sql = "select 
         d.mcht_name,
         e.service_name,
         to_char(to_date(a.invoice_date, 'YYYY/MM/DD'), 'YYYY-MM-DD') invoice_date,
         a.invoice_no,
         a.service_charge,
         nvl(h.name, a.tiket_bearer) tiket_bearer ,
         a.invoice_note
       from service_invoice             a,
         term_info                   c,
         mcht_info                   d,
         virterm_info                f,
         area                        ff,
         service_type                e,
         service_receiv              g,
         ky_user                     h,
         ky_user                     i,
         ky_user                     j
       where 1=1
         and a.tiket_bearer = '{$data['empid']}'
         $where1  $where2
         and a.term_no = c.term_no(+)
         and a.term_no = f.term_no(+)
         and f.mcht_id = d.mcht_id(+)
         and f.area = ff.area_id(+)
         and a.service_type = e.service_type(+)
         and a.invoice_no = g.invoice_no(+)
         and rtrim(a.tiket_bearer) = rtrim(h.userid(+))
         and rtrim(g.payee) = rtrim(i.userid(+))
         and rtrim(a.marketer) = rtrim(j.userid(+))";
          $list2 = Db::query($service_sql);
          $new_list1 = [];
          if ($list2 != null) {
              foreach ($list2 as $k => $v) {
                  $v['type'] = "2";
                  array_push($new_list1, $v);
              }
          }
          // print_r($new_list1);
          // die;
          if (count($new_list) != 0 && count($new_list1) != 0){
            $res = array_merge($new_list, $new_list1);
          }else{
            $res = [];
          }
          
        }elseif($where1 =='' && $where2 !=''){
          $deposit_sql  = "select b.work_order_no,
          c.mcht_name,
          a.deposit_no,
          a.deposit_amount,
          a.create_note,
          a.yjt_type,
          a.status,
          trim(a.recipients_dt) recipients_dt,
          trim(a.create_dt) create_dt
          from deposit_info a, workorder b, mcht_info c
         where 1 = 1
         $where1 $where2
         and a.RECIPIENTS_ID = '{$data['empid']}'
         and a.term_no = b.term_no
         and b.mcht_id = c.mcht_id";
        //  print_r($deposit_sql);
        //  die;
         $list = Db::query($deposit_sql);
        
         $res = [];
         if ($list != null) {
             foreach ($list as $k => $v) {
               $v['recipients_dt'] = $v['recipients_dt'] == null?'': date("Y-m-d", strtotime($v['recipients_dt']));
               $v['create_dt'] = $v['create_dt'] == null?'': date("Y-m-d", strtotime($v['create_dt']));
               $v['type'] = 1;
                 array_push($res, $v);
             }
            
          //  /  return $new_list;
         }
        //  else{
        //   $where1 = "and a.invoice_no like '%{$data['invoice_no']}%'";
        //   $service_sql = "select 
        //   d.mcht_name,
        //   e.service_name,
        //   to_char(to_date(a.invoice_date, 'YYYY/MM/DD'), 'YYYY-MM-DD') invoice_date,
        //   a.invoice_no,
        //   a.service_charge,
        //   nvl(h.name, a.tiket_bearer) tiket_bearer ,
        //   a.invoice_note
        // from service_invoice             a,
        //   term_info                   c,
        //   mcht_info                   d,
        //   virterm_info                f,
        //   area                        ff,
        //   service_type                e,
        //   service_receiv              g,
        //   ky_user                     h,
        //   ky_user                     i,
        //   ky_user                     j
        // where 1=1
        //   and a.tiket_bearer = '{$data['empid']}'
        //   $where1
        //   and a.term_no = c.term_no(+)
        //   and a.term_no = f.term_no(+)
        //   and f.mcht_id = d.mcht_id(+)
        //   and f.area = ff.area_id(+)
        //   and a.service_type = e.service_type(+)
        //   and a.invoice_no = g.invoice_no(+)
        //   and rtrim(a.tiket_bearer) = rtrim(h.userid(+))
        //   and rtrim(g.payee) = rtrim(i.userid(+))
        //   and rtrim(a.marketer) = rtrim(j.userid(+))";
        //    $list2 = Db::query($service_sql);
        //    $res = [];
        //    if ($list2 != null) {
        //        foreach ($list2 as $k => $v) {
        //            $v['type'] = "2";
        //            array_push($res, $v);
        //        }
        //    }
        //  }
        }
        if ($where1 != '' && $where2 =='') {
          $deposit_sql  = "select b.work_order_no,
          c.mcht_name,
          a.deposit_no,
          a.deposit_amount,
          a.create_note,
          a.yjt_type,
          a.status,
          trim(a.recipients_dt) recipients_dt,
          trim(a.create_dt) create_dt
          from deposit_info a, workorder b, mcht_info c
         where 1 = 1
         $where1 $where2
         and a.RECIPIENTS_ID = '{$data['empid']}'
         and a.term_no = b.term_no
         and b.mcht_id = c.mcht_id";
        //  print_r($deposit_sql);
        //  die;
         $list = Db::query($deposit_sql);
        
         $res = [];
         if ($list != null) {
             foreach ($list as $k => $v) {
               $v['recipients_dt'] = $v['recipients_dt'] == null?'': date("Y-m-d", strtotime($v['recipients_dt']));
               $v['create_dt'] = $v['create_dt'] == null?'': date("Y-m-d", strtotime($v['create_dt']));
               $v['type'] = 1;
                 array_push($res, $v);
             }
            
          //  /  return $new_list;
         }else{
          $where1 = "and a.invoice_no like '%{$data['invoice_no']}%'";
          $service_sql = "select 
          d.mcht_name,
          e.service_name,
          to_char(to_date(a.invoice_date, 'YYYY/MM/DD'), 'YYYY-MM-DD') invoice_date,
          a.invoice_no,
          a.service_charge,
          nvl(h.name, a.tiket_bearer) tiket_bearer ,
          a.invoice_note
        from service_invoice             a,
          term_info                   c,
          mcht_info                   d,
          virterm_info                f,
          area                        ff,
          service_type                e,
          service_receiv              g,
          ky_user                     h,
          ky_user                     i,
          ky_user                     j
        where 1=1
          and a.tiket_bearer = '{$data['empid']}'
          $where1
          and a.term_no = c.term_no(+)
          and a.term_no = f.term_no(+)
          and f.mcht_id = d.mcht_id(+)
          and f.area = ff.area_id(+)
          and a.service_type = e.service_type(+)
          and a.invoice_no = g.invoice_no(+)
          and rtrim(a.tiket_bearer) = rtrim(h.userid(+))
          and rtrim(g.payee) = rtrim(i.userid(+))
          and rtrim(a.marketer) = rtrim(j.userid(+))";
           $list2 = Db::query($service_sql);
           $res = [];
           if ($list2 != null) {
               foreach ($list2 as $k => $v) {
                   $v['type'] = "2";
                   array_push($res, $v);
               }
           }
         }
        }
    
      return $res;
       
    
  }

    //领取发票
    public function doReceive(array $data = null)
    {
    
      if (is_array($data['deposit_no'])) {
        foreach ($data['deposit_no'] as $k => $v) {
          $val[] =  "'" . $v . "'";
      }
      $deposit_no = implode(',', $val);
      }  else{
        $deposit_no = $data['deposit_no'];
        $deposit_no = "'".$deposit_no."'";
      }
       
        $check_sql = "select * from deposit_info where deposit_no in($deposit_no)";

        $check = Db::query($check_sql);
        if ($check == null) {
            throw new Exception("没有找到押金条", 500);
        }
        foreach ($check as $k => $v) {
            if ($v['status'] != 4) {
                throw new Exception("有押金条已被领取", 500);
            }
        }

        $check_recipients_id = trim($check['0']['recipients_id']);
        $empid = trim($data['empid']);
        if ($check_recipients_id != $empid) {
            throw new Exception("当前押金条的领用人不是你", 500);
        }
        $receive_sql = "update deposit_info set status ='0',RECIPIENTS_DT = TO_CHAR(SYSDATE,'YYYYMMDD') where deposit_no in ($deposit_no)";
        $update = Db::execute($receive_sql);
        if (!$update) {
            throw new Exception("押金条领取失败", 500);
        }
    }
}
