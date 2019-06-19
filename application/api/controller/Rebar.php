<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\Process;
use app\common\model\TaskProcess;
use app\common\model\User;
use app\common\model\TaskFlow;
use mrmiao\encryption\RSACrypt;


//钢筋班相关任务
class Rebar extends ApiBase
{

    //首次领取任务(绑扎)
    public function getOneTask(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcess $taskProcess)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Rebar.one');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $data['rebar_id'] = $post_data['user_id'];
            //当前任务的流程id
            $process_id = $taskFlow->where('task_id',$post_data['task_id'])->value('process_id');

            if ($process_id == 1){//第一个领取
                $data['process_id'] = 6;
                $data['status'] = 1;//把流程状态改为执行中
                //修改任务开始状态
                $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>1])->setField(['process_status'=>2]);
            }else{//已经有人领取
                $data['process_id'] = '6,'.$process_id;
            }

            //修改主流程表
            $taskFlow->editFlow($post_data['task_id'],$data);


            $data2['task_id'] = $post_data['task_id'];
            $data2['process_id'] = 6;
            $data2['process_status'] = 1;//进行中
            $data2['confirm_time'] = time();//确认时间
            $data2['receive_department'] = '钢筋班';//领取部门
            $data2['finish_department'] = '钢筋班';//完成部门
            $data2['rebar_id'] = $post_data['user_id'];//钢筋班id
            //添加子流程
            $taskProcess->addProcess($data2);

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //钢筋绑扎完成
    public function finishStrap(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcess $taskProcess)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Rebar.finish');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //当前任务状态
            $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                ->value('process_status');

            if ($process_status == 0)
                return ['code' => 402, 'message' => '该任务尚未领取'];

            if ($process_status == 2)
                return ['code' => 402, 'message' => '该任务已完成'];

            //修改子流程表
            $taskProcess->editProcess($post_data);

            $data2['task_id'] = $post_data['task_id'];
            $data2['process_id'] = 7;
            $data2['receive_department'] = '技术员,安质部';//领取部门
            $data2['finish_department'] = '技术员,安质部';//完成部门
            //新增下一步工序(技术员钢筋绑扎检验)
            $taskProcess->addProcess($data2);
            //当前任务的流程id
            $process_id = $taskFlow->where('task_id',$post_data['task_id'])->value('process_id');

            if (strstr($process_id,',')){//同时进行两个工序
                $arr= explode(',',$process_id);
                $item['process_id'] ='7,'. $arr[1];
            }else{//只有一个工序
                $item['process_id'] = 7;
            }

            //修改主流程表
            $taskFlow->editFlow($post_data['task_id'],$item);


            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }





}

