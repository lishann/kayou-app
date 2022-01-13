<?php

namespace app\index\validate;

use think\Validate;

class Pmsmetj extends Validate
{
    protected $rule = [
        'userid' => ['require'],
       // 'date' => ['require'],
        'period_type' => ['require']
    ];

    protected $message  =   [
        'userid.require' => '用户id不能为空',
      //  'date.require' => '时间不能为空',
        'period_type.require' => '统计类型不能为空',
    ];
}
