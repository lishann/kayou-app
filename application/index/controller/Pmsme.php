<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use app\index\model\Pmsme as ModelPmsme;

class Pmsme extends Indexbase
{
  //唯一id
  function uuid()
  {
    if (function_exists('com_create_guid')) {
      return com_create_guid();
    } else {
      mt_srand((float) microtime() * 10000); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
      $charid = strtoupper(md5(uniqid(rand(), true))); //根据当前时间（微秒计）生成唯一id.
      $hyphen = chr(45); // "-"
      $uuid = '' . //chr(123)// "{"
        substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
      //.chr(125);// "}"
      return $uuid;
    }
  }

  //商户进件
  function add_pms_me()
  {
    if (Request::instance()->isPost()) {
      try {
        Db::startTrans(); //开启事务
        $data = input('post.');
        $images = $data['images'];
        unset($data['images']);
        dlog($data, '请求数据');
        //数据验证
        $model = new ModelPmsme();
        $data['me_id'] = date('YmdHis') . mt_rand(0, 999); //生成id
        $valiData = $model->valiData($data); //验证数据
        if ($valiData['retcode'] == '500') {
          return json($valiData);
        }

        //图片验证
        $images_api = $model->images_api($images, $data);
        if ($images_api['retcode'] == '500') {
          return json($images_api);
        }

        $insert = $model->add_insert($data, 'pms_me');  //新增pms_me
        $arr_url = [];
        if ($insert) {
          $path = config('outwork')['yyj_url'];
          $url2 = '/storage/admintwo/pms_me_attach/' . $data['me_id'] . '/';
          foreach ($images as $k => $v) {
            //上传新图片
            $url = '/storage/admintwo/pms_me_attach/' . $data['me_id'] . '/' . $v['file_type'] . '/';
            $base64_url = base64_image_content($v['file_path'], $path, $url);
            if ($base64_url) {
              $arr_url[] = $base64_url;
            } else {
              if ($arr_url) {

                $del = $this->deldir($path . $url2); //删除文件夹及其文件夹下所有文件
                if ($del) {
                  return json_error('图片保存失败' . $v['file_type']);
                } else {
                  return json_error('文件夹删除失败' . $v['file_type']);
                }
              }
            }
            $pms_me_attach = [
              'me_id' => $data['me_id'],
              'attach_type' => $v['file_type'],
              'attach_url' => $base64_url,
              'attach_name' => $v['file_name'],
            ];
            $insert2 = $model->add_insert($pms_me_attach, 'pms_me_attach');
          }

          $res = Db::query("SELECT trim(prefix) prefix,trim(to_char((serial)+1,'099999')) as serial from ACQUIRER_INSTITUTION2 where acq_inst_id2='{$data['acct_ins_id']}'");
          if ($res) {
            $res = $res[0];
            // $res['serial'] = substr('00000' . $res['serial'], -6); //拼接数字
            // $data['mcht_id'] = $res['prefix'] . $res['serial']; //拼接档案编号
            $mcht_id = $res['prefix'] . $res['serial'];
          } else {
            Db::rollback(); //回滚事务
            return json_error('获取档案编号失败');
          }
          $mcht_info = [
            'mcht_id'            => $mcht_id, //档案编号
            'mcht_name'          => $data['mcht_name'], //商户名称
            'brief_name'         => $data['brief_name'],  //商户简称
            'province'           => $data['province'],  //省份
            'city'               => $data['city'],  //市
            'district'           => $data['district'],  //区
            'pan'                => $data['pan'], //开户账号
            'pan_name'           => $data['pan_name'],  //开户名
            'bank_name'          => $data['bank_name'], //开户行名称
            'acct_ins_id'        => $data['acct_ins_id'], //开户行
            'acct_ins_id2'       => $data['acct_ins_id2'],  //开户支行
            'cert_type'          => $data['cert_type'], //法人证件类型
            'sales_manager_id'   => $data['sales_manager_id'],  //客户经理
            'expand_id'          => $data['expand_id'], //拓展方
            'addr'               => $data['addr'],  //商户地址
            'cert_id'            => $data['cert_id'], //法人证件号码
            'cert_name'          => $data['cert_name'], //法人名称
            'cert_startdate'     => $data['cert_startdate'],  //法人证件有效起始日期
            'cert_enddate'       => $data['cert_enddate'],  //法人证件有效截止日期
            'linkman'            => $data['linkman'], //联系人姓名
            'mobile'             => $data['mobile'],  //联系人手机
            'opt_flag'           => '10', //状态
          ];
          //经营范围
          if (isset($data['business_scope'])) {
            $mcht_info['business_scope'] = $data['business_scope'];
          }
          //预留手机号
          if (isset($data['telno'])) {
            $mcht_info['telno'] = $data['telno'];
          }
          $insert3 = $model->add_insert($mcht_info, 'mcht_info');
          $res = Db::execute("update ACQUIRER_INSTITUTION2 set serial=serial+1 where acq_inst_id2='{$data['acct_ins_id']}'");
          if (!$res) {
            Db::rollback(); //回滚事务
            return json_error('新增档案失败-03');
          }
        } else {
          Db::rollback(); //回滚事务
          return json_error('进件新增失败');
        }
        if ($insert && $insert2 && $insert3) {

          Db::commit(); //提交事务
          return json_success('进件成功，档案编号为' . $mcht_id);
        } else {
          Db::rollback(); //回滚事务
          return json_error('进件失败');
        }
      } catch (\Throwable $th) {
        Db::rollback(); //回滚事务
        if ($arr_url) {
          $del = $this->deldir($path . $url2); //删除文件夹及其文件夹下所有文件
          if ($del) {
            return try_error($th);
          } else {
            return json_error('文件夹删除失败' . $v['file_type']);
          }
        }
      }
    }
  }

  //获取身份证正面
  function get_id_front()
  {
    if (request()->isPost()) {
      try {
        $image = input('image');
        $api_key = 'xxRniMsqdzbGCojtUu8g1Dx8';
        $secret_key = '4bQ5vVKvj30YBRKxke5GdwGgZtcULPhh';
        $access_token = $this->getAccessToken($api_key, $secret_key);
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token=' . $access_token;
        $body = ['id_card_side' => "front", 'image' => $image];
        //请求第三方并以json的格式返回
        $res = $this->request_post($url, $body);
        if (isset($res['error_code'])) {
          return ['retcode' => '500', 'retmsg' => $res['error_code'] . '-' . $res['error_msg'], 'data' => ''];
        } else {
          if ($res['idcard_number_type'] == '1' && $res['image_status'] == 'normal') { //识别正常
            return json_success('获取身份证正面', ['cert_name' => $res['words_result']['姓名']['words'], 'cert_id' => $res['words_result']['公民身份号码']['words']]);
          } else {
            $idcardMsg = $this->idcardMsg($res);
            return $idcardMsg;
          }
        }
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //获取身份证反面
  function get_id_back()
  {
    if (request()->isPost()) {
      try {
        $image = input('image');
        $api_key = 'xxRniMsqdzbGCojtUu8g1Dx8';
        $secret_key = '4bQ5vVKvj30YBRKxke5GdwGgZtcULPhh';
        $access_token = $this->getAccessToken($api_key, $secret_key);
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token=' . $access_token;
        $body = ['id_card_side' => "back", 'image' => $image];
        //请求第三方并以json的格式返回
        $res = $this->request_post($url, $body);
        if (isset($res['error_code'])) {
          return ['retcode' => '500', 'retmsg' => $res['error_code'] . '-' . $res['error_msg'], 'data' => ''];
        } else {
          if ($res['image_status'] == 'normal') { //识别正常
            return json_success('获取身份证反面', ['cert_startdate' => $res['words_result']['签发日期']['words'], 'cert_enddate' => $res['words_result']['失效日期']['words']]);
          } else {
            $idcardMsg = $this->idcardMsg2($res);
            return $idcardMsg;
          }
        }
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //获取银行卡
  function get_bank_card()
  {
    if (request()->isPost()) {
      try {
        $image = input('image');
        $api_key = 'xxRniMsqdzbGCojtUu8g1Dx8';
        $secret_key = '4bQ5vVKvj30YBRKxke5GdwGgZtcULPhh';
        $access_token = $this->getAccessToken($api_key, $secret_key);
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/bankcard?access_token=' . $access_token;
        $body = ['image' => $image];
        //请求第三方并以json的格式返回
        $res = $this->request_post($url, $body);
        if (isset($res['error_code'])) {
          return ['retcode' => '500', 'retmsg' => $res['error_code'] . '-' . $res['error_msg'], 'data' => ''];
        } else {
          $bank_card_number = preg_replace('/\D/s', '', $res['result']['bank_card_number']);
          return json_success('获取银行卡号', ['pan' => $bank_card_number]);
        }
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //身份证反面验证
  function idcardMsg2($res)
  {
    $msg = '';
    switch ($res['image_status']) {
      case 'reversed_side':
        $msg = '身份证正反面颠倒';
        break;
      case 'non_idcard':
        $msg = '上传的图片非身份证';
        break;
      case 'blurred':
        $msg = '身份证模糊';
        break;
      case 'other_type_card':
        $msg = '其他类型证照';
        break;
      case 'over_exposure':
        $msg = '身份证关键字段反光或过曝';
        break;
      case 'over_dark':
        $msg = '身份证亮度过低';
        break;
      case 'unknown':
        $msg = '未知状态';
        break;
    }

    return [
      'retcode' => '500',
      'retmsg' => $msg,
      'data' => ''
    ];
  }

  //身份证正面验证
  function idcardMsg($res)
  {
    $msg = '';
    switch ($res['image_status']) {
      case 'reversed_side':
        $msg = '身份证正反面颠倒';
        break;
      case 'non_idcard':
        $msg = '上传的图片非身份证';
        break;
      case 'blurred':
        $msg = '身份证模糊';
        break;
      case 'other_type_card':
        $msg = '其他类型证照';
        break;
      case 'over_exposure':
        $msg = '身份证关键字段反光或过曝';
        break;
      case 'over_dark':
        $msg = '身份证亮度过低';
        break;
      case 'unknown':
        switch ($res['idcard_number_type']) {
          case '-1':
            $msg = '身份证正面所有字段全为空';
            break;
          case '0':
            $msg = '身份证证号不合法';
            break;
          case '2':
            $msg = '身份证证号和性别、出生信息都不一致';
            break;
          case '3':
            $msg = '身份证证号和出生信息不一致';
            break;
          case '4':
            $msg = '身份证证号和性别信息不一致';
            break;
        }
        break;
    }

    return [
      'retcode' => '500',
      'retmsg' => $msg,
      'data' => ''
    ];
  }

  /**
   * 创建一个curl请求，用来请求第三方
   * @param $url
   * @param array $data
   * @return array|bool|float|int|mixed|stdClass|string|null
   * User: wang
   * Date: 2021/9/6 18:13
   */
  function request_post($url, $data = array())
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);

    return json_decode($output, true);
  }

  //删除文件夹及其文件夹下所有文件
  function deldir($dir)
  {
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
      if ($file != "." && $file != "..") {
        $fullpath = $dir . "/" . $file;
        if (!is_dir($fullpath)) {
          unlink($fullpath);
        } else {
          $this->deldir($fullpath);
        }
      }
    }

    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 获取AccessToken
   * @param $api_key
   * @param $secret_key
   * @return mixed|string
   * User: wang
   * Date: 2021/9/6 18:30
   */
  function getAccessToken($api_key, $secret_key)
  {
    $url = 'https://aip.baidubce.com/oauth/2.0/token';
    $post_data['grant_type']    = 'client_credentials';
    $post_data['client_id']     = $api_key;
    $post_data['client_secret'] = $secret_key;
    $o = "";
    foreach ($post_data as $k => $v) {
      $o .= "$k=" . urlencode($v) . "&";
    }
    $post_data = substr($o, 0, -1);

    $res = $this->request_post($url, $post_data);
    if (!isset($res['access_token'])) {
      exit($res['error']);
    }
    return $res['access_token'];
  }

  //拓展方
  function get_expand_id()
  {
    if (request()->isGet()) {
      try {
        $sql = "SELECT trim(expand_id) expand_id, expand_name from institution order by expand_id";
        $data = Db::query($sql);
        return json_success('拓展方', $data);
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //业务经理
  function get_sm_manager_id()
  {
    if (request()->isGet()) {
      try {
        $sql = "SELECT a.value,a.text from sales_manager_view a where 1=1 order by a.value";
        $data = Db::query($sql);
        return json_success('业务经理', $data);
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //客户经理
  function get_sales_manager_id()
  {
    if (request()->isGet()) {
      try {
        $sql = "SELECT trim(empid) value,full_name text from userinfo order by value";
        $data = Db::query($sql);
        return json_success('客户经理', $data);
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //银联MCC
  function get_mcc()
  {
    if (request()->isGet()) {
      try {
        $sql = "SELECT para_code as value,para_name as text from qb_para where type_id = '06'";
        $sql = "SELECT  a.ACCT_INS_NAME,a.ACCT_INS_ID,trim(b.ACCT_INS_NAME2) ACCT_INS_NAME2,b.ACCT_INS_ID2 from ACCT_INS a,ACCT_INS2 b where a.ACCT_INS_ID = b.ACCT_INS_ID(+)";
        $data = Db::query($sql);
        return json_success('银联MCC', $data);
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //获取用户轨迹
  function get_trajectory()
  {
    if (request()->isPost()) {
      try {
        //安装日期
        $work_date = date('Ymd');
        //执行人
        $work_id = input('param.work_id');
        $where1 = '';
        if ($work_id != '' && $work_id != null) {
          $where1 = "and b.work_id = '{$work_id}' ";
        }
        if (!$work_date || !$work_id) {
          return json_error('执行人不可为空');
        }
        //查十条数据
        $sql = "SELECT 
            b.work_id,
            c.mcht_name,
            d.addr,
            nvl(b.mcht_addr, e.addr) mcht_addr,
            b.longitude,
            b.latitude,
            b.work_date
            from workorder b,mcht_info_view c,mcht_info d,virterm_info e
            where b.term_no = e.term_no(+)
            and b.mcht_id = c.mcht_id(+)
            and c.mcht_id = d.mcht_id(+)
            and b.work_date = '{$work_date}'
            and b.longitude is not null
            and b.latitude is not null
            {$where1}";

        $data = Db::query($sql);

        $outdata = array(
          'retcode' => '200',
          'retmsg' => '',
          'data' => $data
        );
        return json($outdata);
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //GCJ-02(火星，高德) 坐标转换成 BD-09(百度) 坐标
  //@param bd_lon 百度经度
  //@param bd_lat 百度纬度
  function bdEncrypt($longitude, $latitude)
  {
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $longitude;
    $y = $latitude;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $longitude = $z * cos($theta) + 0.0065;
    $latitude = $z * sin($theta) + 0.006;
    // 保留小数点后六位
    $data['longitude'] = round($longitude, 6);
    $data['latitude'] = round($latitude, 6);
    return $data;
  }

  //商户备案
  function mcht_reporting()
  {
    if (request()->isPost()) {
      try {
        $data = input();
        $mcht_info = [
          ''
        ];
        $model = new ModelPmsme();
        $insert = $model->add_insert($mcht_info, 'mcht_info');
        if ($insert) {
          return json_success('备案成功');
        } else {
          return json_error('备案失败');
        }
      } catch (\Throwable $th) {
        return try_error($th);
      }
    }
  }

  //工作单列表
  public function record_mcht()
  {
    if (request()->isPost()) {

      try {
        dlog(input(), 'lb_workorder-参数');
        $data = input('');
        print_r($data);
        exit;
        //数据验证
        $model = new ModelPmsme();
        $valiData = $model->valiData2($data); //验证数据
        if ($valiData['retcode'] == '500') {
          return json($valiData);
        }
        //执行人员
        $work_id = input('param.work_id');

        //查询商户号是否存在
        $sql_cnt = "select mcht_no from MCHT_BUSINESS_ADJ t where trim(t.mcht_no)='{$data['data']}'";
        $res_cnt = Db::query($sql_cnt);
        if ($res_cnt) {
          return json_error('商户号已存在');
        }


        //二级收单机构
        // $acq_inst_id2 = input('param.acq_inst_id2');
        $acq_inst_id2 = "J3095510";
        $userid = input('param.userid');

        //生成档案号
        $fn_sql = "select trim(t.prefix) || lpad(trim(t.serial), 6, '0') fn,acq_inst_id,acq_inst_name2 from ACQUIRER_INSTITUTION2 t where t.acq_inst_id2 = '{$acq_inst_id2}'";
        $res_mcht = Db::query($fn_sql)[0];
        print_r($fn_sql);
        exit;


        $sql_da = "insert into mcht_info( 
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

        $sql_para = "insert into mcht_business_adj
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

         $sql_serial = "update ACQUIRER_INSTITUTION2 t set t.serial = t.serial + 1 where t.acq_inst_id2 = '{$acq_inst_id2}'";
         $res_serial = Db::execute($sql_serial);
        exit;
      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage(), '报错信息');
        $arr = explode('(', $th->getMessage());
        return json(['code' => '100', 'msg' => '执行失败' . $arr[0]]);
      }
    }
  }
}
