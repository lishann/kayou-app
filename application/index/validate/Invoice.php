<?php

namespace app\index\validate;

use think\Validate;

class Invoice extends Validate
{
    protected $rule = [
        'empid' => ['require'],
        'deposit_no' => ['require']
    ];

    protected $message  =   [
        'empid.require' => '用户工号不能为空',
        'deposit_no.require' => '押金条号不能为空',
    ];

    protected $scene = [
        //领取押金条
        'receive'  =>  [
            'empid', 'deposit_no'
        ]
    ];
}
