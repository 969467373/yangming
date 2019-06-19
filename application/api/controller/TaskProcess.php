<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\Task;
use app\common\model\User;
use mrmiao\encryption\RSACrypt;
use app\common\model\TaskFlow;
use app\common\model\TaskProcess as TaskProcessModel;
use think\Db;


class TaskProcess extends ApiBase
{

    //获取任务进度
    public function getTaskProgress(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcessModel $taskProcess)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'TaskProcess');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //任务信息
            $data = $taskFlow->getDetail($post_data['task_id']);
            if ($data['process_id'] == '7,5'){
                $data['ing'] = '0';

                $seven = Db::name('task_process')->where(['task_id'=>$data['task_id'],'process_id'=>7])->value('process_status');
                $five = Db::name('task_process')->where(['task_id'=>$data['task_id'],'process_id'=>5])->value('process_status');

                if ($seven == 1 ){
                    if ($five == 1){
                        $data['ing'] = '2';
                    }else{
                        $data['ing'] = '1';
                    }
                }else{
                    if ($five == 1){
                        $data['ing'] = '1';
                    }else{
                        $data['ing'] = '0';
                    }
                }
            }

            //任务进度
            $data['data_son'] = $taskProcess->getProgress($post_data['task_id']);

            //halt($data['data_son']);

            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //获取任务统计
    public function taskStatistics(RSACrypt $crypt,TaskProcessModel $taskProcess)
    {
        try {
            //钢筋绑扎
            $num1 = $taskProcess->where(['process_id'=>6,'process_status'=>2])->count();
            //混凝土浇筑
            $num2 = $taskProcess->where(['process_id'=>14,'process_status'=>2])->count();
            //预初张拉
            $num3 = $taskProcess->where(['process_id'=>20,'process_status'=>2])->count();
            //终张拉
            $num4 = $taskProcess->where(['process_id'=>23,'process_status'=>2])->count();
            //压浆
            $num5 = $taskProcess->where(['process_id'=>27,'process_status'=>2])->count();
            //封端
            $num6 = $taskProcess->where(['process_id'=>29,'process_status'=>2])->count();
            //防水
            $num7 = $taskProcess->where(['process_id'=>31,'process_status'=>2])->count();
            //成品验收
            $num8 = $taskProcess->where(['process_id'=>32,'process_status'=>2])->count();

            $data = [
                "1" => $num1,
                "2" => $num2,
                "3" => $num3,
                "4" => $num4,
                "5" => $num5,
                "6" => $num6,
                "7" => $num7,
                "8" => $num8,
            ];

            //halt($data['data_son']);

            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }








}

