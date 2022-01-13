<?php

namespace app\index\validate;

use think\Validate;

class WorkOrderValidate extends Validate
{
    protected $rule = [
        'note' => ['require'],
        'work_id' => ['require'],
        'sender_id' => ['require'],
        'term_no' => ['require'],
        'flag' => ['require'],
        'work_order_no' => ['require'],
        'userid' => ['require'],
        'content' => ['require'],
        'empid' => ['require'],
        'type' => ['require'],
        'mcht_id' => ['require'],
        'task_note' => ['require'],
        'install_flag' => ['require'],
        'longitude' => ['require'],
        'latitude' => ['require'],
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
        'empid.require' => '发起转单人工号不能为空',
        'type.require' => '工单处理标记不能为空',
        'mcht_id.require' => '商户档案编号不能为空',
        'delay_note.require' => '延期原因不能为空',
        'postpone_date.require' => '延期日期不能为空',
        'task_note.require' => '执行备注不能为空',
        'install_flag.require' => '执行标记不能为空',
        'longitude.require' => '请确认是否开启定位',
        'latitude.require' => '请确认是否开启定位',
    ];

    protected $scene = [
        //提交自助派单
        'edit'  =>  [
            'work_order_no'
        ],
        'submit'  =>  [
            'note', 'work_id', 'term_no', 'flag'
        ],
        //提交转单申请
        'transfer'  =>  [
            'userid', 'work_order_no', 'content'
        ],
        //处理工作单
        'deal_with'  =>  [
            'empid', 'work_order_no', 'type'
        ],
        //处理工作单
        'get'  =>  [
            'empid', 'work_order_no'
        ],
        'details'  =>  [
            'work_order_no'
        ],
        'execute'  =>  [
            'work_order_no', 'task_note', 'install_flag', 'work_id','longitude','latitude'
        ],
        'postpone'  =>  [
            'work_order_no', 'delay_note', 'postpone_date'
        ],
        'scrap'  =>  [
            'work_order_no', 'empid'
        ]
    ];
}
