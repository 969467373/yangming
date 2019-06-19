<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\ConcreteRatio as ratioModel;
use app\common\model\TaskProcess;
use app\common\model\TaskFlow;
use app\common\model\User;
use app\common\tool\Jpush;
use mrmiao\encryption\RSACrypt;
use think\Cache;
use think\Db;


//混凝土配比
class ConcreteRatio extends ApiBase
{

    //发布配比(提交)
    public function addRatio(RSACrypt $crypt,ratioModel $ratio,TaskProcess $taskProcess,TaskFlow $taskFlow,User $user)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcreteRatio.add');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //当前工序状态
            $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>12])
                ->value('process_status');

            if ($process_status == 0)
                return ['code' => 402, 'message' => '请先领取任务'];

            //新增配比
            $ratio ->addratio($post_data);

            //完成此工序,并添加下一步
            $where['task_id'] = $post_data['task_id'];
            $where['process_id'] = 12;

            $taskProcess->where($where)->update([
                'process_status'=>2,
                'finish_time'=>time()
            ]);

            $data2['task_id'] = $post_data['task_id'];
            $data2['process_id'] = 13 ;
            $data2['receive_department'] = '拌合站';//领取部门
            $data2['finish_department'] = '拌合站';//完成部门
            //新增下一步工序(准备混凝土设备)
            $taskProcess->addProcess($data2);

            //主流程表工序自增1
            $taskFlow->where('task_id',$post_data['task_id'])->setInc('process_id');

            //给拌合站 推送消息(新任务)
            $push_arr= $user->getDepartUser(8);

            $push = new Jpush();
            foreach ($push_arr as $item){
                $push->push_user('您有新任务待领取',$item['id'],1);
            }

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //查看混凝土配比
    public function lookRatio(RSACrypt $crypt,ratioModel $ratio)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcreteRatio.look');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //查看配比
            $data = $ratio ->lookRatio($post_data['task_id']);

            if (empty($data))
                return ['code' => 402, 'message' => '暂无数据'];

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //修改配比(提交)
    public function editRatio(RSACrypt $crypt,ratioModel $ratio)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcreteRatio.edit');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //修改配比
            $ratio ->editratio($post_data);

            return $crypt->response(['code' => 200, 'message' => '修改成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }

}

