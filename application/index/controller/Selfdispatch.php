<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\SelfDispatch as Model;
use app\index\validate\SelfDispatch as Validate;
use Exception;

class SelfDispatch extends Controller
{

    //自助派单查询 
    public function query()
    {

        $data = input('get.');
        $model = new Model();
        $res = $model->getMerchantInfoList($data);
        return send($res);
    }

    //提交自助派单
    public function submitDispatch()
    {
        $data = input('post.');
        $model = new Model();
        $validate = new Validate();
        if (!$validate->scene('submit')->check($data)) {
            throw new Exception($validate->getError(), 500);
        }
        $ret = $model->doDispatch($data);
        return send($ret);
    }

    //作废
    public function scrap()
    {
        $data = input('post.');
        $model = new Model();
        $validate = new Validate();
        if (!$validate->scene('scrap')->check($data)) {
            throw new Exception($validate->getError(), 500);
        }
        $ret = $model->toScrap($data);
        return send($ret);
    }

    //自助派单详情
    public function details()
    {
        $data = input('get.');
        $model = new Model();
        $res = $model->getSelfDetails($data);
        return send($res);
    }
}
