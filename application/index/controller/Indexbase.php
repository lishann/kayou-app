<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;

class Indexbase extends Controller
{
  // 初始化方法
  function initialize()
  {
    dlog(input());
  }

  //析构函数
  function __destruct()
  {
    dlog(Db::getLastSql());
  }
}
