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


//物机部相关任务
class Machine extends ApiBase
{

    //发放预埋件完成任务
    public function finishTask(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcess $taskProcess)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Machine.finish');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //当前任务状态
            $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                ->value('process_status');

            if ($process_status == 0)
                return ['code' => 402, 'message' => '该任务尚未领取'];

            if ($process_status == 2)
                return ['code' => 402, 'message' => '该任务已完成'];


            $post_data['used_time'] = '';
            //修改子流程表
            $taskProcess->editProcess($post_data);


            $data2['task_id'] = $post_data['task_id'];
            $data2['process_id'] = 4;
            $data2['receive_department'] = '制梁班';
            $data2['finish_department'] = '制梁班';
            //新增下一步工序(预埋件安装)
            $taskProcess->addProcess($data2);
            //当前任务的流程id
            $process_id = $taskFlow->where('task_id',$post_data['task_id'])->value('process_id');

            if (strstr($process_id,',')){//同时进行两个工序
                $arr= explode(',',$process_id);
                $item['process_id'] = $arr[0].',4';
            }else{//只有一个工序
                $item['process_id'] = 4;
            }

            //修改主流程表
            $taskFlow->editFlow($post_data['task_id'],$item);

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }









}

