<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\Mine as Model;


class Mine extends Controller
{
    //我的业绩
    public function performance()
    {
        $data = input('get.');
        $model = new Model();
        $res = $model->getPerformance($data);
        return send($res);
    }
}
