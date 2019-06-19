<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\model\MakeBeam as makeMeamModel;
use app\common\model\Message;
use app\common\model\Task;
use app\common\model\TaskFlow;
use app\common\model\TaskProcess;
use app\common\model\User;
use app\common\model\Process;
use app\common\model\MouldLog;
use app\common\tool\Jpush;
use mrmiao\encryption\RSACrypt;
use think\Cache;
use think\Db;


class MakeBeam extends ApiBase
{
    

    //发布制梁通知单
    public function addMakeBeam(RSACrypt $crypt,MouldLog $log)
    {

        Db::startTrans();

        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'MakeBeam.add');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $mould_status = Db::name('mould')->where('id',$post_data['mould_id'])->value('status');
            if ($mould_status != 2)
                return $crypt->response(['code' => 400, 'message' => '该绑扎胎具已在使用中'],true);

            //技术员确认指定
            $model1 = new Task();
            $model1->affirmTask($post_data);

            //发布通知单
            $model2 = new makeMeamModel();
            $model2->addMakeMeam($post_data);

            //修改胎具表状态(1=>使用中, 存入任务id)
            Db::name('mould')
                ->where('id',$post_data['mould_id'])
                ->update(['status'=>1,'task_id'=>$post_data['task_id']]);

            //添加胎具使用记录
            $data['mould_id'] = $post_data['mould_id'];
            $data['task_id'] = $post_data['task_id'];
            $log->add($data);

            //获取部长的用户id
            $manager = User::where('department_id',10)->select();
            $manager_id = $manager[0]['id'];

            //给工程部长发送消息
            $messageModel = new Message();

            $message['task_id'] = $post_data['task_id'];
            $message['user_id'] = $manager_id;
            $message['type'] = 1;
            $message['remark'] = '新的任务申请';

            $messageModel->addMessage($message);

            //推送消息
            $push = new Jpush();
            $push->push_user('您有新消息,请及时查看',$manager_id,2);


            Db::commit();

            return $crypt->response(['code' => 200, 'message' => '成功',],true);

        } catch (\Exception $e) {
            //回滚
            Db::rollback();

            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }

    //查看制梁通知单
    public function getBeamList(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //return $post_data;

            //验证参数
            $result = $this->validate($post_data, 'MakeBeam.lookBeam');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $model = new makeMeamModel();
            $data = $model->lookBeam($post_data);


            return $crypt->response(['code' => 200, 'message' => '成功','data' =>$data],true);

        } catch (\Exception $e) {

            return ['code' => 400, 'message' => $e->getMessage()];
        }



    }


    //工程部长审核制梁通知单
    public function checkTask(RSACrypt $crypt,Process $process)
    {
        Db::startTrans();
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'MakeBeam.check');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $model1 = new makeMeamModel();

            $check_status = $model1->where('task_id',$post_data['task_id'])->value('check_status');

            if($check_status==1){
                return $crypt->response(['code' => 400, 'message' => '该任务已通过审核'],true);
            }else if ($check_status==2){
                return $crypt->response(['code' => 400, 'message' => '该任务已被驳回'],true);
            }
            //修改审核状态
            $model1->checkTask($post_data);

            //技术员id
            $technologist_id = $model1->where('task_id',$post_data['task_id'])->value('user_id');


            //如果审核通过,任务开始
            if($post_data['check_status'] == 1){
                //添加任务主流程
                $model2 = new TaskFlow();
                $model2->addFlow($post_data['task_id'],$technologist_id);

                //添加任务工序流程
                $model3 = new TaskProcess();
                $data['task_id'] = $post_data['task_id'];
                $data['process_id'] = 1;

                $model3->addProcess($data);

                //生成缓存
                $where['time_limit'] = 1;
                $data= $process->getProcess($where);

                //存入缓存(列表)
                Cache::set("{$post_data['task_id']}_time_list",$data);

                //存入缓存(数组)
                $data2= $process->getProcess($where)->toArray();

                foreach($data2['data'] as $k=>$v){

                    $arr[$v['id']] =$v;
                }
                Cache::set("{$post_data['task_id']}_time",$arr);

                //给钢筋班, 制梁班, 安质部 ,试验室 ,物机部 推送消息
                $push_user_arr= Db::name('user')->where('department_id','in',[2,3,4,5,6])->field('id')->select();

                $push = new Jpush();
                foreach ($push_user_arr as $item){
                    $push->push_user('您有新任务待领取',$item['id'],1);
                }

            }else{
                $messageModel = new Message();
                //删除工程部长消息
                $where['task_id'] = $post_data['task_id'];
                $where['user_id'] = $post_data['user_id'];

                $messageModel->delMessage($where);

                //给技术员发送失败消息
                $messageModel = new Message();

                $message['task_id'] = $post_data['task_id'];
                $message['user_id'] = $technologist_id;
                $message['type'] = 2;
                $message['remark'] = '新建任务失败';

                $messageModel->addMessage($message);

                //推送消息
                $push = new Jpush();
                $push->push_user('您有新消息,请及时查看',$technologist_id,2);

            }

            Db::commit();


            return $crypt->response(['code' => 200, 'message' => '成功',],true);

        } catch (\Exception $e) {
            //回滚
            Db::rollback();

            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //技术员修改制梁通知单
    public function editMakeBeam(RSACrypt $crypt,User $user)
    {

        Db::startTrans();

        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'MakeBeam.edit');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);



            //修改通知单(审核状态归0)
            $model = new makeMeamModel();
            $model->editMakeMeam($post_data);


            $messageModel = new Message();
            $where['task_id'] = $post_data['task_id'];
            //删除之前的消息
            $messageModel->delMessage($where);

            //获取部长的用户id
            $manager = $user->where('department_id',10)->select();
            $manager_id = $manager[0]['id'];

            //给工程部长发送新的消息
            $message['task_id'] = $post_data['task_id'];
            $message['user_id'] = $manager_id;
            $message['type'] = 1;
            $message['remark'] = '新的任务申请';

            $messageModel->addMessage($message);

            //推送消息
            $push = new Jpush();
            $push->push_user('您有新消息,请及时查看',$manager_id,2);

            Db::commit();

            return $crypt->response(['code' => 200, 'message' => '成功',],true);

        } catch (\Exception $e) {
            //回滚
            Db::rollback();

            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }








}

