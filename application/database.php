<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'oracle',
    // 服务器地址
    'hostname'        => '172.16.129.207',
    // 数据库名
    'database'        => 'ora10',
    // 用户名
    'username'        => 'kywebdev',
    // 密码
    'password'        => 'kywebdev',
    // 端口
    'hostport'        => '1521',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => false,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    //数据库查询出的字段改为小写
    'params' => array(PDO::ATTR_CASE => PDO::CASE_LOWER),
    // PDO::CASE_UPPER：全大写
    // PDO::CASE_LOWER：全小写
    // PDO::CASE_NATURE：原样输出
];

// return [
//   // 数据库类型
//   'type'            => 'oracle',
//   // 服务器地址
//   'hostname'        => '127.0.0.1',
//   // 数据库名
//   'database'        => 'orcl',
//   // 用户名
//   'username'        => 'test',
//   // 密码
//   'password'        => '你的Oracle数据库密码',
//   // 端口
//   'hostport'        => '1521',
//   // 连接dsn
//   'dsn'             => '',
//   // 数据库连接参数
//   'params'          => [],
//   // 数据库编码默认采用utf8
//   'charset'         => 'utf8',
//   // 数据库表前缀
//   'prefix'          => '',
//   // 数据库调试模式
//   'debug'           => true,
//   // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
//   'deploy'          => 0,
//   // 数据库读写是否分离 主从式有效
//   'rw_separate'     => false,
//   // 读写分离后 主服务器数量
//   'master_num'      => 1,
//   // 指定从服务器序号
//   'slave_no'        => '',
//   // 是否严格检查字段是否存在
//   'fields_strict'   => true,
//   // 数据集返回类型
//   'resultset_type'  => 'array',
//   // 自动写入时间戳字段
//   'auto_timestamp'  => false,
//   // 时间字段取出后的默认时间格式
//   'datetime_format' => 'Y-m-d H:i:s',
//   // 是否需要进行SQL性能分析
//   'sql_explain'     => false,
// ];