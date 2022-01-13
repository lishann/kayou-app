<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;

class SelfDispatch extends Model
{

    //获取自助派单商户列表
    public function getMerchantInfoList($data)
    {
        //终端编号
        if (isset($data['keyword']) && strlen($data['keyword'])) {
            $where1 = "and t.terminal_no like '%{$data['keyword']}%'";
            $where2 = "and t.mcht_no like '%{$data['keyword']}%'";
            $where3 = "and f.device_no like '%{$data['keyword']}%'";
        } else {
            return [];
        }

        $sql = $this->getSql($where1);
        $res = Db::query($sql);
        if ($res != null) {
            return $res;
        }
        $sql1 = $this->getSql($where2);
        $res = Db::query($sql1);
        if ($res != null) {
            return $res;
        }
        $sql2 = $this->getSql($where3);

        $res = Db::query($sql2);
        if ($res != null) {
            return $res;
        }

        return $res;
    }

    //自助派单查询
    public function getSql($where)
    {
        "select * from (select x.*,rownum rn from (select a.mcht_id,b.mcht_name,a.speed_up,a.device_flag,a.deposit,b.bank_name,b.pan_name,b.pan,a.linkman,a.telno,d.city_name   city,a.addr,a.term_no,i.para_name   device_type,a.area,c.area_name,b.grade from virterm_info a,mcht_info b,area c,city d,para i where 1=1  and exists (select 1 from terminals_all a2 where a2.term_no = a.term_no and a2.terminal_no = 'A0008055') and a.mcht_id = b.mcht_id and a.area = c.area_id(+) and b.province = d.province(+) and b.city = d.city(+) and b.district = d.district(+) and a.device_type = i.para_code(+) and '03' = i.type_id(+) and a.device_flag in ('2', '4')) x) where rn <= 1*10 and rn > (1-1)*10
        ";
        $field = "             a.mcht_id,
                               b.mcht_name,
                               a.speed_up,
                               a.device_flag,
                               a.deposit,
                               b.bank_name,
                               b.pan_name,
                               b.pan,
                               a.linkman,
                               a.telno,
                               d.city_name   city,
                               a.addr,
                               trim(a.term_no) term_no,
                               i.para_name   device_type,
                               a.area,
                               c.area_name,
                               b.grade,
                               f.device_no,
                               t.mcht_no,
                               t.terminal_no";
        $sql = "
        select *
          from (select x.*, rownum rn
                  from (select $field
                          from virterm_info a, mcht_info b, area c, city d, para i,devicelist f,terminals t
                         where 1 = 1
                         $where
                           and a.mcht_id = b.mcht_id
                           and a.area = c.area_id(+)
                           and b.province = d.province(+)
                           and b.city = d.city(+)
                           and b.district = d.district(+)
                           and a.device_type = i.para_code(+)
                           and a.mcht_id = f.mcht_id(+)
                           and t.term_no = a.term_no(+)
                           and '03' = i.type_id(+)
                           and a.device_flag in ('2', '4')) x)
         where rn <= 1 * 10
           and rn > (1 - 1) * 10";
        return $sql;
    }

    //获取自助派单详情
    public function getSelfDetails()
    {
        //     $field = 'trim(a.mcht_id) mcht_id,
        //     trim(b.mcht_name) mcht_name,
        //     trim(t.terminal_no) terminal_no,
        //     trim(a.term_no) term_no,
        //     trim(f.device_no) device_no,
        //     df.device_type,
        //     trim(a.linkman) linkman,
        //     trim(a.addr) addr,
        //     acq.acq_inst_name,
        //     t.mcht_no,
        //     term_view.paramter_name,
        //     ti.psam_no,
        //     trim(t.mobile)mobile';
        //     $sql1 = "select *
        //     from (select x.*, rownum rn
        //             from (select 
        //             $field
        //                     from virterm_info          a,
        //                          mcht_info             b,
        //                          area                  c,
        //                          city                  d,
        //                          para                  i,
        //                          terminals             t,
        //                          devicelist            f,
        //                          acquirer_institution  acq,
        //                          VIEW_TERM_PARAMS_LIST term_view,
        //                          deviceinfo            df,
        //                          term_info             ti
        //                    where 1 = 1
        //                    $where
        //                      and a.mcht_id = b.mcht_id(+)
        //                      and a.term_no = ti.term_no(+)
        //                      and f.device_id = df.device_id(+)
        //                      and a.term_no = term_view.term_no(+)
        //                      and b.acq_inst_id = acq.acq_inst_id(+)
        //                      and a.term_no = f.term_no(+)
        //                      and a.term_no = t.term_no(+)
        //                      and a.area = c.area_id(+)
        //                      and b.province = d.province(+)
        //                      and b.city = d.city(+)
        //                      and b.district = d.district(+)
        //                      and a.device_type = i.para_code(+)
        //                      and '03' = i.type_id(+)
        //                      and a.device_flag in ('2', '4')) x)
        //    where rn <= 1 * 10";
    }

    //提交自助派单
    public function doDispatch($data)
    {

        $date = date("Ymd", time());
        //获取系统今日自助派单的数量
        $sql = "select count(self_flag)as count from workorder where self_flag ='1' and sender_date = '$date'";
        $self_count = Db::query($sql);

        // if ($self_count['0']['count'] == 5) {
        //     throw new Exception('今日系统自助派单次数已经用完，明天再来吧', 500);
        // }

        $sql1 = "select a.sender_date, a.work_order_no from workorder a where a.term_no ='{$data['term_no']}'";
        $check = Db::query($sql1);
        if ($check != null) {
          $now_date = date("Ymd",time());
          $work_order_no = substr($check['0']['work_order_no'], 0, 2);
          if ($check['0']['sender_date'] == $now_date && $work_order_no == 'WH'){
            throw new Exception('该商户今日已派过维护单，请不要重复操作', '500');
          }
        }

        //获取到当前系统最大维护类型的工作单号并+1
        $sql2 = "select trim(prefix) prefix,trim(to_char(serial,'099999')) serial from work_order_no where type='3'";
        $work_order_no = Db::query($sql2);
        $work_order_no = $work_order_no['0']['serial'] + 1;

        $max_work_order_no  = date("ym", time()) . $work_order_no;

        $sql3 = "select mcht_id,linkman,telno,speed_up,mcht_addr from view_virterm_info where 1=1 and term_no='{$data['term_no']}'";
        $virterm_info = Db::query($sql3);
        if ($virterm_info == null) {
            throw new Exception('没有查到要插入到工作单的商户信息', 500);
        }
        $virterm = $virterm_info['0'];

        //将数据插入到工作单表
        // if ($data['flag'] == 1) {
        $insert_sql = "insert into workorder a (
        work_order_no,mcht_id,term_no,work_flag,paper_num,u_signs,work_id,plan_train_num,
        mcht_addr,mobile,telno,speed_up,linkman,note,pro_svr_cd,status,sender_id,
        sender_date,plan_date,self_flag) 
        values
        ('WH$max_work_order_no','{$virterm['mcht_id']}','{$data['term_no']}','0010000000000','','','{$data['empid']}','',
        '{$virterm['mcht_addr']}','','','0','{$virterm['linkman']}','{$data['note']}','','1','{$data['empid']}',
        to_char(sysdate,'YYYYMMDD'),{$data['plan_date']},'1')";

        // } else {

        //         $original_date = date('Ymd', time());
        //         $insert_sql = "insert into workorder a (
        //     work_order_no,mcht_id,term_no,work_flag,paper_num,u_signs,work_id,plan_train_num,
        //     mcht_addr,mobile,telno,speed_up,linkman,note,pro_svr_cd,status,sender_id,
        //     sender_date,plan_date,self_flag,delay_note) 
        //     values
        //     ('WH$max_work_order_no','{$virterm['mcht_id']}','{$data['term_no']}','0010000000000','','','{$data['empid']}','',
        //     '{$virterm['mcht_addr']}','','','0','{$virterm['linkman']}','','','B','{$data['empid']}',
        //     to_char(sysdate,'YYYYMMDD'),{$data['delay_time']},'0','原计划时间$original_date ；延期原因：{$data['delay_note']}')";
        //     }

        // var_dump($max_work_order_no);
        // die;

        // $update_work_order = intval($work_order_no['0']['serial']);
        // print_r($work_order_no);
        // die;
        //更新虚拟终端表数据
        $update_sql = "update virterm_info set device_flag='4' where 1=1 and term_no='{$data['term_no']}' and device_flag in('2','4')";
        $update_sql1 = "update work_order_no set serial='$work_order_no' where type='3'";
        Db::startTrans();
        try {
            Db::execute($insert_sql);
            Db::execute($update_sql);
            Db::execute($update_sql1);
            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
            throw new Exception('工单新增失败', '500');
        }
        $sql3 = "select max(work_order_no)as work_order_no from workorder where work_order_no like '%WH%'";
        $you_work_order_no = Db::query($sql3);
        return $you_work_order_no;
    }

    //执行报废
    public function toScrap(array $data = null)
    {
        # code...
    }
}
