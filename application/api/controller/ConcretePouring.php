<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\ConcretePouring as PouringModel;
use app\common\model\TaskProcess;
use app\common\model\TaskFlow;
use app\common\model\MakeBeam;
use app\common\model\Process;

use mrmiao\encryption\RSACrypt;
use think\Cache;
use think\Db;


//砼浇筑令
class ConcretePouring extends CommonTask
{

    //发布浇筑令(获取参数)
    public function getPouring(RSACrypt $crypt,MakeBeam $beam)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcretePouring.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $data = $beam->getItem($post_data['task_id']);

            //上一步骤(模端内膜检验)完成时间
            $data['request_date'] = Db::name('task_process')
                ->where([
                    'task_id'=>$post_data['task_id'],
                    'process_id'=>9
                ])
                ->value('finish_time');

            //任务名称
            $data['title'] = Db::name('make_beam')
                ->where('task_id',$post_data['task_id'])
                ->value('title');

            //制梁台座
            $data['pedestal'] = Db::name('make_beam')->where('task_id',$post_data['task_id'])->value('pedestal');

            $cache = Cache::get("{$post_data['task_id']}_time");
            if (!$cache){//没有缓存
                $process = new Process();
                //查询表中数据
                $where['time_limit'] = 1;
                $data= $process->getProcess($where)->toArray();

                foreach($data['data'] as $k=>$v){

                    $arr[$v['id']] =$v;
                }
                //存入缓存
                Cache::set("{$post_data['task_id']}_time",$arr);
                //取出缓存
                $cache = Cache::get("{$post_data['task_id']}_time");
            }
            //计划用时
            $data['plan_duration'] = $cache[14]['duration'];


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }




    //发布浇筑令(提交)
    public function addPouring(RSACrypt $crypt,PouringModel $pouring,TaskProcess $taskProcess,TaskFlow $taskFlow)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcretePouring.add');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //当前工序状态
            $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>11])
                ->value('process_status');

            if ($process_status == 0)
                return ['code' => 402, 'message' => '请先领取任务'];

            //新增砼浇筑指令
            $pouring ->addPouring($post_data);

            //完成此工序,并添加下一步
            $where['task_id'] = $post_data['task_id'];
            $where['process_id'] = 11;

            $taskProcess->where($where)->update([
                'process_status'=>2,
                'finish_time'=>time()
                ]);

            $data2['task_id'] = $post_data['task_id'];
            $data2['process_id'] = 12 ;
            $data2['receive_department'] = '试验室,制梁班';//领取部门
            $data2['finish_department'] = '试验室';//完成部门
            $data2['inform_paper'] = 1;//是否是通知单
            //新增下一步工序(发布混凝土配比)
            $taskProcess->addProcess($data2);

            //主流程表工序自增1
            $taskFlow->where('task_id',$post_data['task_id'])->setInc('process_id');

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //查看浇筑令
    public function lookPouring(RSACrypt $crypt,PouringModel $pouring)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcretePouring.look');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //新增砼浇筑指令
            $data = $pouring ->lookPouring($post_data['task_id']);

            if (!$data)
                return ['code' => 402, 'message' => '暂无数据'];

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //修改浇筑令(提交)
    public function editPouring(RSACrypt $crypt,PouringModel $pouring)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ConcretePouring.edit');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //修改砼浇筑指令
            $pouring ->editPouring($post_data);

            return $crypt->response(['code' => 200, 'message' => '修改成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


}

