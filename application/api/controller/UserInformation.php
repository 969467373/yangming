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
use app\common\model\TaskFlow;
use app\common\model\User;
use app\common\model\Message;
use app\common\model\Overtime;
use app\common\model\Web;
use mrmiao\encryption\RSACrypt;
use app\common\model\TaskFlow as flowModel;
use think\Db;


class UserInformation extends ApiBase
{

    //个人中心首页
    public function getUserIndex(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'User.getInformation');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result], true);

            //获取用户部门
            $model = new User();
            $depart_id = $model->where('id', $post_data['user_id'])->value('department_id');
            $data = $model->getInformation($post_data['user_id']);

            $taskModel = new Task();
            $flowModel = new flowModel();

            //根据用户部门,确定查询条件
            if ($depart_id == 1) { //如果用户是技术员
                //新任务
                $data['new'] = $taskModel->getList()->count();

                //进行中的任务
                $where2['f.status'] = ['neq', 2];
                $where2['f.technologist_id '] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.technologist_id '] = $post_data['user_id'];
            } else if ($depart_id == 2) { //钢筋班
                //新任务
                $where1['f.rebar_id']= 0;
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.rebar_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.rebar_id'] = $post_data['user_id'];

            } else if ($depart_id == 3) { //制梁班
                //新任务
                $where1['f.beam_id']= 0;
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.beam_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.beam_id'] = $post_data['user_id'];

            } else if ($depart_id == 4) { //安质部
                //新任务
                $where1['f.quality_id']= 0;
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.quality_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.quality_id'] = $post_data['user_id'];

            } else if ($depart_id == 5) { //实验室
                //新任务
                $where1['f.lab_id'] = 0;
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.lab_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.lab_id'] = $post_data['user_id'];

            } else if ($depart_id == 6) { //物机部
                //新任务
                $where1['f.machine_id'] = 0;
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.machine_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.machine_id'] = $post_data['user_id'];


            } else if ($depart_id == 7) {//预应力班(新任务数据需要改)
                //新任务
                $where1['f.prestress_id']= 0;
                $where1['f.process_id']= 18;//进行到钢绞线穿束
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.prestress_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.prestress_id'] = $post_data['user_id'];


            } else if ($depart_id == 8) {//拌合站(新任务数据需要改)
                //新任务
                $where1['f.prestress_id']= 0;
                $where1['f.process_id']= 13;//进行到准备混凝土设备
                $list = $flowModel->getUserTask($where1,1,$post_data['user_id']);

                $data['new'] = $list['total'];
                //进行中的任务
                $where2['f.status'] = 1;
                $where2['f.blend_id'] = $post_data['user_id'];

                //完成中的任务
                $where3['f.status'] = 2;
                $where3['f.blend_id'] = $post_data['user_id'];

            } else {//管理组 和 工程部长
                //没有新任务
                $data['new'] = 0;
                //进行中的任务
                $where2['f.status'] = 1;

                //完成中的任务
                $where3['f.status'] = 2;

            }

            //执行中的数量
            //$data['doing']= $flowModel->getUserTask($where2)->count();
            $list = $flowModel->getUserTask($where2,1,$post_data['user_id']);
            $data['doing'] = $list['total'];

            //已完成的数量
            //$data['finish']= $flowModel->getUserTask($where3)->count();
            $list = $flowModel->getUserTask($where3,1,$post_data['user_id']);
            $data['finish'] = $list['total'];

            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //获取用户资料
    public function getInfomation(RSACrypt $crypt, User $user)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'User.getInformation');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result], true);

            //获取用户资料
            $data = $user->getInformation($post_data['user_id']);


            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //修改密码
    public function changPassword(RSACrypt $crypt, User $user)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'User.changePassword');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result], true);

            //修改密码
            $user->changePassword(

                $post_data['user_id'],
                $post_data['origin_password'],
                $post_data['new_password']

            );

            return $crypt->response(['code' => 200, 'message' => '密码修改成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //修改头像
    public function changeAvatar()
    {
        try {
            $post_data = request()->param();


            $file = request()->file('avatar');

            //验证参数
            $result = $this->validate($post_data, 'User.changeAvatar');
            if (true !== $result)
                return ['code' => 400, 'message' => $result];


            $avatar_info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'avatar'.DS.date('Ymd'));
            $post_data['avatar']  = 'http://'.$_SERVER['HTTP_HOST'].'/uploads/avatar/' . date('Ymd') . '/' . $avatar_info->getFilename();


            //修改信息
            $user = new User();
            $user->changeUserAvatar($post_data);

            return ['code' => 200, 'message' => "修改成功",'avatar'=>$post_data['avatar']];

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }
    }


    //获取新消息数量和超时提醒数量
    public function getMessageNumber(RSACrypt $crypt,Message $message,Overtime $overtime)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'User.getInformation');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result], true);

            //获取新消息数量
            $data['message_number'] = $message->getNewNumber($post_data['user_id']);
            //获取新超时提醒数量
            $data['overtime_number'] = $overtime->getNewOvertime($post_data['user_id']);

            //获取用户部门
            $model = new User();
            $depart_id = $model->where('id', $post_data['user_id'])->value('department_id');

            $taskModel = new Task();
            $flowModel = new flowModel();

            //根据用户部门,确定查询条件
            if ($depart_id == 1) { //如果用户是技术员
                //新任务
                $task_new = $taskModel->where('technologist_affirm',0)->field(['id as task_id'])->select();

            } else if ($depart_id == 2) { //钢筋班
                //新任务
                $where['rebar_id']= 0;
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else if ($depart_id == 3) { //制梁班
                //新任务
                $where['beam_id']= 0;
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else if ($depart_id == 4) { //安质部
                //新任务
                $where['quality_id']= 0;
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else if ($depart_id == 5) { //实验室
                //新任务
                $where['lab_id'] = 0;
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else if ($depart_id == 6) { //物机部
                //新任务
                $where['machine_id'] = 0;
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else if ($depart_id == 7) {//预应力班(新任务数据需要改)
                //新任务
                $where['prestress_id']= 0;
                $where['process_id']= 18;//进行到钢绞线穿束
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else if ($depart_id == 8) {//拌合站(新任务数据需要改)
                //新任务
                $where['prestress_id']= 0;
                $where['process_id']= 13;//进行到准备混凝土设备
                $task_new = $flowModel->where($where)->field(['task_id'])->select();

            } else {//管理组 和 工程部长
                //没有新任务
                $task_new = [];
            }

            $arr_new=[];//新任务id数组
            $arr_read =[];//已读任务id数组

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

            $count = 0;
            if (!empty($arr_new)){
                foreach ($arr_new as &$v) {
                    if (!in_array($v,$arr_read)){//新任务id  是否在 已读任务数组里
                        $count +=1;//不在数组中,值加1;
                    }
                }
            }
/*            $data['arr1'] = $arr_new;
            $data['arr2'] = $arr_read;
            $data['count'] = $count;*/

            if ($count != 0){//值不为0 ,则有红点
                $data['red'] = 1;
            }else{//值为0,没红点
                $data['red'] = 0;
            }


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //获取关于我们内容
    public function getWeb(RSACrypt $crypt,Web $web)
    {
        try {

            $data = $web->getWeb(1) ;

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }

}

