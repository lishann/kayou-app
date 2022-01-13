<?php

namespace app\index\validate;

use think\Validate;

class SelfDispatch extends Validate
{
    protected $rule = [

        'work_id' => ['require'],
        'term_no' => ['require'],
        'note' => ['require'],
        'plan_date' => ['require']
    ];

    protected $message  =   [
        'empid.require' => '执行人id不能为空',
        'term_no.require' => '虚拟终端号不能为空',
        'note.require' => '派单原因不能为空',
        'plan_date.require' => '计划执行时间不能为空[参数名:plan_date]',
    ];

    protected $scene = [
        'submit'  =>  [
            'empid', 'term_no', 'note','plan_date'
        ],
    ];
}
