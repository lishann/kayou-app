<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Workorder as ModelWorkorder;
use app\index\validate\WorkOrderValidate as Validate;
use Exception;

class User extends Controller
{
  //用户列表
  public function userList()
  {
    $data = input('get.');
    $sql = "select  a.big_area_id
            from area_member a, ky_user b,userinfo u
           where 1 = 1
             and u.userid = '{$data['userid']}'
             and a.member_id = u.empid
             and b.userid = u.userid
           order by a.member_id
          ";
    $big_area_id = Db::query($sql);
    if ($big_area_id == null) {
      throw new Exception('找不到当前用户的区域参数', 500);
    }
    $big_area_id = Db::query($sql)['0']['big_area_id'];

    if (isset($data['full_name']) && strlen($data['full_name'])) {
      $where = "and u.full_name like '%{$data['full_name']}%'";
    } else {
      $where = '';
    }

    $sql1 = "select u.userid, u.full_name,u.empid,bg.big_area_name area_name
            from 
            area_member a,
             ky_user b,
             userinfo u,
             big_area bg
           where 1 = 1
           $where
            and u.userid not in ('{$data['userid']}')
             and a.big_area_id in ('{$big_area_id}')
             and a.member_id = u.empid
             and b.userid = u.userid
             and a.big_area_id = bg.big_area_id
             and b.demission_flag <> '1'
           order by a.member_id";
    $res = Db::query($sql1);
    if ($res == null) {
      throw new Exception('当前区域没有可转单人员', 500);
    }
    return send($res);
  }
}
