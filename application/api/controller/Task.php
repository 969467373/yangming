<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;
use app\common\model\MouldLog;
use app\common\model\Task as taskModel;
use app\common\model\User;
use app\common\tool\Jpush;
use mrmiao\encryption\RSACrypt;
use think\Db;


class Task extends ApiBase
{
    

    //发布梁型预制指令
    public function sendTask(RSACrypt $crypt,User $user)
    {
        Db::startTrans();
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Task.add');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);
            
            $model = new taskModel();
            $model->addTask($post_data);

            //给技术员推送消息
            $tec = $user->getDepartUser(1);

            $push = new Jpush();
            foreach ($tec as $item){
                $push->push_user('您有新任务待领取',$item['id'],1);
            }
            Db::commit();

            return $crypt->response(['code' => 200, 'message' => '成功',],true);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //技术员查看指令
    public function getInstruct(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Task.instruct');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);


            $model = new taskModel();
            $data = $model->getInstructList($post_data);


            return $crypt->response(['code' => 200, 'message' => '成功','data' => $data],true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



}

