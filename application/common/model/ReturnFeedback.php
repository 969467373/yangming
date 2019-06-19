<?phpnamespace app\common\model;use think\Db;use think\Exception;use think\Session;//返工反馈class ReturnFeedback extends BaseModel{    //新增返工反馈 + 图片    function add($data){        //开启事务        Db::startTrans();        try {            $this->allowField(true)->save($data);            Db::commit();            return true;        } catch (\Exception $e) {            // 回滚事务            Db::rollback();            throw $e;        }    }    //查看返工反馈详情    function getDetail($id)    {        $data = $this->where('id',$id)            ->field([                'reason',                'img',            ])            ->find();        if(!empty($data['img'])){            $data['img'] = unserialize($data['img']);        }else{            $data['img'] = [];        }        return $data;    }}