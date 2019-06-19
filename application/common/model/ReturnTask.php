<?phpnamespace app\common\model;use think\Db;use think\Exception;use think\Session;//返工任务class ReturnTask extends BaseModel{    //获取用户返工列表    function getReturnList($where,$user_id,$page=1)    {        $list = $this->alias('r')            ->join('process p','p.id=r.process_id')            ->join('make_beam mb','mb.task_id = r.task_id')            ->join('user u','u.id = mb.user_id')            ->field([                'r.id',                'r.duty_id as user_id',                'r.task_id',                'r.process_id',                'p.title as process_title',                'r.reason',                'r.create_time',                'mb.user_id as technologist_id',                'u.name as technologist_name',                'mb.title',                'mb.cross_hole',                'mb.bridge_number',                'mb.bridge_model',            ])            ->where($where)            ->order('r.create_time desc')            ->paginate(6, false, ['page' => $page]);        foreach($list as &$item){            $item['feedback_id'] = 0;            $depart = Db::name('user')->where('id',$user_id)->value('department_id');            if (in_array($depart,[1,4])){                $feedback_id = Db::name('return_feedback')->where('return_id',$item['id'])->value('id');                if (!empty($feedback_id)){                    $item['feedback_id'] = $feedback_id;                }            }        }        return $list;    }    //删除返工记录    function delReturn($id)    {        $this->where('id',$id)->delete();    }    //新增返工记录 + 图片    function add($data){        //开启事务        Db::startTrans();        try {            $this->allowField(true)->save($data);            Db::commit();            return true;        } catch (\Exception $e) {            // 回滚事务            Db::rollback();            throw $e;        }    }    //查看返工详情    function getReturnDetail($id)    {        $data = $this->where('id',$id)            ->field([                'reason',                'img',            ])            ->find();        if(!empty($data['img'])){            $data['img'] = unserialize($data['img']);        }else{            $data['img'] = [];        }        return $data;    }}