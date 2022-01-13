<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Workorders as Model;
use app\index\validate\WorkOrderValidate as Validate;
use Exception;

class Workorders extends Controller
{

  //工作单查询
  public function workOrderQuery()
  {
    $data = input('get.');
    $model = new Model();
    $res = $model->doQuery($data);
    return send($res);
  }

  //工作单详情
  public function workOrderDetails()
  {
    $data = input('get.');
    $model = new Model();
    $validate = new Validate();
    if (!$validate->scene('details')->check($data)) {
      throw new Exception($validate->getError(), 500);
    }
    $res = $model->getDetails($data);
    return send($res);
  }

  //执行工作单
  public function execute()
  {
    if (request()->isPost()) {
      $data = input('post.');
      $model = new Model();
      $validate = new Validate();
      if (!$validate->scene('execute')->check($data)) {
        throw new Exception($validate->getError(), 500);
      }
      $model->toExecute($data);
      return send();
    }
  }

  //工作单延期
  public function postpone()
  {
    if (request()->isPost()) {
      $data = input('post.');
      $model = new Model();
      $validate = new Validate();
      if (!$validate->scene('postpone')->check($data)) {
        throw new Exception($validate->getError(), 500);
      }
      $model->toPostpone($data);
      return send();
    }
  }

  //工作单作废
  public function scrap()
  {
    if (request()->isPost()) {
      $data = input('post.');
      $model = new Model();
      $validate = new Validate();
      if (!$validate->scene('scrap')->check($data)) {
        throw new Exception($validate->getError(), 500);
      }
      $model->toScrap($data);
      return send();
    }
  }



  //工作单内容修改
  function editWorkorder()
  {
    if (request()->isPost()) {
      $data = input('post.');
      $model = new Model();
      $validate = new Validate();
      if (!$validate->scene('edit')->check($data)) {
        throw new Exception($validate->getError(), 500);
      }
      $model->doEdit($data);
      return send();
    }
  }

  //转单待处理列表
  public function transferPendingList()
  {
    $data = input('get.');
    $model = new Model();
    $res = $model->getPendList($data);
    return send($res);
  }


  //发起工作单转单
  public function transferOrder()
  {
    $data = input('post.');
    $validate = new Validate();
    $model = new Model();
    if (!$validate->scene('transfer')->check($data)) {
      throw new Exception($validate->getError(), 500);
    }
    $model->doTransfer($data);
    return send();
  }


  //转单接受或拒接
  public function dealWithTransferOrder()
  {
    $data = input('post.');
    $validate = new Validate();
    $model = new Model();
    if (!$validate->scene('deal_with')->check($data)) {
      throw new Exception($validate->getError(), 500);
    }
    $model->doDealWith($data);
    return send();
  }
}
