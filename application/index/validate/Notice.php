<?php

namespace app\index\validate;

use think\Validate;

class Notice extends Validate
{
    protected $rule = [
       
        'work_order_no' => ['require'],
        'delay_note' => ['require'],
        'delay_date' => ['require'],
    ];

    protected $message  =   [
    
        'work_order_no.require' => '工作单号不能为空',
        'delay_note.require' => '延期备注不能为空',
        'delay_date.require' => '延期日期不能为空',
    ];

    protected $scene = [
        //领取押金条
        'receive'  =>  [
            'empid', 'deposit_no'
        ]
    ];
}
