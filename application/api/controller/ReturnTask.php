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
use app\common\model\ReturnTask as ReturnTaskModel;
use mrmiao\encryption\RSACrypt;
use think\Db;


//返工
class ReturnTask extends ApiBase
{

    //我的返工
    public function getUserReturn(RSACrypt $crypt,ReturnTaskModel $returnTask)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ReturnTask.get');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $depart = Db::name('user')->where('id',$post_data['user_id'])->value('department_id');

            if (in_array($depart,[1,4])){//如果是技术员，安质部
                $where['r.user_id'] = $post_data['user_id'];//查发布人
            }else{//其他部门
                $where['r.duty_id'] = $post_data['user_id'];//查负责人
            }
            //获取返工列表
            $data = $returnTask->getReturnList($where,$post_data['user_id'],$post_data['page']);


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //删除返工记录
    public function delUserReturn(RSACrypt $crypt,ReturnTaskModel $returnTask)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ReturnTask.del');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $user_id = $returnTask->where('id',$post_data['return_id'])->value('duty_id');
            //判断用户id是否正确
            if ($user_id != $post_data['user_id'])
                return $crypt->response(['code' => 400, 'message' => '该用户没有删除权限'],true);


            //执行删除
            $returnTask->delReturn($post_data['return_id']);


            return $crypt->response(['code' => 200, 'message' => '删除成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //查看返工详情
    public function getReturnDetail(RSACrypt $crypt,ReturnTaskModel $returnTask)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ReturnTask.detail');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取返工详情
            $data = $returnTask->getReturnDetail($post_data['return_id']);


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }





}

