<?phpnamespace app\common\model;use think\Db;use think\Exception;use think\Session;//割丝通知单class CutWire extends BaseModel{    //发布    function addWire($data)    {        $this->allowField(true)->save($data);    }    //编辑    function editWire($data)    {        $this->allowField(true)->isUpdate(true)->save($data);    }    //查看    function lookWire($task_id)    {        $data = $this ->where('task_id',$task_id)            ->field([                'id',                'task_id',                'project_name',                'bridge_number',                'inform_time',                'final_tension_time',                'shorten',                'conclusion',                'user_id',                'create_time',                'prestress_id',                'prestress_time',            ])            ->find();        if (empty($data))            return false;        $data['name'] = Db::name('user')->where('id',$data['user_id'])->value('name');        $data['prestress_name'] = Db::name('user')->where('id',$data['prestress_id'])->value('name');        return $data;    }}