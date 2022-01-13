<?php

namespace app\index\model;

use think\Model;
use think\Validate;

class Workorder extends Model
{
  //数据处理
  function yanzheng($data)
  {
    $rule = [
      'create_id'             => 'require',
      'sign_name'             => 'require',
      'sign_note'             => 'require',
      'work_date'             => 'require',
      'status'                => 'require',
      'work_order_no'         => 'require',
    ];
    $msg = [
      'create_id.require'             => '签收人id' . '不能为空',
      'sign_name.require'             => '签收人' . '不能为空',
      'sign_note.require'             => '签收备注' . '不能为空',
      'work_date.require'             => '执行日期' . '不能为空',
      'status.require'                => '工作单状态' . '不能为空',
      'work_order_no.require'         => '工作单号' . '不能为空',
    ];
    $validate = new Validate($rule, $msg);
    if (!$validate->check($data)) {
      return [
        'code' => '0',
        'msg' => $validate->getError(),
      ];
    }
  }
}
