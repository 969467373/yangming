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
use app\common\model\Mudjack as MudjackModel;
use mrmiao\encryption\RSACrypt;
use think\Db;


//压浆通知单
class Mudjack extends ApiBase
{

    //压浆通知单(获取参数)
    public function getMudjack(RSACrypt $crypt,MudjackModel $mudjack)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Mudjack.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //上一步骤(终张拉)完成时间
            $data['final_tension_time'] = Db::name('task_process')
                ->where([
                    'task_id'=>$post_data['task_id'],
                    'process_id'=>23
                ])
                ->value('finish_time');

            $data['title'] = Db::name('make_beam')
                ->where('task_id',$post_data['task_id'])
                ->value('title');



            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //发布压浆通知单
    public function addMudjack(RSACrypt $crypt,MudjackModel $mudjack,TaskProcess $taskProcess,TaskFlow $taskFlow)
    {
        try {
            $post_data = $crypt->request();


            if ($post_data['type'] == 1){//执行添加
                unset($post_data['id']);

                //验证参数
                $result = $this->validate($post_data, 'Mudjack.add');
                if (true !== $result)
                    return $crypt->response(['code' => 400, 'message' => $result],true);

                //当前工序状态
                $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>26])
                    ->value('process_status');

                if ($process_status == 0)
                    return ['code' => 402, 'message' => '请先领取任务'];


                $mudjack->addMudjack($post_data);

                //完成此工序,并添加下一步
                $where['task_id'] = $post_data['task_id'];
                $where['process_id'] = 26;

                $taskProcess->where($where)->update([
                    'process_status'=>2,
                    'finish_time'=>time()
                ]);

                $data2['task_id'] = $post_data['task_id'];
                $data2['process_id'] = 27 ;
                $data2['receive_department'] = '预应力班';//领取部门
                $data2['finish_department'] = '预应力班';//完成部门
                //新增下一步工序(压浆)
                $taskProcess->addProcess($data2);

                //主流程表工序自增1
                $taskFlow->where('task_id',$post_data['task_id'])->setInc('process_id');

            }else{//执行编辑

                //验证参数
                $result = $this->validate($post_data, 'Mudjack.edit');
                if (true !== $result)
                    return $crypt->response(['code' => 400, 'message' => $result],true);

                $mudjack->editMudjack($post_data);

            }

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //查看压浆通知单
    public function lookMudjack(RSACrypt $crypt,MudjackModel $mudjack)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Mudjack.look');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $data = $mudjack->lookMudjack($post_data['task_id']);

            if (!$data)
                return ['code' => 402, 'message' => '暂无数据'];

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



}

