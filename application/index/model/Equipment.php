<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;

class Equipment extends Model
{

    //待领取设备列表
    public function getList($data)
    {
        if (isset($data['key_word']) && strlen($data['key_word'])) {
            $where1 = "and j.work_order_no like '%{$data['key_word']}%'";
        } else {
            $where1 = '';
        }

        if (isset($data['flag']) && strlen($data['flag'])) {
            if ($data['flag'] == 1) {
                $where3 = "and a.op_flag = 'F'";
            } else {
                $where3 = "and a.op_flag = '0'";
            }
        } else {
            $where3 = '';
        }
    //     case
    //     when d.receive_time is null then
    //         ''
    // else
    //     to_char(to_date(d.receive_time, 'YYYYMMDD'), 'YYYY-MM-DD') 
    // end receive_time,
        $sql = "
        select 
        trim(a.depot_opr_no) depot_opr_no,
        a.device_no,
        c.device_type,
        d.receive_time,
        f.mcht_name,
        k.deposit_no,
        e.addr,
        h.para_name    op_flag,
        j.work_order_no,
        case
        when a.op_flag ='F' then
            '1'
        else
        '2'
  end type,
  case
  when substr(j.work_order_no, 0, 2) like '00%' then
      '装机'
  when substr(j.work_order_no, 0, 2) like 'WH%' then
      '维护'
  when substr(j.work_order_no, 0, 2) like 'XJ%' then
      '巡检'
  else
       '其他'
  end work_type
   from depotoprlog    a,
        devicelist     b,
        deviceinfo     c,
        depotoprrecode d,
        para           g,
        virterm_info   e,
        mcht_info      f,
        para           h,
        mview_para_zdtzf i,
        workorder       j,
        deposit_info    k
  where 1=1
  $where1 $where3
  and a.device_no = b.device_no
  and j.work_id='{$data['empid']}'
  and j.status in ('1','6','9')
    and b.device_id = c.device_id
    and a.term_no = k.term_no(+)
    and a.depot_opr_no = d.depot_opr_no
    and a.term_no = j.term_no(+)
    and g.type_id = '01'
    and c.device_flag = g.para_code
    and h.type_id = '02'
    and a.op_flag = h.para_code
    and a.term_no = e.term_no(+)
    and e.mcht_id = f.mcht_id(+)
    and b.term_inves = rtrim(i.para_code(+))";
       
        $res = Db::query($sql);
     
        if ($res == null) {
            if (isset($data['key_word']) && strlen($data['key_word'])) {
                $where1 = "and a.device_no like '%{$data['key_word']}%'";
            } else {
                $where1 = '';
            }


            if (isset($data['flag']) && strlen($data['flag'])) {
              if ($data['flag'] == 1) {
                  $where3 = "and a.op_flag = 'F'";
              } else {
                  $where3 = "and a.op_flag = '0'";
              }
          } else {
              $where3 = '';
          }

            $sql = "
            select 
            trim(a.depot_opr_no) depot_opr_no,
            a.device_no,
            c.device_type,
            d.receive_time,
            f.mcht_name,
            k.deposit_no,
            e.addr,
            h.para_name    op_flag,
            j.work_order_no, 
        case
        when a.op_flag ='F' then
            '1'
    else
     '2'
      end type,
      case
      when substr(j.work_order_no, 0, 2) like '00%' then
          '装机'
      when substr(j.work_order_no, 0, 2) like 'WH%' then
          '维护'
      when substr(j.work_order_no, 0, 2) like 'XJ%' then
          '巡检'
      else
           '其他'
      end work_type
       from depotoprlog    a,
            devicelist     b,
            deviceinfo     c,
            depotoprrecode d,
            para           g,
            virterm_info   e,
            mcht_info      f,
            para           h,
            mview_para_zdtzf i,
            workorder       j,
            deposit_info    k
      where 1=1
      $where1 $where3
      and a.device_no = b.device_no
      and j.work_id='{$data['empid']}'
      and j.status in ('1','6','9')
        and b.device_id = c.device_id
        and a.term_no = k.term_no(+)
        and a.depot_opr_no = d.depot_opr_no
        and a.term_no = j.term_no(+)
        and g.type_id = '01'
        and c.device_flag = g.para_code
        and h.type_id = '02'
        and a.op_flag = h.para_code
        and a.term_no = e.term_no(+)
        and e.mcht_id = f.mcht_id(+)
        and b.term_inves = rtrim(i.para_code(+))";
        $res = Db::query($sql);
        }
       $new_list = [];
        foreach ($res as $k => $v){
          $v['receive_time'] = $v['receive_time'] == null?'': date("Y-m-d H:i:s", strtotime($v['receive_time']));
          $new_list[]=$v;
        }
        return $new_list;
    }

    //领取设备
    public function getEquipment($data)
    {
     
        // $data['depot_opr_no'] = ['00003538', '00007524', '00007524'];
        if (is_array($data['depot_opr_no'])) {
          foreach ($data['depot_opr_no'] as $k => $v) {
            $val[] =  "'" . $v . "'";
        }
        $depot_opr_no = implode(',', $val);
        }else{
          $depot_opr_no = $data['depot_opr_no'];
          $depot_opr_no = "'".$depot_opr_no."'";
        }
    
     
        $check_sql = "select * from depotoprrecode where depot_opr_no in($depot_opr_no)";
        
        $check = Db::query($check_sql);
       
        if ($check != null) {
            foreach ($check as $k => $v) {
              $v['receive_time'] = trim( $v['receive_time']);
                if ($v['receive_time'] != '') {
                    throw new Exception("该设备已经被领取", 500);
                }
            }
        }
        $date = date("YmdHis",time());
        $update_sql = "update depotoprrecode set hand_id = '{$data['empid']}', receive_time = '$date' where depot_opr_no in ($depot_opr_no)";
        // print_r($update_sql);
        // die;
        $update_sql1 = "update depotoprlog set op_flag = '0' where depot_opr_no in ($depot_opr_no)";
        Db::startTrans();
        try {
            Db::execute($update_sql);
            Db::execute($update_sql1);
            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            $arr = explode('(', $th->getMessage());
            dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
            throw new Exception("设备领取失败" . $arr[0], 500);
        }
    }
}
