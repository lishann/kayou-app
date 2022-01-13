<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Equipment as Model;
use app\index\validate\WorkOrderValidate as Validate;
use Exception;


//设备领取
class Equipment extends Controller
{
    //待领取设备列表
    public function list()
    {
        if (request()->isGet()) {
            $data = input('get.');
            $model = new Model();
            $list = $model->getList($data);
            return send($list);
        }
    }

    //领取设备
    public function get()
    {
        $data = input('post.');
        $model = new Model();
        $validate = new Validate();
        // if (!$validate->scene('receive')->check($data)) {
        //     throw new Exception($validate->getError(), 500);
        // }
        $model->getEquipment($data);
        return send();
    }
}
