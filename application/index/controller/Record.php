<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Mine as Model;


class Record extends Controller
{
  //工作单列表
  public function record_mcht()
  {
    if (request()->isPost()) {

      try {
        dlog(input(), 'lb_workorder-参数');
        $para= input('param.');
        print_r($para);

        //执行人员
        $work_id = input('param.work_id');

        //查询商户号是否存在
        $sql_cnt= "select count(1) cnt from MCHT_BUSINESS_ADJ t where trim(t.mcht_no)='403590198177'";
        $res_cnt = Db::query($sql_cnt)[0];

        print_r($res_cnt);
        exit;

        //二级收单机构
        // $acq_inst_id2 = input('param.acq_inst_id2');
        $acq_inst_id2 = "J3095510";
        $userid='HN0048';

        $sql_serial= "update ACQUIRER_INSTITUTION2 t set t.serial = t.serial + 1 where t.acq_inst_id2 = '{$acq_inst_id2}'";
        $res_serial = Db::execute($sql_serial);

        //生成档案号
        $fn_sql= "select trim(t.prefix) || lpad(trim(t.serial), 6, '0') fn,acq_inst_id,acq_inst_name2 from ACQUIRER_INSTITUTION2 t where t.acq_inst_id2 = '{$acq_inst_id2}'";
        $res_mcht = Db::query($fn_sql)[0];
        //print_r($res_fn);
        

        $sql_da= "insert into mcht_info( 
            mcht_id,
            mcht_name,
            brief_name,
            addr,
            city,
            linkman,
            mobile,
            license,
            pan_name,
            bank_name,
            cert_type,
            sales_manager_id,
            expand_id,
            expand_manager_id,
            opt_flag,
            opt_id,
            expand_manager_telno,
            province,
            district,
            pro_svr_cd,
            acq_inst_id,
            acq_inst_id2,
            acct_ins_id,
            project_belong)
        values
          ('{$res_mcht["fn"]}',
          mcht_name,
          mcht_name,
          addr,
          city,
          linkman,
          mobile,
          license,
          pan_name,
          bank_name,
          '01',
          '{$userid}',
          '{$res_mcht["acq_inst_id"]}',
          expand_manager_id,
          '10',
          '{$userid}',
          province,
          district,
          '49914410',
          '{$res_mcht["acq_inst_id"]}',
          '{$acq_inst_id2}',
          '{$res_mcht["acq_inst_id"]}',
          '{$res_mcht["acq_inst_name2"]}'.'报备')";
        
        $sql_para= "insert into mcht_business_adj
        (mcht_no,
         mcht_name,
         linkman,
         telno,
         mcht_id,
         settle_bank_name,
         settle_name,
         opt_flag)
      values
        ('{$res_mcht["fn"]}',
         mcht_name,
         linkman,
         mobile,
         mcht_id,
         settle_bank_name,
         linkman,
         '10')";
        
        exit;

      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json(['code' => '100','msg' => '执行失败' . $arr[0]]);
      }
    }
  }

}
