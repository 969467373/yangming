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
use app\common\model\TaskRead;
use app\common\model\User;
use mrmiao\encryption\RSACrypt;
use app\common\model\TaskFlow as flowModel;
use think\Db;


class TaskFlow extends ApiBase
{

    //获取新任务列表
    public function getNewTaskList(RSACrypt $crypt,TaskRead $read)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'TaskFlow');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取部门id
            $userModel = new User();
            $depart_id = $userModel->where('id',$post_data['user_id'])->value('department_id');

            $taskModel = new Task();
            $model = new flowModel();
            
            //如果用户是技术员
            if ($depart_id == 1){//指令任务

                $data = $taskModel->getList($post_data['page']);
                $type = 1;

                $task_new = $taskModel->where('technologist_affirm',0)->field(['id as task_id'])->select();

            }else if ($depart_id == 2 ){ //钢筋班
                $where['f.rebar_id']= 0;
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);
                $type = 2;

                $task_new = $model->where(['rebar_id'=> 0])->field(['task_id'])->select();

            }else if ($depart_id == 3 ){ //制梁班

                $where['f.beam_id']= 0;
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);
                $type = 2;

                $task_new = $model->where(['beam_id'=> 0])->field(['task_id'])->select();

            }else if ($depart_id == 4 ){ //安质部

                $where['f.quality_id']= '';
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);
                $type = 2;

                $task_new = $model->where(['quality_id'=> 0])->field(['task_id'])->select();

            }else if ($depart_id == 5 ){ //实验室

                $where['f.lab_id']= 0;
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);
                $type = 2;

                $task_new = $model->where(['lab_id'=> 0])->field(['task_id'])->select();

            }else if ($depart_id == 6 ){ //物机部

                $where['f.machine_id']= 0;
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);
                $type = 2;

                $task_new = $model->where(['machine_id'=> 0])->field(['task_id'])->select();

            }else if ($depart_id == 7 ){//预应力班

                $where['f.prestress_id']= 0;
                $where['f.process_id']= 18;//进行到钢绞线穿束
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);

                $type = 2;

                $task_new = $model->where(['prestress_id'=> 0,'process_id'=>18])->field(['task_id'])->select();

            }else if ($depart_id == 8 ){//拌合站

                $where['f.blend_id']= 0;
                $where['f.process_id']= 13;//进行到准备混凝土设备
                $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);
                $type = 2;

                $task_new = $model->where(['blend_id'=> 0,'process_id'=>13])->field(['task_id'])->select();

            }else{//工程部长和管理组(没有新任务)
                $task_new=[];
            }

            $arr_new=[];//新任务id数组
            $arr_read=[];//已读任务id数组
            //新任务数组
            foreach ($task_new as &$item) {
                $arr_new[]=$item['task_id'];
            }

            //已读任务
            $task_read= Db::name('task_read')->where('user_id',$post_data['user_id'])->field('task_id')->select();

            //已读任务数组
            foreach ($task_read as &$item) {
                $arr_read[]=$item['task_id'];
            }

            if (!empty($arr_new)){
                foreach ($arr_new as &$v) {
                    if (!in_array($v,$arr_read)){//新任务id  是否在 已读任务数组里
                        //不在数组里, 写入已读表
                        $info['user_id']=$post_data['user_id'];
                        $info['task_id']=$v;
                        $read->add($info);//添加查看记录
                    }
                }
            }

            return $crypt->response(['code' => 200, 'message' => '成功','type' => $type, 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //获取执行中任务列表
    public function getDoingTaskList(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'TaskFlow');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取部门id
            $userModel = new User();
            $depart_id = $userModel->where('id',$post_data['user_id'])->value('department_id');


            //根据用户部门,确定查询条件
            if ($depart_id == 1 ){ //如果用户是技术员

                $where['f.status']= ['neq',2];
                $where['f.technologist_id ']= $post_data['user_id'];
            }else if ($depart_id == 2 ){ //钢筋班

                $where['f.status']= 1;
                $where['f.rebar_id']= $post_data['user_id'];

            }else if ($depart_id == 3 ){ //制梁班

                $where['f.status']= 1;
                $where['f.beam_id']= $post_data['user_id'];

            }else if ($depart_id == 4 ){ //安质部

                $where['f.status']= 1;
                $where['f.quality_id']= $post_data['user_id'];

            }else if ($depart_id == 5 ){ //实验室

                $where['f.status']= 1;
                $where['f.lab_id']= $post_data['user_id'];

            }else if ($depart_id == 6 ){ //物机部

                $where['f.status']= 1;
                $where['f.machine_id']= $post_data['user_id'];

            }else if ($depart_id == 7 ){//预应力班

                $where['f.status']= 1;
                $where['f.prestress_id']= $post_data['user_id'];

            }else if ($depart_id == 8 ){//拌合站

                $where['f.status']= 1;
                $where['f.blend_id']= $post_data['user_id'];

            }else{//管理组 和 工程部长
                $where['f.status']= 1;
            }

            $model = new flowModel();
            $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);


            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data],true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //获取已完成任务列表
    public function getFinishTaskList(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'TaskFlow');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取部门id
            $userModel = new User();
            $depart_id = $userModel->where('id',$post_data['user_id'])->value('department_id');


            //根据用户部门,确定查询条件
            if ($depart_id == 1 ){ //如果用户是技术员

                $where['f.status']= 2;
                $where['f.technologist_id ']= $post_data['user_id'];
            }else if ($depart_id == 2 ){ //钢筋班

                $where['f.status']= 2;
                $where['f.rebar_id']= $post_data['user_id'];

            }else if ($depart_id == 3 ){ //制梁班

                $where['f.status']= 2;
                $where['f.beam_id']= $post_data['user_id'];

            }else if ($depart_id == 4 ){ //安质部

                $where['f.status']= 2;
                $where['f.quality_id']= $post_data['user_id'];

            }else if ($depart_id == 5 ){ //实验室

                $where['f.status']= 2;
                $where['f.lab_id']= $post_data['user_id'];

            }else if ($depart_id == 6 ){ //物机部

                $where['f.status']= 2;
                $where['f.machine_id']= $post_data['user_id'];

            }else if ($depart_id == 7 ){//预应力班

                $where['f.status']= 2;
                $where['f.prestress_id']= $post_data['user_id'];

            }else if ($depart_id == 8 ){//拌合站

                $where['f.status']= 2;
                $where['f.blend_id']= $post_data['user_id'];

            }else{//管理组 和 工程部长
                $where['f.status']= 2;
            }

            $model = new flowModel();
            $data = $model->getUserTask($where,$post_data['page'],$post_data['user_id']);


            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }








}

