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
use app\common\model\Message as messageModel;
use mrmiao\encryption\RSACrypt;
use app\common\model\TaskFlow as flowModel;




class Message extends ApiBase
{

    //获取用户消息列表(一级页面)
    public function getMessageByTask(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Message.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);


            $model = new messageModel();
            $list = $model->getTaskMessage($post_data,$post_data['page']);


            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $list],true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //获取用户消息列表(二级页面)
    public function getMessage(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Message.getMessage');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取消息
            $model = new messageModel();
            $list = $model->getMessage($post_data,$post_data['page']);

            //修改消息状态
            $model->changeStatus($post_data);


            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $list],true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }




}

