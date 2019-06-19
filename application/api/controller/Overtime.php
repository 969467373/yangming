<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\User;
use app\common\model\Overtime as OvertimeModel;
use mrmiao\encryption\RSACrypt;


//超时
class Overtime extends ApiBase
{

    //我的超时
    public function getUserOvertime(RSACrypt $crypt,OvertimeModel $overtime)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Overtime.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取返工列表
            $data = $overtime->getOvertimeList($post_data,$post_data['page']);


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //超时提醒
    public function getOvertimeMessage(RSACrypt $crypt,OvertimeModel $overtime)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Overtime.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取超时提醒列表
            $data = $overtime->getMessageList($post_data,$post_data['page']);

            //修改超时提醒状态
            $overtime->changeStatus($post_data);

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //删除超时记录
    public function delUserOvertime(RSACrypt $crypt,OvertimeModel $overtime)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Overtime.del');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $user_id = $overtime->where('id',$post_data['time_id'])->value('user_id');


            //判断用户id是否正确
            if ($user_id != $post_data['user_id'])
                return $crypt->response(['code' => 400, 'message' => '该用户没有删除权限'],true);

            //执行删除
            $overtime->delOvertime($post_data['time_id']);


            return $crypt->response(['code' => 200, 'message' => '删除成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


}

