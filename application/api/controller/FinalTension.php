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
use app\common\model\TaskFlow;
use app\common\model\User;
use app\common\model\FinalTension as FinalModel;
use mrmiao\encryption\RSACrypt;
use think\Db;


//终张拉通知单
class FinalTension extends ApiBase
{

    //终张拉通知单(获取参数)
    public function getTension(RSACrypt $crypt,FinalModel $tension)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'FinalTension.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //上一步骤(起移梁)完成时间
            $data['produced_time'] = Db::name('task_process')
                ->where([
                    'task_id'=>$post_data['task_id'],
                    'process_id'=>21
                ])
                ->value('finish_time');

            $data['title'] = Db::name('make_beam')
                ->where('task_id',$post_data['task_id'])
                ->value('title');

            //制梁台座
            $data['pedestal'] = Db::name('make_beam')->where('task_id',$post_data['task_id'])->value('pedestal');


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //发布终张拉通知单
    public function addTension(RSACrypt $crypt,FinalModel $tension,TaskProcess $taskProcess,TaskFlow $taskFlow)
    {
        try {
            $post_data = $crypt->request();


            if ($post_data['type'] == 1){//执行添加
                unset($post_data['id']);

                //验证参数
                $result = $this->validate($post_data, 'FinalTension.add');
                if (true !== $result)
                    return $crypt->response(['code' => 400, 'message' => $result],true);

                //当前工序状态
                $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>22])
                    ->value('process_status');

                if ($process_status == 0)
                    return ['code' => 402, 'message' => '请先领取任务'];


                $tension->addTension($post_data);

                //完成此工序,并添加下一步
                $where['task_id'] = $post_data['task_id'];
                $where['process_id'] = 22;

                $taskProcess->where($where)->update([
                    'process_status'=>2,
                    'finish_time'=>time()
                ]);

                $data2['task_id'] = $post_data['task_id'];
                $data2['process_id'] = 23 ;
                $data2['receive_department'] = '技术员,预应力班';//领取部门
                $data2['finish_department'] = '预应力班';//完成部门
                //新增下一步工序(终张拉)
                $taskProcess->addProcess($data2);

                //主流程表工序自增1
                $taskFlow->where('task_id',$post_data['task_id'])->setInc('process_id');

            }else{//执行编辑

                //验证参数
                $result = $this->validate($post_data, 'FinalTension.edit');
                if (true !== $result)
                    return $crypt->response(['code' => 400, 'message' => $result],true);

                $tension->editTension($post_data);

            }

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //查看终张拉通知单
    public function lookTension(RSACrypt $crypt,FinalModel $tension)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'FinalTension.look');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $data = $tension->lookTension($post_data['task_id']);

            if (!$data)
                return ['code' => 402, 'message' => '暂无数据'];

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



}

