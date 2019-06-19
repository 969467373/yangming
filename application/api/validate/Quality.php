<?phpnamespace app\api\validate;use app\common\model\User;use app\common\model\TaskFlow;use think\Validate;class Quality extends Validate{    protected $rule = [        'user_id'=> 'require|checkUserExist|checkUserDepart',        'task_id'=> 'require',        'process_id'=> 'require',        'type'=> 'require',        'reason'=> 'require',    ];    protected $message = [        'user_id.require' => '用户id不能为空',        'task_id.require' => '任务id不能为空',        'process_id.require' => '工序id不能为空',        'type.require' => '检测结果不能为空',        'reason.require' => '返工原因不能为空',    ];    protected $scene = [        //检测        'check'=>[            'user_id',            'task_id',            'process_id',            'type',            'reason',        ],    ];    //验证用户id是否存在    protected function checkUserExist($value)    {        $user = new User();        $res = $user->where(['id'=>$value])->value('id');        if (!$res)            return '用户id不存在';        return true;    }    //验证用户部门是否正确    protected function checkUserDepart($value)    {        $user = new User();        $depart = $user->where(['id'=>$value])->value('department_id');        if ($depart != 4)            return '用户无此权限';        return true;    }}