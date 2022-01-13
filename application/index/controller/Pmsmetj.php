<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use app\index\model\Pmsmetj as Model;
use app\index\validate\Pmsmetj as Validate;
use Exception;

class Pmsmetj extends Controller
{
  //商户进件统计
  public function statistics()
  {
    $data = input('get.');
    $model = new Model();
    $validate = new Validate();
    if (!$validate->check($data)) {
      throw new Exception($validate->getError(), '500');
    }
    $res = $model->getStatistics($data);
    return send($res);
  }
}
