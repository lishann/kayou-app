<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\{Log, Request};
use app\lib\tools\tools;
use think\Db;

function sign($arr)
{
  //先按ascii
}

function success($data)
{
  return json($data, 200);
}

function fail($data)
{
  $response = (new Builder())->fail($data);
  //tools::dlog($response, '系统异常');
  return json($response, 200);
}


function error($data = [], $retmsg = '')
{
  $response = (new Builder())->import_error(['data' => $data, 'retmsg' => $retmsg]);
  //slog($response, "response");
  return json($response, 200);
}

function send($data = [], $total = 0)
{
  $response = (new Builder())->success(['data' => $data, 'total' => count($data)]);
  // dlog(count($data));
  return json($response, 200);
}


/*
* 包装类
*/
class Builder
{
  public function __construct()
  {
  }

  public function import_error($data)
  {
    $data['retcode'] = 500;
    if ($data['retmsg'] == null) {
      $data['retmsg'] = '操作失败';
    }
    return $this->builder($data);
  }

  public function success($data)
  {

    $data['retcode'] = 200;
    if (!isset($data['retmsg'])) {
      $data['retmsg'] = '操作成功';
    }
    // dump($data);
    return $this->builder($data);
  }

  public function fail($data)
  {
    if ($data['retcode'] == '50008') {
      $data['retmsg'] = '非法的token';
    }
    if ($data['retcode'] == '50012') {
      $data['retmsg'] = '账号已在其他客户端登录';
    }
    if ($data['retcode'] == '50014') {
      $data['retmsg'] = 'token已过期，请重新登录';
    }
    if (!isset($data['retcode'])) {
      $data['retcode'] = 500;
    }
    $response['retcode']  = $data['retcode'];
    if (!isset($data['retmsg'])) {
      $data['retmsg'] = config('retcode.info')[$data['retcode']] ?? '请求失败';
    }
    return $this->builder($data);
  }
  public  function builder($response)
  {
    // $response = [];
    $response['seqno']  = input("seqno") ?? "";
    $response['noncestr']  = date("YmdHis") . rand(1000, 9999);
    $response['timestamp']  = time();
    $response['sign']  = $this->getSign($response);

    $module = Request::module();
    $controller = Request::controller();
    $action = Request::action();

    $action   =  "$module/$controller/$action";
    $action  = strtolower($action);
    $response['action']  = $action;

    return $response;
  }
  public function getSign($arr)
  {
    if (isset($arr['sign'])) {
      unset($arr['sign']);
    }
    $access_token = Request::header('authorization');
    if (!$access_token) {
      return '';
    }
    ksort($arr);
    $i = 0;
    $string = "";
    foreach ($arr as $k => $v) {
      $_v = $v;
      if (is_array($v) || is_object($v)) {
        $_v = 'ARRAY';
      }
      if ($i == 0) {
        $string .= "$k=$_v";
        $i++;
      } else {
        $string .= "&$k=$_v";
      }
    }

    $string .= "&key=" . $access_token;
    return strtoupper(md5($string));
  }

  // 比较两串字符的ascii码大小
  function check_ascii(string $str1, string $str2)
  {
    $len1 = strlen($str1);
    $len2 = strlen($str2);

    if ($len1 > $len2) {
      $bool = false;
      $len = $len2;
    } else {
      $bool = true;
      $len = $len1;
    }

    for ($i = 0; $i < $len; ++$i) {
      if (ord($str1[$i]) > ord($str2[$i])) { // 第一个 比 第二个 大
        return false;
      } elseif (ord($str1[$i]) < ord($str2[$i])) { // 第二个 比 第一个 大
        return true;
      }
    }

    return $bool; // 前面字符相等，长度短的小
  }

  // 根据 ascii码 排序 顺序 (仿windows文件排序)
  function asc_sort(array &$arr, callable $callable)
  {
    foreach ($arr as $k1 => &$v1) {
      foreach ($arr as $k2 => &$v2) {
        if ($v1 != $v2 && $callable($v1, $v2)) {
          $tmp = $v1;
          $v1 = $v2;
          $v2 = $tmp;
        }
      }
    }
  }

  // 根据 ascii码 排序 倒序 (仿windows文件排序)
  function ascr_sort(array &$arr, callable $callable)
  {
    foreach ($arr as &$v1) {
      foreach ($arr as &$v2) {
        if ($v1 != $v2 && !$callable($v1, $v2)) {
          $tmp = $v1;
          $v1 = $v2;
          $v2 = $tmp;
        }
      }
    }
  }
}

//-----------------------------------------zzy------------------------------------------------------
/**
 * @description:  base64格式编码转换为图片并保存对应文件夹
 * @param {type}
 * @return:
 */
function base64_image_content($base64_image_content, $path, $url, $upload = '')
{
  //匹配出图片的格式
  if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
    $type = $result[2];
    $file = $path . $url;
    if (!file_exists($file)) {
      //检查是否有该文件夹，如果没有就创建，并给予最高权限
      mkdir($file, 0777, true);
    }
    if ($upload) {
      $url = $url . $upload . uniqid(time()) . ".{$type}";
    } else {
      $url = $url . uniqid(time()) . ".{$type}";
    }
    $new_file = $path . $url;
    if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
      return $url;
    } else {
      return false;
    }
  } else {
    return false;
  }
}
//日志
/**
 * @param string $content 日志信息
 * @param string $title 提示信息
 * @return string
 */
function dlog($content, $title = "提示信息")
{

  date_default_timezone_set('PRC');

  $filename = './oracle_log/oracle_log.log';
  $recordsize = 3072;
  $outmode = 'file';

  if (is_array($content)) {
    $content = json_encode($content, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
  }

  $len = strlen($content);

  $data = debug_backtrace();
  $func = $data[1]['function'];
  $data = $data[0];
  $dt = date("Y/m/d H:i:s");
  $dtext = date("YmdHis");

  $file = basename($data['file']);
  $line = $data['line'];

  if (isset($recordsize) && $len > $recordsize) {
    $tmp_content = substr($content, 0, $recordsize - 1) . PHP_EOL . "...省略(数据长度大于配置长度: {$len} > {" . $recordsize . "} )...";
  } else {
    $tmp_content = $content;
  }
  $request = Request::instance();
  //当前控制器名
  $controller = $request->controller();
  //当前方法名
  $action = $request->action();
  // $record = "$dt file:$file file2:$controller func:$func func2:$action line:$line pid:" . getmypid() . PHP_EOL . "=====$title($len)=====" . PHP_EOL . $tmp_content . PHP_EOL . PHP_EOL;
  $record = "$dt controller:$controller func:$action line:$line pid:" . getmypid() . PHP_EOL . "=====$title($len)=====" . PHP_EOL . $tmp_content . PHP_EOL . PHP_EOL;

  if (file_exists($filename)) {
    clearstatcache(); /*清缓存，否则常驻php程序的filesize得到的结果不会有变化*/
    $filesize = abs(filesize($filename));
    if ($filesize > 10240000) { /*大于10M*/
      rename($filename, $filename . "." . $dtext); /*文件更名*/
    }
  }

  if ($outmode == 'file') {
    file_put_contents($filename, $record, FILE_APPEND);
  } elseif ($outmode == 'show') {
    echo $record;
  } elseif ($outmode == 'all') {
    echo $record;
    file_put_contents($filename, $record, FILE_APPEND);
  }
}
//分页查询数据
/**
 * @param string $sql 查询的sql语句
 * @param string $page 查询的页数
 * @param string $limit 查询的条数
 * @param array $oracle 查询连接的数据库
 * @return array 返回二维数组
 */
function tp_db_query_page($sql, $page, $limit, $oracle = '', $pretreatment = [])
{
  array_push($pretreatment, $page, $limit, $page, $limit);
  $sqlstr = "select * from (select rownum rn, a.* from ($sql) a) where rn <= ? * ? and rn > (? - 1) * ?";

  if (empty($oracle)) {
    $data = Db::query($sqlstr, $pretreatment);
  } else {
    $data = Db::connect($oracle)->query($sqlstr, $pretreatment);
  }
  if ($data === false) {
    return false;
  } else {
    return $data;
  }
}

//分页查询数据
/**
 * @param string $sql 查询的sql语句
 * @param array $oracle 查询连接的数据库
 * @return array 返回二维数组
 */
function db_query_limit($sql, $oracle = '')
{
  $sqlstr = "select count(*) total from ($sql)";
  if (empty($oracle)) {
    $data = Db::query($sqlstr);
  } else {
    $data = Db::connect($oracle)->query($sqlstr);
  }
  if ($data === false) {
    return false;
  } else {
    return $data;
  }
}

//try 错误返回
function try_error($th, $db = '')
{
  if ($db == '') {
    $db = Db();
  }
  dlog($db->getLastSql() . "\n" . $th->getMessage(), '报错信息');
  $arr = explode("\n (", $th->getMessage());
  $res = [
    'retcode' => '500',
    'retmsg' => '执行失败' . $arr[0],
  ];
  return json($res);
}

//json 成功返回
function json_success($msg = '成功', $data = [])
{
  $res = [
    'retcode' => '200',
    'retmsg' => $msg,
    'data' => $data
  ];
  return json($res);
}

//json 失败返回
function json_error($msg = '失败', $data = [], $db = '')
{
  if ($db == '') {
    $db = Db();
  }
  $res = [
    'retcode' => '500',
    'retmsg' => $msg,
    'data' => $data
  ];
  dlog($db->getLastSql());
  return json($res);
}
