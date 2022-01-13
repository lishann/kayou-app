<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\controller\Indexbase;

class Login extends Indexbase
{
  function login()
  {
    if (request()->isPost()) {
      try {
        $login_name = input('login_name');
        $login_pwd = md5(input('login_name') . input('login_pwd'));
        $openid = input('openid');
        $deviceid = input('deviceid'); //手机设备id
        $model = input('model'); //手机型号
        //验证账号
        $sql = "SELECT a.*,b.department_name,c.city_name from userinfo a,department b,city2 c  
        where a.department_id = b.department_id(+)
          and a.city = c.city_code(+)
        and login_name = '{$login_name}'";
        $res = db::query($sql);
        if (!$res) {
          return json(['code' => '100', 'msg' => '账号或密码错误']);
        }
        if ($res[0]['login_pwd'] != $login_pwd) {
          return json(['code' => '100', 'msg' => '账号或密码错误']);
        }
        if ($res[0]['status'] != '1') {
          return json(['code' => '100', 'msg' => '账号已被禁用']);
        }

        if ($res[0]['deviceid']) {
          if ($deviceid != $res[0]['deviceid']) {
            // return json(['code' => '100', 'msg' => '当前用户已绑定' . $res[0]['model'] . '设备']);
          }
        } else {
          $sql = "UPDATE userinfo set deviceid = '{$deviceid}',model = '{$model}' where userid = '{$res[0]['userid']}'";
          $update = Db::execute($sql);
        }

        //部门其他组员信息
        $sql = "SELECT trim(empid) empid,userid,full_name,telno from userinfo where department_id = '{$res[0]['department_id']}' and userid <> '{$res[0]['userid']}' ";
        $teammeate = db::query($sql);

        if ($openid) {
          $insert = 1;
          $sql = "SELECT * from user_openid a where openid = '{$openid}' or userid = '{$res[0]['userid']}'";
          $user_openid = Db::query($sql);
          if ($user_openid) {
            $user_openid = $user_openid[0];
            if ($user_openid['openid'] == $openid) {
              if ($user_openid['userid'] == $res[0]['userid']) {
                return json([
                  'code' => '00',
                  'msg' => '登录成功',
                  'data' => [
                    'userid' => $res[0]['userid'],
                    'full_name' => $res[0]['full_name'],
                    'empid' => trim($res[0]['empid'])
                  ]
                ]);
              } else {
                return json(['code' => '100', 'msg' => 'openid重复绑定']);
              }
            } else {
              return json(['code' => '100', 'msg' => '账户重复绑定']);
            }
          } else {
            $appid = config('outwork.appid');
            $sql = "INSERT into user_openid(userid,openid,appid) values('{$res[0]['userid']}','{$openid}','{$appid}')";
            $insert = Db::execute($sql);
          }
          if (!$insert) {
            return json(['code' => '100', 'msg' => '新增openid失败']);
          }
        }
        return json([
          'code' => '00',
          'msg' => '登录成功',
          'data' => [
            'userid' => $res[0]['userid'],
            'empid' => trim($res[0]['empid']),
            'sex' => $res[0]['sex'],
            'model' => $res[0]['model'],
            'full_name' => $res[0]['full_name'],
            'department_id' => $res[0]['department_id'],
            'department_name' => $res[0]['department_name'],
            'city' => $res[0]['city'],
            'city_name' => $res[0]['city_name'],
            'telno' => $res[0]['telno'],
            'teammeate' => $teammeate
          ]
        ]);

        //第一步获取openid,获取用户名密码
        //不存在openid情况:通过用户名和密码去用户表查询信息是否存在,存在的话新增记录并返回登录信息,不存在的话返回错误信息
        //存在openid的情况:通过用户名和密码去查信息是否存在,存在的话直接返回登录信息,不存在的话返回错误信息
        //不存openid的情况,但是用户名和密码又能查到,直接返回重复绑定

      } catch (\Throwable $th) {
        dlog(Db::getLastSql() . "\n" . $th->getMessage());
        return json([
          'code' => '100',
          'msg' => '执行失败',
          'data' => $th->getMessage()
        ]);
      }
    }
  }

  function get_openid()
  {
    try {
      $data = input();
      //dlog($data);
      $code = $data['code']; //小程序传来的code值
      $appid = config('outwork.appid'); //小程序的appid
      $appSecret = config('outwork.appSecret'); // 小程序的$appSecret
      $wxUrl = config('outwork.wxUrl');
      $getUrl = sprintf($wxUrl, $appid, $appSecret, $code); //把appid，appsecret，code拼接到url里
      $result = $this->curl_get($getUrl); //请求拼接好的url
      $wxResult = json_decode($result, true);
      if (empty($wxResult)) {
        echo '获取openid时异常，微信内部错误';
      } else {
        $loginFail = array_key_exists('errcode', $wxResult);
        if ($loginFail) { //请求失败
          var_dump($wxResult);
          $res = [
            'code' => '100',
            'msg' => 'openid获取失败',
            'data' => $wxResult,
          ];
        } else { //请求成功
          $openid = $wxResult['openid'];
          $res = [
            'code' => '00',
            'msg' => 'openid获取成功',
            'data' => $openid
          ];
        }
        return json($res);
      }
    } catch (\Throwable $th) {
      Db::rollback(); //回滚事务
      dlog($th->getMessage());
      return json([
        'code' => '100',
        'msg' => 'openid获取失败',
        'data' => $th->getMessage(),
      ]);
    }
  }

  //判断当前微信是否绑定
  function checkBind()
  {
    if (request()->isPost()) {
      dlog(input());
      $openid = input('openid');
      $sql = "select t.userid,t.full_name,t.empid,t.status from USERINFO t,USER_OPENID t2 where t.userid=t2.userid and t2.openid='$openid'";
      print_r($sql);
      $res = Db::query($sql);
      print_r($sql);
    }
  }

  //php请求网络的方法
  function curl_get($url, &$httpCode = 0)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验,部署在linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
  }
}
