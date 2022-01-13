<?php

namespace app\index\validate;

use think\Validate;

class Receive extends Validate
{
    protected $rule = [

        'work_order_no' => ['require'],
        'userid' => ['require'],
        'empid' => ['require'],

    ];

    protected $message  =   [
        'work_order_no.require' => '工作单号不能为空',
        'note.require' => '自助派单原因不能为空',
        'work_id.require' => '执行人id不能为空',
        'sender_id.require' => '派单人id不能为空',
        'term_no.require' => '虚拟终端号不能为空',
        'flag.require' => '提交标记不能为空',
        'userid.require' => '用户id不能为空',
        'content.require' => '转单原因不能为空',
        'empid.require' => '领取工作单人工号不能为空',
        'type.require' => '工单处理标记不能为空',
    ];

    protected $scene = [
        //处理工作单
        'get'  =>  [
            'empid', 'work_order_no'
        ],
        'details'  =>  [
            'empid', 'work_order_no'
        ]
    ];
}
