<?php

namespace app\index\model;

use think\Model;
use think\Validate;
use think\Db;
use Exception;

class Pmsmetj extends Model
{
    //商户进件统计
    public function getStatistics(array $data = null)
    {
      $sql = $this->statisticsSql($data);
      $sql1 = $this->getSql($data); 
      $res['count'] = Db::query($sql);
      $res['list'] = Db::query($sql1);
      return $res;
    }
  

    public function getSql($data)
    {
      switch ($data['period_type']){
        case "1";
        $where = "and a.insert_date = '{$data['date']}'";
        break;
        case "2";
        $where = "and a.insert_date between '{$data['start']}' AND '{$data['end']}'";
        break;
        case "3";
        $where = "and substr(a.insert_date,0,6) = '{$data['date']}'";
        break;
  
      }
      $sql = "select a.mcht_id,a.mcht_name,a.opt_id,a.status as status_code,a.merchant_type,
      b.name,
      case
      when a.status ='0'then
       '已提交'
      when a.status ='1' then
       '已生效'
       when a.status ='2' then
       '驳回'
       else
       '审核中'
    end status,
    case
    when merchant_type ='1' then
     '小微商户'
    when merchant_type ='2' then
     '个体商户'
     when merchant_type ='3' then
     '企业商户'
    end merchant_type_name,
    a.insert_date
    from pms_me a, ky_user b
    where 1= 1
    and a.opt_id = b.userid(+)
    and a.opt_id = '{$data['userid']}'
    $where
    order by a.insert_date desc
    ";
    // print_r($sql);
    // die
    return $sql;
    }
  
    public function statisticsSql($data)
    {
      switch ($data['period_type']){
        case "1";
        $where = "and a.insert_date = '{$data['date']}'";
        break;
        case "2";
        $where = "and a.insert_date between '{$data['start']}' AND '{$data['end']}'";
        break;
        case "3";
        $where = "and substr(a.insert_date,0,6) = '{$data['date']}'";
        break;
      }

      $sql = "select sum(case
              when status = '1' then
               1
              else
               0
            end) finish,
          sum(case
              when status = '0' then
               1
              else
               0
            end) unfinish,
          count(*) count
         from pms_me a
        where a.opt_id = '{$data['userid']}' and a.insert_date is not null
        $where";
        return $sql;
    }

  
}