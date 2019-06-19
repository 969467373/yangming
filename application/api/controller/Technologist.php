<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\InitialTension;
use app\common\model\FinalTension;
use app\common\model\PedestalLog;
use app\common\model\TaskProcess;
use app\common\model\ReturnTask;
use app\common\model\TaskFlow;
use app\common\model\Message;
use app\common\model\User;
use app\common\tool\Jpush;
use mrmiao\encryption\RSACrypt;
use think\Db;


//技术员相关任务
class Technologist extends ApiBase
{


    //检验任务
    public function checkStrap(TaskFlow $taskFlow,TaskProcess $taskProcess,ReturnTask $returnTask,User $user,Message $message)
    {
        Db::startTrans();
        try {
            $post_data = request()->param();

            //验证参数
            $result = $this->validate($post_data, 'Technologist.check');
            if (true !== $result)
                return ['code' => 400, 'message' => $result];


            if ($post_data['type'] == 1){//合格

                //修改技术员确认字段
                $taskProcess->where([
                    'task_id'=>$post_data['task_id'],
                    'process_id'=>$post_data['process_id'],
                ])->setField(['technologist_affirm'=>1]);

                if (in_array($post_data['process_id'],[10,23,25,27,29])){//确认后,下一步工序状态直接改成1
                    //下一步工序
                    $next_id = $post_data['process_id'] +1;

                    Db::name('task_process')->where(['task_id'=>$post_data['task_id'],'process_id'=>$next_id])
                        ->update(['process_status'=>1,'confirm_time'=>time()]);
                }

                switch ($post_data['process_id']){
                    case 7://确认后开始吊装入模

                        $pedestal_status = Db::name('pedestal')->where('id',$post_data['pedestal_id'])->value('status');
                        if ($pedestal_status != 2)
                            return ['code' => 400, 'message' => '该制梁台座已在使用中'];

                        //存入台座名称
                        Db::name('make_beam')
                            ->where('task_id',$post_data['task_id'])
                            ->setField(['pedestal'=>$post_data['pedestal_title']]);

                        //修改台座表(状态改为1 使用中,存入task_id)
                        Db::name('pedestal')
                            ->where('id',$post_data['pedestal_id'])
                            ->update(['status'=>1,'task_id'=>$post_data['task_id']]);

                        //添加台座使用记录
                        $log['pedestal_id'] = $post_data['pedestal_id'];
                        $log['task_id'] = $post_data['task_id'];
                        $pedestal_log = new PedestalLog();
                        $pedestal_log->add($log);

                        //胎具使用结束
                        Db::name('mould')
                            ->where('task_id',$post_data['task_id'])
                            ->update(['status'=>2,'task_id'=>0]);

                        //修改胎具使用记录表(结束时间)
                        Db::name('mould_log')
                            ->where('task_id',$post_data['task_id'])
                            ->update(['finish_time'=>time()]);

                        break;

                    case 21://起移梁(确认后,台座使用结束)
                        //台座使用结束
                        Db::name('pedestal')
                            ->where('task_id',$post_data['task_id'])
                            ->update(['status'=>2,'task_id'=>0]);
                        break;

                    case 19://初张拉通知单
                        //把用户id存进预张拉表
                        $model = new InitialTension();
                        $model->where('task_id',$post_data['task_id'])
                            ->update(['technologist_id'=>$post_data['user_id'],'technologist_time'=>time()]);
                        break;

                    case 22://终张拉通知单
                        //把用户id存进终张拉表
                        $model = new FinalTension();
                        $model->where('task_id',$post_data['task_id'])
                            ->update(['technologist_id'=>$post_data['user_id'],'technologist_time'=>time()]);
                        break;
                }

            }else{//不合格

                //判断是否是可以返工的工序
                if (!in_array($post_data['process_id'],[6,4,8,9,15,18,20,21,23,25,27,29,31])){
                    return ['code' => 402, 'message' => '该工序不能进行返工'];
                }

                //1.添加返工表数据
                //处理图片
                $image = request()->file();

                if (!empty($image)) {
                    $result = [];
                    foreach ($image as $k=>$file) {
                        // 移动到框架应用根目录/public/uploads/ 目录下
                        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'return');
                        if ($info) {
                            $result[] ='http://'.$_SERVER['HTTP_HOST']. '/uploads/return/' . date('Ymd') . '/' . $info->getFilename();
                        }
                    }
                    $post_data['img'] = serialize($result);
                } else {
                    $post_data['img'] = '';
                }

                if (in_array($post_data['process_id'],[6,8])){//钢筋班的任务
                    $field = 'rebar_id';
                }else if (in_array($post_data['process_id'],[4,9,15,21])){//制梁班的任务
                    $field = 'beam_id';
                }else if (in_array($post_data['process_id'],[18,20,23,25,27,29,31])){//预应力班任务
                    $field = 'prestress_id';
                }

                //负责人id
                $post_data['duty_id'] = $taskFlow->where('task_id',$post_data['task_id'])->value($field);

                //执行添加返工
                $returnTask->add($post_data);
                $returnTaskId = $returnTask->getLastInsID();

                //2.修改子流程表
                $item['task_id'] = $post_data['task_id'];
                $item['process_id'] = $post_data['process_id'];
                $taskProcess->returnProcess($item);


                //3.给相关人员发消息
                $users = $user->where('department_id',9)->field('id')->select();
                $leader = $user->where('department_id',10)->select();

                $leader_id = $leader[0]['id'];

                foreach ($users as $v){
                    $a[] = $v['id'];

                }
                $str = implode(',',$a);
                $str = $str.','.$leader_id.','.$post_data['duty_id'];//需要发消息的人

                $user_arr = explode(',',$str);



                foreach($user_arr as $k=>$v)
                {
                    $data[$k] =[
                        'user_id'=>$v,
                        'task_id'=>$post_data['task_id'],
                        'process_id' => $post_data['process_id'],
                        'type' => 3,
                        'return_id' => $returnTaskId,
                        'remark' => $post_data['reason'],
                    ];

                    //推送消息
                    $push = new Jpush();
                    $push->push_user('您有新消息,请及时查看',$v,2);
                }

                //批量新增消息
                $message->saveAll($data);
                
                //4.修改主流程表
                //当前任务的流程id
                $process_id = $taskFlow->where('task_id',$post_data['task_id'])->value('process_id');

                if (strstr($process_id,',')){//同时进行两个工序
                    $arr= explode(',',$process_id);

                    if ($post_data['process_id'] == 4){//预埋件安装检验
                        $item['process_id'] = $arr[0].','.$post_data['process_id'];//第一步工序id,传过来的id,
                    }else if ($post_data['process_id'] == 6){//钢筋绑扎检验
                        $item['process_id'] = $post_data['process_id'].','.$arr[1];//传过来的id,第二步工序id
                    }
                }else{//只有一个工序
                    $item['process_id'] = $post_data['process_id'];
                }

                //修改主流程表
                $taskFlow->editFlow($post_data['task_id'],$item);

                //删除检验的工序
                $del_id = $post_data['process_id']+1;

                $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$del_id])->delete();


            }

            Db::commit();

            return ['code' => 200, 'message' => '成功'];

        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();

            return ['code' => 400, 'message' => $e->getMessage()];

        }

    }




    //总任务完成
    public function finishTask(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcess $taskProcess,User $user,Message $message)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Technologist.finish');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //任务总状态
            $taskFlow->where('task_id',$post_data['task_id'])
                ->update(['status'=>2,'finish_time'=>time()]);

            //子流程状态
            $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                ->update(['process_status'=>2,'finish_time'=>time()]);
            //上一步工序, 修改技术员确认字段
            $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>32])
                ->update(['technologist_affirm'=>1]);

            //3.给相关人员发消息
            $users = $user->where('department_id',9)->field('id')->select();
            $leader = $user->where('department_id',10)->select();

            //部长id
            $leader_id = $leader[0]['id'];

            foreach ($users as $v){
                $a[] = $v['id'];

            }

            $str = implode(',',$a);
            $str = $str.','.$leader_id;//需要发消息的人

            $user_arr = explode(',',$str);

            //halt($user_arr);

            foreach($user_arr as $k=>$v)
            {
                $data[$k] =[
                    'user_id'=>$v,
                    'task_id'=>$post_data['task_id'],
                    'type' => 4,
                    'remark' => '任务完成',
                ];
            }

            //批量新增消息
            $message->saveAll($data);

            $flow_user = $taskFlow->where('task_id',$post_data['task_id'])
                ->field([
                    'technologist_id',
                    'rebar_id',
                    'beam_id',
                    'quality_id',
                    'lab_id',
                    'machine_id',
                    'prestress_id',
                    'blend_id',
                ])
                ->find();

            $str = $str.','.$flow_user['technologist_id'].','.$flow_user['rebar_id'].','.$flow_user['beam_id'].','.$flow_user['quality_id'].','.$flow_user['lab_id'].','.$flow_user['machine_id'].','.$flow_user['prestress_id'].','.$flow_user['blend_id'];

            $push_arr =  explode(',',$str);

            //推送消息
            $push = new Jpush();
            foreach ($push_arr as $item){
                $push->push_user('您的任务已完成',$item,4);
            }

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



}

