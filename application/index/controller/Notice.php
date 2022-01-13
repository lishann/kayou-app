<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use app\index\model\Notice as Model;
use app\index\validate\Notice as Validate;
use Exception;

class Notice extends Controller
{
  //超时消息通知
  public function timeOut()
  {
    $data = input('get.');
    $model = new Model();
    $res = $model->getTimeOut($data);
    return send($res);
  }

  //超时备注
  public function timeOutRemark()
  {
    $data = input('post.');
    $model = new Model();
    $validate = new Validate();
    if (!$validate->check($data)) {
      throw new Exception($validate->getError(), '500');
    }
    $model->toRemark($data);
    return send();
  }
}
