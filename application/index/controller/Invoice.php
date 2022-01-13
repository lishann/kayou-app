<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Invoice as Model;
use app\index\validate\Invoice as Validate;
use Exception;

class Invoice extends Controller
{

    //押金条列表
    public function list()
    {
        $data = input('get.');
        $model = new Model();
        $res = $model->getInvoiceList($data);
        return send($res);
    }

    //领取押金条
    public function receive()
    {
        $data = input('post.');
        $model = new Model();
        $validate = new Validate();
        if (!$validate->scene('receive')->check($data)) {
            throw new Exception($validate->getError(), 500);
        }
        $model->doReceive($data);
        return send();
    }
}
