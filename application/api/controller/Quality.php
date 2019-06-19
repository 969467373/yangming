<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\Message;
use app\common\model\TaskProcess;
use app\common\model\User;
use app\common\model\ReturnTask;
use app\common\model\TaskFlow;
use app\common\tool\Jpush;
use mrmiao\encryption\RSACrypt;
use think\Db;


//安质部相关任务
class Quality extends ApiBase
{

    //检验任务
    public function checkTask(RSACrypt $crypt,User $user,TaskFlow $taskFlow,TaskProcess $taskProcess,ReturnTask $returnTask, Message $message)
    {
        Db::startTrans();
        try {

            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Quality.check');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            if ($post_data['type'] == 1){//合格

                //检验工序的id
                $process_ids = $post_data['process_id'] +1;

                //开始检验的时间
                $confirm_time = $taskProcess->where(['task_id'=>$post_data['task_id'], 'process_id'=>$process_ids])->value('confirm_time');

                $used_time = time()-$confirm_time;

                //修改子流程表(检验工序的状态)
                $taskProcess->where(['task_id'=>$post_data['task_id'], 'process_id'=>$process_ids])
                    ->update([
                        'process_status'=>2,
                        'finish_time'=>time(),
                        'used_time'=>$used_time,
                    ]);

                //任务总流程id
                $process_id = $taskFlow->where('task_id',$post_data['task_id'])->value('process_id');
                //halt($process_ids);

                if($process_ids<8){//可能是5,7,10,32
                    if ($process_ids == 5){//检验预埋件安装

                        $check_process = 7;

                    }else if($process_ids == 7){//钢筋绑扎检验

                        $check_process = 5;
                    }

                    //判断 另一个进行的工序,状态为2 的数据是否存在
                    $info = $taskProcess->where(['task_id'=>$post_data['task_id'], 'process_id'=>$check_process, 'process_status'=>2])->find();


                    if (!empty($info) &&$info){//存在(另一个工序完事了)
                        //生成下一步工序(吊装入模)
                        $data2['task_id'] = $post_data['task_id'];
                        $data2['process_id'] = 8;
                        $data2['receive_department'] = '技术员,钢筋班,制梁班';
                        $data2['finish_department'] = '钢筋班';

                        //新增下一步工序
                        $taskProcess->addProcess($data2);
                        //修改总流程表工序id
                        $taskFlow->where('task_id',$post_data['task_id'])->setField(['process_id'=>8]);
                    }else{//不存在(另一个工序还没干完)

                        if (strstr($process_id,',')){//同时进行两个工序
                            $arr= explode(',',$process_id);

                            if ($process_ids == 5){//预埋件安装检验
                                $item['process_id'] =$arr[0];
                            }else if ($process_ids == 7){//钢筋绑扎检验
                                $item['process_id'] =$arr[1];
                            }
                        }else {//只有一个工序(另一个还没领取)
                            $item['process_id'] = $process_id;//不改变工序id
                        }
                        //修改总流程表工序id
                        $taskFlow->editFlow($post_data['task_id'],$item);
                    }
                }else{//10,32
                    //新生成的工序id
                    $item['process_id'] = $process_ids + 1;//检验工序id + 1;
                    //生成下一步工序
                    $data2['task_id'] = $post_data['task_id'];
                    $data2['process_id'] = $item['process_id'];
                    if ($process_id == 10){//下一步是发布浇筑令

                        $data2['receive_department'] = '技术员';
                        $data2['finish_department'] = '技术员';
                        $data2['inform_paper'] = 1;//是否是通知单

                    }else if ($process_id == 32){//下一步是任务完成

                        $data2['receive_department'] = '技术员';
                        $data2['finish_department'] = '技术员';
                    }
                    //新增下一步工序
                    $taskProcess->addProcess($data2);

                    //修改总流程表工序id
                    $taskFlow->editFlow($post_data['task_id'],$item);
                }







                /*if (strstr($process_id,',')){//同时进行两个工序
                    $arr= explode(',',$process_id);

                    if ($process_ids == 5){//预埋件安装检验
                        $item['process_id'] =$arr[0];
                    }else if ($process_ids == 7){//钢筋绑扎检验
                        $item['process_id'] =$arr[1];
                    }
                }else{//只有一个工序
                    if ($process_id < 8){
                        $item['process_id'] =8;
                    }else{
                        $item['process_id'] = $process_id + 1;
                    }

                    $data2['task_id'] = $post_data['task_id'];
                    $data2['process_id'] = $item['process_id'];
                    if ($process_id < 8) {//下一步工序是吊装入模

                        $data2['receive_department'] = '技术员,钢筋班,制梁班';
                        $data2['finish_department'] = '钢筋班';

                    }else if ($process_id == 10){//下一步是发布浇筑令

                        $data2['receive_department'] = '技术员';
                        $data2['finish_department'] = '技术员';
                        $data2['inform_paper'] = 1;//是否是通知单

                    }else if ($process_id == 32){//下一步是任务完成

                        $data2['receive_department'] = '技术员';
                        $data2['finish_department'] = '技术员';

                    }
                    //新增下一步工序
                    $taskProcess->addProcess($data2);
                }

                //修改主流程表
                $taskFlow->editFlow($post_data['task_id'],$item);*/


            }else {//不合格
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


                if ($post_data['process_id']==6){//钢筋班的任务
                    $field = 'rebar_id';
                }else if (in_array($post_data['process_id'],[4,9])){//制梁班的任务
                    $field = 'beam_id';
                }else if ($post_data['process_id']==31){//预应力班任务
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
                //部长id
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

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();

            return ['code' => 400, 'message' => $e->getMessage()];
        }
    }



}

