<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Receive as Model;
use app\index\validate\Receive as Validate;
use Exception;


//领取工单
class Receive extends Controller
{
    //可领取工单列表
    public function list()
    {
        if (request()->isGet()) {
            $data = input('get.');
            $model = new Model();
            $list = $model->availableList($data);
            return send($list);
        }
    }
    //领取工单
    public function get()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $validate = new Validate();
            $model = new Model();
            if (!$validate->scene('get')->check($data)) {
                throw new Exception($validate->getError(), 500);
            }
            $model->getWorkOrder($data);
            return send();
        }
    }

    //查看已领取工单详情
    public function details()
    {
        if (request()->isGet()) {
            $data = input('get.');
            $validate = new Validate();
            $model = new Model();
            if (!$validate->scene('details')->check($data)) {
                throw new Exception($validate->getError(), 500);
            }
            $res = $model->getDetails($data);
            return send($res);
        }
    }
}
