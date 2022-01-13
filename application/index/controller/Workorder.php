<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Workorder as ModelWorkorder;

class Workorder extends Controller
{

  //我的工作单统计
  function mine_sumary()
  {
    if (request()->isPost()) {
      $para = input();
      $work_id = $para['work_id'];
      $type = $para['type']; //1-当天，2-本周,3-本月,4-本季度
      // print_r($para['work_id']);
      // print_r($work_id);

      $op = '';
      if ($type == 1) {
        $op = "a.plan_date =TO_CHAR(SYSDATE,'YYYYMMDD')";
      } else if ($type == 2) {
        $op = "a.plan_date  between TO_CHAR(TO_DATE(trunc(sysdate, 'd') + 1),'YYYYMMDD') and TO_CHAR(TO_DATE(trunc(sysdate, 'd') + 7),'YYYYMMDD')";
      } else if ($type == 3) {
        $op = "substr(a.plan_date,0,6) =TO_CHAR(SYSDATE,'YYYYMM')";
      } else if ($type == 4) {
        $op = "a.plan_date  between TO_CHAR(trunc(sysdate, 'Q'),'YYYYMMDD') and TO_CHAR(add_months(trunc(sysdate, 'Q'), 3) - 1,'YYYYMMDD')";
      }
      //print_r($op);

      $sql_tj = "select count(1) cnt from WORKORDER a where a.work_id='{$work_id}' and " . $op;

      $sql_type = "select  case
                      when substr(a.work_order_no,0,2) like '00%' then
                        '装机'
                      when substr(a.work_order_no,0,2) like 'WH%' then
                        '维护'
                      else
                        '巡检'
                    end work_type,
                    sum(case when a.work_date is not null then 1 else 0 end) finish,
                    sum(case when a.work_date is null then 1 else 0 end) unfinish
                      from WORKORDER a where " . $op . "and a.work_id = '{$work_id}'
                      group by case
                      when substr(a.work_order_no,0,2) like '00%' then
                        '装机'
                      when substr(a.work_order_no,0,2) like 'WH%' then
                        '维护'
                      else
                        '巡检'
                    end";
      $res_tj = Db::query($sql_tj)[0];
      $res_type = Db::query($sql_type);

      if ($res_tj == 0) {
        return json([
          'code' => '100',
          'msg' => '未查到统计数据'
        ]);
      } else {
        return json([
          'code' => '00',
          'msg' => '工作单查询成功',
          'data' => $res_type
        ]);
      }
    }
  }

  //团队工作单统计
  function team_sumary()
  {
    if (request()->isPost()) {
      $para = input();
      $work_id = $para['work_id'];
      $type = $para['type']; //1-当天，2-本周,3-本月,4-本季度
      //print_r($work_id);

      $op = '';
      if ($type == 1) {
        $op = "a.plan_date =TO_CHAR(SYSDATE,'YYYYMMDD')";
      } else if ($type == 2) {
        $op = "a.plan_date  between TO_CHAR(TO_DATE(trunc(sysdate, 'd') + 1),'YYYYMMDD') and TO_CHAR(TO_DATE(trunc(sysdate, 'd') + 7),'YYYYMMDD')";
      } else if ($type == 3) {
        $op = "substr(a.plan_date,0,6) =TO_CHAR(SYSDATE,'YYYYMM')";
      } else if ($type == 4) {
        $op = "a.plan_date  between TO_CHAR(trunc(sysdate, 'Q'),'YYYYMMDD') and TO_CHAR(add_months(trunc(sysdate, 'Q'), 3) - 1,'YYYYMMDD')";
      }
    }
  }

  //工作单查询(通过工作单号或者商户名称)
  function query_workorder()
  {
    if (request()->isPost()) {
      try {
        $work_id = input('work_id');
        $type = input('type'); //1-工作单号,2-商户名称
        $mcht_name = input('mcht_name');
        $work_order_no = explode('_', input('work_order_no'))[0];

        dlog(input(), 'query_workorder-参数');
        $s_op = "";

        $s_h = "select trim(a.work_order_no) work_order_no,a.work_id,
                      a.plan_date,a.sender_date,a.status,
                      a.work_flag,a.mcht_name,a.addr,a.linkman,a.telno,
                      a.work_date,a.sign_date,a.sign_name,
                      a.device_type,a.work_name,a.mcht_addr,
                      a.work_flag_name,a.note,a.status_name,
                      trim(a.sender_name) sender,a.sender_name,a.gzdnr,a.sign_note,a.sign_id
                from v_workorder_1 a
              where 1 = 1
              and work_id='{$work_id}' and ";

        if ($type == '1') {
          $s_op = " work_order_no='{$work_order_no}'";
        } else {
          $s_op = " mcht_name like '%{$mcht_name}%'";
        }

        $sql = $s_h . $s_op . " order by a.status,a.work_flag";
        //fprint_r($sql);

        $workorder = Db::query($sql);

        if ($workorder) {
          return json([
            'code' => '00',
            'msg' => '工作单查询成功',
            'data' => $workorder
          ]);
        } else {
          return json([
            'code' => '100',
            'msg' => '未查到该工作单'
          ]);
        }
      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => ''
        ]);
      }
    }
  }

  //工作单列表
  function lb_workorder()
  {
    if (request()->isPost()) {
      try {
        dlog(input(), 'lb_workorder-参数');

        //执行人员
        $work_id = input('param.work_id');
        //计划日期
        $date = input('param.date');

        //查询某天派单
        $sql = "select trim(a.work_order_no) work_order_no,
                  a.work_id,
                  a.status,
                  a.status_name,
                  a.mcht_name,
                   a.mcht_addr,
                  a.work_flag_name,
                  a.speed_up,
                  trim(a.sender_name) sender,
                  to_char(to_date(a.sender_date,'YYYYMMDD'),'YYYY-MM-DD') sender_date,
                  to_char(to_date(a.work_date||a.work_time,'YYYYMMDDHH24MISS'),'YYYY-MM-DD HH24:MI:SS') work_date
              from v_workorder_1 a
            where a.work_id = '{$work_id}' and a.plan_date = '{$date}'
            order by a.work_date desc, a.status, a.work_flag";
        $data = Db::query($sql);
        $count = db_query_limit($sql)[0]; //总条数
        $res = array(
          'code' => '00',
          'count' => $count['total'],
          'data' => $data,
        );
        return json($res);
      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => ''
        ]);
      }
    }
  }

  //工作单详情
  function details_workorder()
  {
    if (request()->isPost()) {
      try {
        $work_order_no = input('work_order_no');
        dlog(input(), 'details_workorder-参数');
        $sql = "SELECT to_char(sysdate,'YYYYMMDD') today,trim(a.work_order_no) work_order_no,trim(a.term_no) term_no,
                a.sender_id,a.mcht_name2,a.status,a.status_name,
                a.addr,a.linkman,trim(a.telno) telno,a.mobile,
                a.work_id,a.work_name,a.job_num,a.work_flag_name,
                a.work_name,a.plan_date,a.note,a.addr2,a.mcht_id,
                a.term_type,a.mcht_name3,a.install_date,
                a.train_num,a.sign_name,a.sign_date,a.install_flag,
                to_char(to_date(a.work_date || a.work_time, 'YYYYMMDDHH24MISS'),'YYYY-MM-DD HH24:MI:SS') work_date,
                a.linkman2,a.mcht_addr2,a.telno2,a.task_note,a.sales_manager_id
        from v_workorder a
        where a.work_order_no = '{$work_order_no}'";
        $data = Db::query($sql);
        if ($data) {
          $data = $data[0];
          // //通过工作单号 查出 商户档案编号
          // $select = Db::table('workorder')->where(['WORK_ORDER_NO' => $work_order_no])->find();
          // //通过档案编号 和 当天时间 查询 当天上传的工作单号
          // $select2 = Db::table('workorder')->where(['MCHT_ID' => $select['mcht_id'], 'WORK_DATE' => date('Ymd')])->find();
          // //通过当天上传的工作单号 查询 上传的图片
          $field = array(
            'work_order_no',
            'filetype' => 'fType',
            'file_name' => 'fName',
            'file_url' => 'fPath',
            'create_date',
          );
          $where = '';
          $gongzuodan = '';
          if ($data['work_date']) {
            $sql = "SELECT
            work_order_no,
            filetype  fType,
            file_name  fName,
            file_url  fPath,
            create_date
            from workorder_es
            where WORK_ORDER_NO = '{$work_order_no}'
            and FILETYPE = '0'
            order by create_date desc";
            // $gongzuodan = Db::table('workorder_es')->field($field)->where(['WORK_ORDER_NO' => $work_order_no, 'FILETYPE' => '0'])->order('create_date desc')->select();
            $gongzuodan = Db::query($sql);
            if ($gongzuodan) {
              $gongzuodan = $gongzuodan[0];
            }
          } else {
            $where = "";
            // $select3 = Db::table('workorder_es')->field($field)->where(['WORK_ORDER_NO' => $select2['work_order_no'], 'FILETYPE' => ['not in', '0']])->select();
          }
          //通过档案编号 和 当天时间 查询 当天上传的工作单号
          $date = date('Ymd');
          $sql = "SELECT a.fType, b.fPath, b.fName, b.WORK_ORDER_NO, b.create_date
          from (select fType, max(create_date) create_date
                  from (select *
                          from (SELECT a.WORK_ORDER_NO,
                                       a.filetype      as fType,
                                       a.file_name     as fName,
                                       a.file_url      as fPath,
                                       a.create_date
                                  from WORKORDER_ES a
                                 where a.WORK_ORDER_NO in
                                       (SELECT WORK_ORDER_NO
                                          from WORKORDER
                                         where mcht_id = '{$data['mcht_id']}'
                                         and work_date = '{$date}'))
                         order by fType, create_date desc)
                 group by fType) a,
               (SELECT a.WORK_ORDER_NO,
                       a.filetype      as fType,
                       a.file_name     as fName,
                       a.file_url      as fPath,
                       a.create_date
                  from WORKORDER_ES a
                 where a.WORK_ORDER_NO in
                       (SELECT WORK_ORDER_NO from WORKORDER where mcht_id = '{$data['mcht_id']}' and work_date = '{$date}')) b
         where a.fType = b.fType
           and a.create_date = b.create_date
           and a.fType not in '0'";
          $select3 = Db::query($sql);
          $data['images'] = $select3;
          $data['images'][] = $gongzuodan;
          // print_r($sql);exit;
          // if (!$select3) {
          $where1 = '';
          if (!$data['sign_name']) {
            $where1 = "and a.fType <> '0'";
          }

          $sql = "SELECT a.fType,b.fPath, b.fName, b.WORK_ORDER_NO,b.create_date
          from (select fType, max(create_date) create_date
                  from (select *
                          from (SELECT a.WORK_ORDER_NO,
                                       a.filetype      as fType,
                                       a.file_name     as fName,
                                       a.file_url      as fPath,
                                       a.create_date
                                  from WORKORDER_ES a
                                 where a.WORK_ORDER_NO in
                                       (SELECT WORK_ORDER_NO
                                          from WORKORDER
                                         where term_no = '{$data['term_no']}'))
                         order by fType, create_date desc)
                 group by fType) a,
               (SELECT a.WORK_ORDER_NO,
                       a.filetype      as fType,
                       a.file_name     as fName,
                       a.file_url      as fPath,
                       a.create_date
                  from WORKORDER_ES a
                 where a.WORK_ORDER_NO in
                       (SELECT WORK_ORDER_NO from WORKORDER where term_no = '{$data['term_no']}')) b
         where a.fType = b.fType
         and a.create_date = b.create_date
         and a.fType not in '0'
         $where1";
          $images = Db::query($sql);
          // $data['images'] = $images;
        }

        //合并图片组
        foreach ($images as $k => $v) {
          $n = 0;
          $arr_type[] = $v['fType'];
          foreach ($select3 as $kk => $vv) {
            if ($v['fType'] == $vv['fType']) {
              $n++;
            }
          }
          if ($n == 0) {
            $data['images'][] = $v;
          }
        }
        // print_r($data['images']);exit;
        // }
        if ($data) {
          $data = $data;
          $res = array(
            'code' => '00',
            'msg' => '工作单详情查询成功',
            'data' => $data,
          );
        } else {
          $res = array(
            'code' => '100',
            'msg' => '工作单号错误或工作单不存在',
            'data' => $data,
          );
        }
        return json($res);
      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => ''
        ]);
      }
    }
  }

  //工作单内容修改
  function edit_workorder()
  {
    if (request()->isPost()) {
      try {
        $data = input();
        dlog($data, 'edit_workorder-参数');
        $work_order_no = $data['work_order_no'];
        foreach ($data as $k => $v) {
          $val[] = $k . "='" . $v . "'";
        }
        $val = implode(',', $val);
        $sql = "UPDATE WORKORDER set $val where work_order_no='$work_order_no'";
        $update = Db::execute($sql);
        if ($update) {
          $res = array(
            'code' => '00',
            'msg' => '工作单内容修改成功',
            'data' => ''
          );
        } else {
          $res = array(
            'code' => '100',
            'msg' => '工作单内容修改失败' . Db::getLastSql(),
            'data' => ''
          );
        }
        return json($res);
      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => ''
        ]);
      }
    }
  }

  //影印件上传
  function yyj_upload()
  {
    if (request()->isPost()) {
      Db::startTrans(); //开启事务
      try {
        $arr_url = [];
        $data = input();
        dlog($data, 'yyj_upload-参数');
        // print_r($data);exit;
        $path = config('outwork.yyj_url');
        $url = '/storage//storage/admintwo/upload/' . $data['work_order_no'] . '/' . date('Ymd') . '/';
        foreach ($data['images'] as $k => $v) {
          if ($v['fPath'] == '') {
            continue; // 跳出本次循环
          }
          //查询老图片
          $sql = "SELECT  * from WORKORDER_ES a
            WHERE  a.WORK_ORDER_NO = '{$data['work_order_no']}'
            AND FILETYPE = '{$v['fType']}'";
          $select = Db::query($sql);
          if ($select) {
            $select = $select[0];
          }
          //删除老图片数据
          $sql = "DELETE FROM WORKORDER_ES WHERE  WORK_ORDER_NO = '{$data['work_order_no']}' AND FILETYPE = '{$v['fType']}'";
          $delete = Db::execute($sql);
          //上传新图片
          $base64_url = base64_image_content($v['fPath'], $path, $url);
          $arr_url[] = $base64_url;
          if (!$base64_url) {
            $res = [
              'code' => '100',
              'msg' => '图片保存失败',
              'data' => ''
            ];
            return json($res);
          }
          $arr = [
            'work_order_no' => $data['work_order_no'], //工作单号
            'file_url' => $base64_url, //文件URL
            'note' => $data['note'], //备注
            'filetype' => $v['fType'], //文件类型 0-工作单 1-装机工作单 2-维护工作单 3-巡检工作单 4-押金收据收条 5-使用须知 6-门头 7-收银台 8-内景 9-收银员 10-签购单 99-其他
            'file_name' => $v['fName'], //文件名
            'create_id' => $data['create_id'], //创建人
          ];
          // $insert = Db::table('workorder_es')->insert($arr);
          $insert = $this->add_insert($arr, 'workorder_es');
          if ($insert) {
            if (isset($select['file_url'])) {
              //删除老图片
              if (file_exists($path . $select['file_url'])) {
                unlink($path . $select['file_url']);
              }
            }
          }
          $arr_url = [];
        }
        if ($insert) {
          $res = [
            'code' => '00',
            'msg' => '影印件上传成功',
            'data' => ''
          ];
          Db::commit(); //提交事务
        } else {
          $res = [
            'code' => '00',
            'msg' => '影印件上传失败' . Db::getLastSql(),
            'data' => ''
          ];
        }
        return json($res);
      } catch (\Throwable $th) {
        Db::rollback(); //回滚事务
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        if ($arr_url) {
          foreach ($arr_url as $k => $v) {
            if (file_exists($path . $v)) {
              unlink($path . $v);
            }
          }
        }
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => ''
        ]);
      }
    }
  }

  //无工作单派单
  function notgzd_upload()
  {
    if (request()->isPost()) {
      Db::startTrans(); //开启事务
      try {
        $arr_url = [];
        $data = input();
        dlog($data, 'notgzd_upload-参数');
        $path = config('outwork.yyj_url');

        //查询单号
        $sql = "select trim(prefix) prefix,trim(to_char(serial,'099999')) serial from work_order_no where type='2'";
        $gzd = Db::query($sql)[0];
        $data['work_order_no'] = $gzd['prefix'] . date('ym') . $gzd['serial']; //工作单号
        //单号+1
        $di_zeng = $this->serial('XJ', $gzd['serial']);

        $url = '/storage/admintwo/upload/' . $data['work_order_no'] . '/' . date('Ymd') . '/';

        foreach ($data['images'] as $k => $v) {
          if ($v['fPath'] == '') {
            continue; // 跳出本次循环
          }
          //上传新图片
          $base64_url = base64_image_content($v['fPath'], $path, $url);
          $arr_url[] = $base64_url;
          if (!$base64_url) {
            $res = [
              'code' => '100',
              'msg' => '图片保存失败'
            ];
            return json($res);
          }
          $arr = [
            'work_order_no' => $data['work_order_no'], //工作单号
            'file_url' => $base64_url, //文件URL
            'note' => $data['note'], //备注
            'filetype' => $v['fType'], //文件类型 0-工作单 1-装机工作单 2-维护工作单 3-巡检工作单 4-押金收据收条 5-使用须知 6-门头 7-收银台 8-内景 9-收银员 10-签购单 99-其他
            'file_name' => $v['fName'], //文件名
            'create_id' => $data['create_id'], //创建人
            'mchtno' => $data['mchtno'], //商户号
          ];
          // $insert = Db::table('workorder_es')->insert($arr);
          $insert = $this->add_insert($arr, 'workorder_es');
        }
        if ($insert) {
          $res = [
            'code' => '00',
            'msg' => '工作单录入成功',
            'data' => $data['work_order_no']
          ];
          Db::commit(); //提交事务
        } else {
          $res = [
            'code' => '100',
            'msg' => '工作单派单失败'
          ];
        }
        return json($res);
      } catch (\Throwable $th) {
        Db::rollback(); //回滚事务
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        if ($arr_url) {
          foreach ($arr_url as $k => $v) {
            if (file_exists($path . $v)) {
              unlink($path . $v);
            }
          }
        }
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => '',
        ]);
      }
    }
  }

  //无工作单影印件列表
  function notgzd_lb()
  {
    if (request()->isPost()) {
      try {
        $data = input();
        dlog($data, 'notgzd_lb-参数');
        // $result = validate(V_Workorder::class)->check($data);
        // if ($result) {
        //   return '数据验证成功';
        // } else {
        //   validate()->getError();
        // }
        //验证账号
        $date = date('Ymd');
        $sql = "SELECT 
        trim(WORK_ORDER_NO) WORK_ORDER_NO, --工作单号
        FILE_URL, --文件URL
        NOTE, --备注
        decode(trim(FILETYPE),'0','0-工作单','1','1-装机工作单','2','2-维护工作单','3','3-巡检工作单','4','4-押金收据收条','5','5-使用须知','6','6-门头','7','7-收银台','8','8-内景','9','9-收银员','10','10-签购单','99','99-其他',FILETYPE) FILETYPE, --文件类型 0-工作单 1-装机工作单 2-维护工作单 3-巡检工作单 4-押金收据收条  5-使用须知 6-门头 7-收银台 8-内景 9-收银员 10-签购单 99-其他
        FILE_NAME, --文件名
        CREATE_ID, --创建人
        to_char(to_date(CREATE_DATE,'YYYY-MM-DD HH24:MI:SS'),'YYYY-MM-DD HH24:MI:SS') CREATE_DATE, --创建日期
        MCHTNO --商户号
        from WORKORDER_ES where CREATE_ID='{$data['create_id']}'
        and substr(CREATE_DATE,1,8) = '{$date}'
        and mchtno is not null";
        $workorder_es = Db::query($sql);
        if ($workorder_es) {
          foreach ($workorder_es as $k => $v) {
            $mchtno_arr[] = $v['mchtno'];
          }
          $mchtno_arr = array_unique($mchtno_arr);
          foreach ($mchtno_arr as $k => $v) {
            foreach ($workorder_es as $kk => $vv) {
              if ($v == $vv['mchtno']) {
                $arr[$v][] = $vv;
              }
            }
          }
          $img_arr = [];
          foreach ($arr as $k => $v) {
            foreach ($v as $kk => $vv) {
              $img_arr[] = [
                'fPath' => $vv['file_url'],
                'fName' => $vv['file_name'],
                'fType' => $vv['filetype'],
              ];
            }
            $new_arr[] = [
              'work_order_no' => $vv['work_order_no'],
              'note' => $vv['note'],
              'create_id' => $vv['create_id'],
              'create_date' => $vv['create_date'],
              'mchtno' => $vv['mchtno'],
              'images' => $img_arr
            ];
            $img_arr = [];
          }
          // print_r($new_arr);exit;
          return json([
            'code' => '00',
            'msg' => '影印件列表查询成功',
            'data' => $new_arr
          ]);
        } else {
          return json([
            'code' => '100',
            'msg' => '未查到影印件'
          ]);
        }
      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败' . $arr[0],
          'data' => '',
        ]);
      }
    }
  }

  //回执单签收
  function receipt_sign()
  {
    if (request()->isPost()) {
      Db::startTrans(); //开启事务
      try {
        $arr_url = [];
        $data = input();
        $dlog = $data;
        // print_r($data['images']);exit;
        unset($dlog['images']);
        dlog($dlog, 'receipt_sign-参数');
        $model = new ModelWorkorder();
        $yanzheng = $model->yanzheng($data);
        if ($yanzheng['code'] == '0') {
          return json($yanzheng);
        }
        $path = config('outwork')['yyj_url'];

        $url = '/storage/admintwo/upload/' . $data['work_order_no'] . '/';
        //只能修改当天
        // $select = Db::table('workorder')->where(['work_order_no' => $data['work_order_no']])->find();
        // if ($select['plan_date'] != date('Ymd')) {
        //   $res = [
        //     'code' => '100',
        //     'msg' => '只能修改当天工作单'
        //   ];
        //   return json($res);
        // }
        $n = 0;
        foreach ($data['images'] as $k => $v) {
          if ($v['fPath']) {
            $n++;
          }
        }
        if ($n == '0') {
          $res = [
            'code' => '100',
            'msg' => '必须上传影印件'
          ];
          return json($res);
        }
        $insert = '1';
        foreach ($data['images'] as $k => $v) {
          if (substr($data['work_order_no'], 0, 2) == 'WH' || substr($data['work_order_no'], 0, 2) == 'XJ') {
            if ($v['fPath'] == '') {
              if ($v['fType'] == '0') {
                $res = [
                  'code' => '100',
                  'msg' => '必须上传工作单'
                ];
                return json($res);
              }
              if ($v['fType'] == '6') {
                $res = [
                  'code' => '100',
                  'msg' => '必须上传门头照'
                ];
                return json($res);
              }
              continue; // 跳出本次循环
            }
          } else {
            if ($v['fPath'] == '') {
              if ($v['fType'] == '0') {
                $res = [
                  'code' => '100',
                  'msg' => '装机单必须上传工作单'
                ];
                return json($res);
              }
              if ($v['fType'] == '6') {
                $res = [
                  'code' => '100',
                  'msg' => '装机单必须上传门头照'
                ];
                return json($res);
              }
              if ($v['fType'] == '7') {
                $res = [
                  'code' => '100',
                  'msg' => '装机单必须上传收银台'
                ];
                return json($res);
              }
              if ($v['fType'] == '8') {
                $res = [
                  'code' => '100',
                  'msg' => '装机单必须上传内景'
                ];
                return json($res);
              }
              continue; // 跳出本次循环
            }
          }

          //上传新图片
          $base64_url = '';
          if (strpos($v['fPath'], 'base64') !== false) {
            $type = trim($v['fType']) . '_';
            // if ($v['fPath'] != '') {
            $base64_url = base64_image_content($v['fPath'], $path, $url, $type);
            $arr_url[] = $base64_url;
            if (!$base64_url) {
              $res = [
                'code' => '100',
                'msg' => '图片保存失败'
              ];
              return json($res);
            }
            // }
            $arr = [
              'work_order_no' => $data['work_order_no'], //工作单号
              'file_url' => $base64_url, //文件URL
              'filetype' => $v['fType'], //文件类型 0-工作单 1-装机工作单 2-维护工作单 3-巡检工作单 4-押金收据收条 5-使用须知 6-门头 7-收银台 8-内景 9-收银员 10-签购单 99-其他
              'file_name' => $v['fName'], //文件名
              'create_id' => $data['create_id'], //创建人
            ];
            // $insert = Db::table('workorder_es')->insert($arr);
            $insert = $this->add_insert($arr, 'workorder_es');
          }
        }
        unset($data['images']);
        $sign_id = $data['create_id']; //签收人
        $mform = $data['mForm'];
        unset($data['create_id']);
        unset($data['mForm']);
        $workorder = $data;
        $workorder['sign_id'] = $sign_id;
        // if ($workorder['status'] == '1') {
        //   $workorder['status'] = '2';
        // }
        $workorder['linkman2'] = $mform['linkman2'];
        $workorder['mcht_addr2'] = $mform['mcht_addr2'];
        $workorder['telno2'] = $mform['telno2'];
        $workorder['sign_name'] = $data['sign_name'];
        $workorder['task_note'] = $data['task_note'];
        $workorder['install_flag'] = $data['install_flag'];
        $workorder['work_date'] = date('Ymd');
        $workorder['work_time'] = date('His');
        foreach ($workorder as $k => $v) {
          if ($v || $v == '0') {
            $value[] = $k . "='" . $v . "'";
          }
        }
        $value = implode(',', $value);
        $sql = "UPDATE WORKORDER set $value where work_order_no = '{$data['work_order_no']}'";
        $update = Db::execute($sql);
        //删除重复的图片类型
        $delete = '1';
        $sql = "select *
          from WORKORDER_ES t
         where t.work_order_no = '{$data['work_order_no']}'
           and (t.filetype, t.create_date) not in
               (select filetype, max(create_date) create_date
                  from (select *
                          from (SELECT a.WORK_ORDER_NO,
                                       a.filetype,
                                       a.create_date
                                  from WORKORDER_ES a
                                 where a.WORK_ORDER_NO = '{$data['work_order_no']}')
                         order by filetype, create_date desc)
                 group by filetype)";
        $select = Db::query($sql);
        if ($select) {
          foreach ($select as $k => $v) {
            if (file_exists($path . $v['file_url'])) {
              unlink($path . $v['file_url']);
            }
            $delete = Db::table('workorder_es')->where(['WORK_ORDER_NO' => $data['work_order_no'], 'FILETYPE' => $v['filetype'], 'FILE_URL' => $v['file_url']])->delete();
          }
        }
        if ($insert && $update && $delete) {
          $res = [
            'code' => '00',
            'msg' => '回执单签收成功',
            'data' => $data['work_order_no']
          ];
          Db::commit(); //提交事务
        } else {
          $res = [
            'code' => '100',
            'msg' => '回执单签收失败',
            'data' => ''
          ];
          dlog(Db::getLastSql());
        }
        return json($res);
      } catch (\Throwable $th) {
        Db::rollback(); //回滚事务
        dlog(Db::getLastSql() . "\n" . $th->getMessage() . "\n" . $th->getLine() . "\n" . $th->getFile(), '报错信息');
        if ($arr_url) {
          foreach ($arr_url as $k => $v) {
            if (file_exists($path . $v)) {
              unlink($path . $v);
            }
          }
        }
        $arr = explode('(', $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '回执单签收执行失败' . $arr[0],
          'data' => '',
        ]);
      }
    }
  }

  //work_order_no表serial字段值+1
  private function serial($prefix, $serial)
  {
    if ($serial == '999999') {
      $res = Db::execute("update work_order_no set serial='1' where prefix='{$prefix}'");
    } else {
      $res = Db::execute("update work_order_no set serial=serial+1 where prefix='{$prefix}'");
    }
    return $res;
  }

  function test()
  {
    $path = config('outwork.yyj_url');
    $url = '/storage/admintwo/upload/' . date('Ymd') . '/';
    //上传新图片
    print_r($url);
  }

  function test2()
  {
    $variable = [1, 2, 3, 4, 5];
    foreach ($variable as $key => $value) {
      if ($value == 3) {
        continue;
      }
      echo $value;
      echo "<br>";
    }
    exit;
    echo '这里不输出';
  }

  //新增 表数据
  function add_insert($data, $table)
  {
    foreach ($data as $kk => $vv) {
      if (is_array($vv) === false) {
        $key[] = $kk; //字段名
        $val[] = "'" . $vv . "'"; //字段值
      }
    }
    $key = implode(",", $key);
    $val = implode(",", $val);
    $insert = Db::execute("insert into  {$table} ({$key}) values ({$val})");
    dlog(Db::getLastSql(), '新增语句');
    return $insert;
  }
}
