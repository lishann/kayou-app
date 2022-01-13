<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;

class Pmsme extends Model
{
  function valiData($data)
  {
    $rule = [
      'mcht_name'              => 'require|max:200',   //商户名称
      'brief_name'             => 'require|max:40',    //商户简称
      'province'               => 'require|max:2',     //省份
      'city'                   => 'require|max:2',     //市
      'district'               => 'require|max:2',     //区
      'pan'                    => 'require|max:30',    //开户账号
      'pan_name'               => 'require|max:200',   //开户名
      'bank_name'              => 'require|max:200',   //开户行名称
      'cert_type'              => 'require|max:2',     //法人证件类型
      'sales_manager_id'       => 'require|max:8',     //客户经理
      'expand_id'              => 'require|max:11',    //拓展方
      'me_id'                  => 'max:36',            //唯一id
      'mcht_id'                => 'max:20',            //商户档案编号
      'addr'                   => 'require|max:200',   //商户地址
      'linkman'                => 'require|max:80',            //联系人
      'telno'                  => 'max:25',            //电话   预留手机号
      'mobile'                 => 'require|max:20',            //手机
      'zip_code'               => 'max:6',             //邮编
      'fax'                    => 'max:220',           //传真
      'e_mail'                 => 'max:60',            //email邮箱
      'financial_linkman'      => 'max:80',            //财务联系人
      'registered_capital'     => 'max:120',           //注册资本
      'capital_currency'       => 'max:3',             //注册资本币种
      'business_scope'         => 'max:500',           //经营范围
      'scale_business'         => 'max:200',           //营业规模
      'site_flag'              => 'max:1',             //营业用地性质
      'business_area'          => 'max:12',            //营业面积
      'license'                => 'max:30',            //营业执照号码
      'license_date'           => 'max:6',             //营业执照有效截止日期
      'issuing_authority'      => 'max:200',           //
      'property'               => 'max:3',             //
      'taxcert_no'             => 'max:30',            //税务登记证号码
      'bank_id'                => 'max:12',            //开户行行号
      'bank_no'                => 'max:12',            //
      'cert_id'                => 'require|max:30',    //法人证件号码
      'cert_name'              => 'require|max:200',   //法人名称
      'expand_manager_id'      => 'max:32',            //拓展业务经理
      'opt_flag'               => 'max:2',             //状态
      'note'                   => 'max:200',           //备注信息
      'info'                   => 'max:20',            //营业执照|税务登记证|租赁合同|审批表|银联协议|存折复印件|门店照片|授权书|开户许可证|法人身份证
      'submit_date'            => 'max:20',            //
      'opt_id'                 => 'max:8',             //userinfo表当前登录用户的userid
      'insert_date'            => 'max:20',            //新增时间
      'reject_reason'          => 'max:200',           //审核驳回原因
      'service_type'           => 'max:2',             //
      'license_no'             => 'max:32',            //
      'settle_no'              => 'max:32',            //开户行清算号
      'term_number'            => 'max:4',             //申请终端数量
      'expand_manager_telno'   => 'max:40',            //拓展业务经理电话
      'pro_svr_cd'             => 'max:8',             //
      'acq_inst_id'            => 'max:8',             //收单机构
      'acq_inst_id2'           => 'max:8',             //收单机构2
      'pan_province'           => 'max:2',             //开户行省份
      'pan_city'               => 'max:2',             //开户行城市
      'bank_code'              => 'max:12',            //收款银行编码
      'pan_type'               => 'max:1',             //账户类型
      'settle_fee'             => 'max:6',             //结算费用
      'check_file_flag'        => 'max:1',             //是否需要对账文件
      'signature_rate'         => 'max:6',             //签约扣率
      'min_fee'                => 'max:6',             //保底手续费
      'max_fee'                => 'max:10',            //封顶手续费
      'manage_ins'             => 'max:11',            //
      'acct_ins_id'            => 'require|max:8',     //一级归属支行
      'acct_ins_id2'           => 'require|max:8',     //二级归属支行
      'unionpay_app_open_flag' => 'max:1',             //
      'grade'                  => 'max:1',             //商户等级
      'acct_ins_id3'           => 'max:8',             //三级归属支行
      'acct_ins_id4'           => 'max:8',             //四级归属支行
      'license_sdate'          => 'max:6',             //营业执照有效起始日期
      'cert_startdate'         => 'require|max:8',     //法人证件有效起始日期
      'cert_enddate'           => 'max:8',             //法人证件有效截止日期
      'project_belong'         => 'max:100',           //项目所属
      'sm_manager_id'          => 'max:10',            //扫码业务经理
      'sm_insert_time'         => 'max:8',             //扫码入网时间
      'status'                 => 'max:1',             //进件状态(0-待审核，1-审核通过,2-审核驳回)
      'atime'                  => 'max:20',            //审核时间
      'auditor'                => 'max:10',            //审核人
      'merchant_type'          => 'require|max:1',     //商户类型(1-小微商户，2-个人商户，3-企业)
      'management_type'        => 'max:1',             //经营类型(1-门店场所,2-流动经营/便民服务)
      'mcc'                    => 'require|max:4',     //mcc行业类目
      'controller_switch'      => 'require|max:1',     //实际控制人是否是经营者 0-否 1-是
      'beneficiary_switch'     => 'require|max:1',     //受益人是否是经营者 0-否 1-是
      'eq_type'                => 'require|max:32',    //设备类型
      'eq_num'                 => 'require|max:10',    //设备数量
      'mcht_addr'              => 'require|max:200',   //安装地址
    ];

    //小微商户
    if ($data['merchant_type'] == '1') {
      $rule['management_type'] = 'require|max:1'; //经营类型(1-门店场所,2-流动经营/便民服务)
      $rule['business_scope'] = 'require|max:500'; //经营类型(1-门店场所,2-流动经营/便民服务)
      $rule['telno'] = 'require|max:25'; //预留手机号
    }
    //个体商户
    if ($data['merchant_type'] == '2') {
      $rule['telno'] = 'require|max:25'; //预留手机号
    }

    $msg = [
      'mcht_name.require'           => '商户名称' . '不可为空',
      'brief_name.require'          => '商户简称' . '不可为空',
      'province.require'            => '省份' . '不可为空',
      'city.require'                => '市' . '不可为空',
      'district.require'            => '区' . '不可为空',
      'pan.require'                 => '开户账号' . '不可为空',
      'pan_name.require'            => '开户名' . '不可为空',
      'bank_name.require'           => '开户行名称' . '不可为空',
      'cert_type.require'           => '法人证件类型' . '不可为空',
      'sales_manager_id.require'    => '客户经理' . '不可为空',
      'expand_id.require'           => '拓展方' . '不可为空',
      'addr.require'                => '商户地址' . '不可为空',
      'merchant_type.require'       => '商户类型' . '不可为空',
      'mcc.require'                 => '行业类目' . '不可为空',
      'cert_name.require'           => '法人名称' . '不可为空',
      'cert_id.require'             => '法人证件号码' . '不可为空',
      'cert_startdate.require'      => '法人证件有效起始日期' . '不可为空',
      'acct_ins_id.require'         => '开户行' . '不可为空',
      'acct_ins_id2.require'        => '开户支行' . '不可为空',
      'linkman.require'             => '联系人姓名' . '不可为空',
      'mobile.require'              => '联系人手机号' . '不可为空',
      'controller_switch.require'   => '实际控制人是否是经营者' . '不可为空',
      'beneficiary_switch.require'  => '受益人是否是经营者' . '不可为空',
      'eq_type.require'             => '设备类型' . '不可为空',
      'eq_num.require'              => '设备数量' . '不可为空',
      'mcht_addr.require'           => '安装地址' . '不可为空',

      'management_type.require'     => '经营类型' . '不可为空',
      'business_scope.require'      => '经营范围' . '不可为空',
      'telno.require'               => '预留手机号' . '不可为空',

      'mcht_name.max'               => '商户名称' . '长度不能超过200',
      'brief_name.max'              => '商户简称' . '长度不能超过40',
      'province.max'                => '省份' . '长度不能超过2',
      'city.max'                    => '市' . '长度不能超过2',
      'district.max'                => '区' . '长度不能超过2',
      'pan.max'                     => '开户账号' . '长度不能超过30',
      'pan_name.max'                => '开户名' . '长度不能超过200',
      'bank_name.max'               => '开户行名称' . '长度不能超过200',
      'cert_type.max'               => '法人证件类型' . '长度不能超过2',
      'sales_manager_id.max'        => '客户经理' . '长度不能超过8',
      'expand_id.max'               => '拓展方' . '长度不能超过11',
      'me_id.max'                   => '唯一id' . '长度不能超过36',
      'mcht_id.max'                 => '商户档案编号' . '长度不能超过20',
      'addr.max'                    => '商户地址' . '长度不能超过200',
      'linkman.max'                 => '联系人' . '长度不能超过80',
      'telno.max'                   => '电话' . '长度不能超过25',
      'mobile.max'                  => '手机' . '长度不能超过20',
      'zip_code.max'                => '邮编' . '长度不能超过6',
      'fax.max'                     => '传真' . '长度不能超过220',
      'e_mail.max'                  => 'email邮箱' . '长度不能超过60',
      'financial_linkman.max'       => '财务联系人' . '长度不能超过80',
      'registered_capital.max'      => '注册资本' . '长度不能超过120',
      'capital_currency.max'        => '注册资本币种' . '长度不能超过3',
      'business_scope.max'          => '经营范围' . '长度不能超过500',
      'scale_business.max'          => '营业规模' . '长度不能超过200',
      'site_flag.max'               => '营业用地性质' . '长度不能超过1',
      'business_area.max'           => '营业面积' . '长度不能超过12',
      'license.max'                 => '营业执照号码' . '长度不能超过30',
      'license_date.max'            => '营业执照有效截止日期' . '长度不能超过6',
      'issuing_authority.max'       => 'issuing_authority' . '长度不能超过200',
      'property.max'                => 'property' . '长度不能超过3',
      'taxcert_no.max'              => '税务登记证号码' . '长度不能超过30',
      'bank_id.max'                 => '开户行行号' . '长度不能超过12',
      'bank_no.max'                 => 'bank_no' . '长度不能超过12',
      'cert_id.max'                 => '法人证件号码' . '长度不能超过30',
      'cert_name.max'               => '法人名称' . '长度不能超过200',
      'expand_manager_id.max'       => '拓展业务经理' . '长度不能超过32',
      'opt_flag.max'                => '状态' . '长度不能超过2',
      'note.max'                    => '备注信息' . '长度不能超过200',
      'info.max'                    => '营业执照' . '长度不能超过20',
      'submit_date.max'             => 'submit_date' . '长度不能超过20',
      'opt_id.max'                  => 'userinfo表当前登录用户的userid' . '长度不能超过8',
      'insert_date.max'             => '新增时间' . '长度不能超过20',
      'reject_reason.max'           => '审核驳回原因' . '长度不能超过200',
      'service_type.max'            => 'service_type' . '长度不能超过2',
      'license_no.max'              => 'license_no' . '长度不能超过32',
      'settle_no.max'               => '开户行清算号' . '长度不能超过32',
      'term_number.max'             => '申请终端数量' . '长度不能超过4',
      'expand_manager_telno.max'    => '拓展业务经理电话' . '长度不能超过40',
      'pro_svr_cd.max'              => 'pro_svr_cd' . '长度不能超过8',
      'acq_inst_id.max'             => '收单机构' . '长度不能超过8',
      'acq_inst_id2.max'            => '收单机构2' . '长度不能超过8',
      'pan_province.max'            => '开户行省份' . '长度不能超过2',
      'pan_city.max'                => '开户行城市' . '长度不能超过2',
      'bank_code.max'               => '收款银行编码' . '长度不能超过12',
      'pan_type.max'                => '账户类型' . '长度不能超过1',
      'settle_fee.max'              => '结算费用' . '长度不能超过6',
      'check_file_flag.max'         => '是否需要对账文件' . '长度不能超过1',
      'signature_rate.max'          => '签约扣率' . '长度不能超过6',
      'min_fee.max'                 => '保底手续费' . '长度不能超过6',
      'max_fee.max'                 => '封顶手续费' . '长度不能超过10',
      'manage_ins.max'              => 'manage_ins' . '长度不能超过11',
      'acct_ins_id.max'             => '一级归属支行' . '长度不能超过8',
      'acct_ins_id2.max'            => '二级归属支行' . '长度不能超过8',
      'unionpay_app_open_flag.max'  => 'unionpay_app_open_flag' . '长度不能超过1',
      'grade.max'                   => '商户等级' . '长度不能超过1',
      'acct_ins_id3.max'            => '三级归属支行' . '长度不能超过8',
      'acct_ins_id4.max'            => '四级归属支行' . '长度不能超过8',
      'license_sdate.max'           => '营业执照有效起始日期' . '长度不能超过6',
      'cert_startdate.max'          => '法人证件有效起始日期' . '长度不能超过8',
      'cert_enddate.max'            => '法人证件有效截止日期' . '长度不能超过8',
      'project_belong.max'          => '项目所属' . '长度不能超过100',
      'sm_manager_id.max'           => '扫码业务经理' . '长度不能超过10',
      'sm_insert_time.max'          => '扫码入网时间' . '长度不能超过8',
      'status.max'                  => '进件状态' . '长度不能超过1',
      'atime.max'                   => '审核时间' . '长度不能超过20',
      'auditor.max'                 => '审核人' . '长度不能超过10',
      'merchant_type.max'           => '商户类型' . '长度不能超过1',
      'management_type.max'         => '经营类型' . '长度不能超过1',
      'mcc.max'                     => '行业类目' . '长度不能超过4',
      'controller_switch.max'       => '实际控制人是否是经营者' . '长度不能超过1',
      'beneficiary_switch.max'      => '受益人是否是经营者' . '长度不能超过1',
      'eq_type.max'                 => '设备类型' . '长度不能超过32',
      'eq_num.max'                  => '设备数量' . '长度不能超过10',
      'mcht_addr.max'               => '安装地址' . '长度不能超过200',
    ];
    $validate = new Validate($rule, $msg);
    if (!$validate->check($data)) {
      return [
        'retcode' => '500',
        'retmsg' => $validate->getError(),
        'data' => '',
      ];
    }

    return [
      'retcode' => '200',
      'retmsg' => '',
      'data' => $data
    ];
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

  //身份证识别
  function images_api($images, $data)
  {
    if (!$images) {
      return json_error('图片不可为空');
    }
    foreach ($images as $k => $v) {
      $file_type[] = $v['file_type'];
    }
    //小微商户
    if ($data['merchant_type'] == '1') {
      if (!in_array('02', $file_type)) {
        return json_error('门头照未上传');
      }
      if (!in_array('03', $file_type)) {
        return json_error('店内照未上传');
      }
      if (!in_array('06', $file_type)) {
        return json_error('身份证正面未上传');
      }
      if (!in_array('07', $file_type)) {
        return json_error('身份证反面未上传');
      }
      if (!in_array('08', $file_type)) {
        return json_error('收单协议未上传');
      }
      if (!in_array('09', $file_type)) {
        return json_error('银行卡未上传');
      }
      if (!in_array('11', $file_type)) {
        return json_error('结算授权书未上传');
      }
    }
    //个体商户
    if ($data['merchant_type'] == '3') {
      if (!in_array('01', $file_type)) {
        return json_error('营业执照未上传');
      }
      if (!in_array('02', $file_type)) {
        return json_error('门头照未上传');
      }
      if (!in_array('03', $file_type)) {
        return json_error('店内照未上传');
      }
      if (!in_array('06', $file_type)) {
        return json_error('身份证正面未上传');
      }
      if (!in_array('07', $file_type)) {
        return json_error('身份证反面未上传');
      }
      if (!in_array('08', $file_type)) {
        return json_error('收单协议未上传');
      }
      if (!in_array('09', $file_type)) {
        return json_error('银行卡未上传');
      }
      if (!in_array('11', $file_type)) {
        return json_error('结算授权书未上传');
      }
    }
    //企业商户
    if ($data['merchant_type'] == '3') {
      if (!in_array('01', $file_type)) {
        return json_error('营业执照未上传');
      }
      if (!in_array('02', $file_type)) {
        return json_error('门头照未上传');
      }
      if (!in_array('03', $file_type)) {
        return json_error('店内照未上传');
      }
      if (!in_array('06', $file_type)) {
        return json_error('身份证正面未上传');
      }
      if (!in_array('07', $file_type)) {
        return json_error('身份证反面未上传');
      }
      if (!in_array('08', $file_type)) {
        return json_error('收单协议未上传');
      }
      if (!in_array('09', $file_type)) {
        return json_error('银行卡未上传');
      }
      if (!in_array('10', $file_type)) {
        return json_error('结算证明未上传');
      }
      if (!in_array('11', $file_type)) {
        return json_error('结算授权书未上传');
      }
    }
    $api_key = 'xxRniMsqdzbGCojtUu8g1Dx8';
    $secret_key = '4bQ5vVKvj30YBRKxke5GdwGgZtcULPhh';
    //首先先获取access_token  ,因为请求身份证验证接口需要用到 ，请求access_token 有效期是30天 我这里没有保存， 如需要你们可以保存到session中
    //获取access_token  返回参数请参考 https://ai.baidu.com/ai-doc/REFERENCE/Ck3dwjhhu
    $access_token = $this->getAccessToken($api_key, $secret_key);
    //请求身份证识别接口地址
    //https://cloud.baidu.com/doc/OCR/s/rk3h7xzck  官网文档

    //接收到前端传过来的base64图片  官网最大能接口4M 并且后缀为jpg/jpeg/png/bmp格式， 我这里也懒得进行操作了，你们获取base进行验证一下就行
    foreach ($images as $k => $v) {
      if ($v['file_type'] == '06') { //法人身份证正面
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token=' . $access_token;
        $body = ['id_card_side' => "front", 'image' => $v['file_path']];
        //请求第三方并以json的格式返回
        $res = $this->request_post($url, $body);
        if (isset($res['error_code'])) {
          return ['retcode' => '500', 'retmsg' => $res['error_code'] . '-' . $res['error_msg'], 'data' => ''];
        } else {
          if ($res['idcard_number_type'] == '1' && $res['image_status'] == 'normal') { //识别正常
            if (isset($data['controller_switch'])) {
              if ($data['controller_switch'] == '0') { //判断实际控制人是否是经营者
                if ($res['words_result']['公民身份号码']['words'] != $data['cert_id']) {
                  return ['retcode' => '500', 'retmsg' => '法人证件号码与实际填写不符', 'data' => ''];
                }
                if ($res['words_result']['姓名']['words'] != $data['cert_name']) {
                  return ['retcode' => '500', 'retmsg' => '法人证件姓名与实际填写不符', 'data' => ''];
                }
              }
            }
          } else {
            $idcardMsg = $this->idcardMsg($res);
            return $idcardMsg;
          }
        }
      }
      if ($v['file_type'] == '07') { //法人身份证反面
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token=' . $access_token;
        $body = ['id_card_side' => "back", 'image' => $v['file_path']];
        //请求第三方并以json的格式返回
        $res = $this->request_post($url, $body);
        if (isset($res['error_code'])) {
          return ['retcode' => '500', 'retmsg' => $res['error_code'] . '-' . $res['error_msg'], 'data' => ''];
        } else {
          if ($res['image_status'] == 'normal') { //识别正常
            if (isset($data['controller_switch'])) {
              if ($data['controller_switch'] == '0') { //判断实际控制人是否是经营者
                if ($res['words_result']['签发日期']['words'] != $data['cert_startdate']) {
                  return ['retcode' => '500', 'retmsg' => '法人证件起始日期与实际填写不符', 'data' => ''];
                }
                if ($data['cert_enddate'] != '') { //长期 截止日期为空
                  if ($res['words_result']['失效日期']['words'] != $data['cert_enddate']) {
                    return ['retcode' => '500', 'retmsg' => '法人证件截止日期与实际填写不符', 'data' => ''];
                  }
                }
              }
            }
          } else {
            $idcardMsg = $this->idcardMsg2($res);
            return $idcardMsg;
          }
        }
      }
    }
    // $url = ROOT_PATH . "public/static/images/image1.png";
    // $img = file_get_contents($url);
    // $img = base64_encode($img);
    // $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token=' . $access_token;
    // //调用使用的方法
    // //发起请求
    // $body = ['id_card_side' => "front", 'image' => $img];

    // //请求第三方并以json的格式返回
    // $res = $this->request_post($url, $body);
    // print_r($res);exit;
    // if (isset($res['error_code'])) {
    //   return json_error($res['error_code'] . '-' . $res['error_msg']);
    // } else {
    //   return json_success('成功');
    // }
    //返回参数参考地址： https://cloud.baidu.com/doc/OCR/s/rk3h7xzck
    //错误码请自行判断
    //参考地址 https://cloud.baidu.com/doc/OCR/s/dk3h7y5vr
  }

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

  //商户备案 数据验证
  function valiData2($data)
  {
    $rule = [
      'mcht_no'              => 'require|max:15',   //商户号
      'mcht_name'            => 'require|max:100',   //商户名称
      'addr'                 => 'require|max:100',   //地址
      'linkman'              => 'require|max:40',   //法人代表  联系人
      // 'telno'                => 'require|max:20',   //电话
      'bank_name'            => 'require|max:100',   //开户行名称
      'expand_manager_id'    => 'max:32',   //银行客户经理
      'expand_manager_telno' => 'max:20',   //客户经理联系方式
      'mobile'               => 'require|max:20',   //商户联系人手机号
      'province'             => 'require|max:2',   //省
      'city'                 => 'require|max:2',   //市
      'district'             => 'require|max:2',   //区
      'acq_inst_id2'         => 'require|max:8',   //收单机构2
      'signature_rate'       => 'require|max:6',   //签约扣率

    ];
    $msg = [
      'mcht_no.require'         => '商户号' . '不可为空',
      'mcht_name.require'       => '商户名称' . '不可为空',
      'addr.require'            => '地址' . '不可为空',
      'linkman.require'         => '联系人' . '不可为空',
      // 'telno.require'           => '电话' . '不可为空',
      'bank_name.require'       => '开户行名称' . '不可为空',
      'mobile.require'          => '商户联系人手机号' . '不可为空',
      'province.require'        => '省' . '不可为空',
      'city.require'            => '市' . '不可为空',
      'district.require'        => '区' . '不可为空',
      'acq_inst_id2.require'    => '收单机构2' . '不可为空',
      'signature_rate.require'  => '签约扣率' . '不可为空',


      'mcht_no.max'               => '商户号' . '长度不能超过100',
      'mcht_name.max'             => '商户名称' . '长度不能超过100',
      'addr.max'                  => '地址' . '长度不能超过100',
      'linkman.max'               => '联系人' . '长度不能超过20',
      // 'telno.max'                 => '电话' . '长度不能超过40',
      'bank_name.max'             => '开户行名称' . '长度不能超过100',
      'expand_manager_id.max'     => '银行客户经理' . '长度不能超过32',
      'expand_manager_telno.max'  => '客户经理联系方式' . '长度不能超过20',
      'mobile.max'                => '商户联系人手机号' . '20',
      'province.max'              => '省' . '长度不能超过2',
      'city.max'                  => '市' . '长度不能超过2',
      'district.max'              => '区' . '长度不能超过2',
      'acq_inst_id2.max'          => '收单机构2' . '长度不能超过8',
      'signature_rate.max'        => '签约扣率' . '长度不能超过6',
    ];
    $validate = new Validate($rule, $msg);
    if (!$validate->check($data)) {
      return [
        'retcode' => '500',
        'retmsg' => $validate->getError(),
        'data' => '',
      ];
    }

    return [
      'retcode' => '200',
      'retmsg' => '',
      'data' => $data
    ];
  }
}
