<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\Process as processModel;
use app\common\model\User;
use mrmiao\encryption\RSACrypt;
use think\Cache;


class Process extends ApiBase
{

    //获取有时效的工序
    public function getProcessTime(RSACrypt $crypt,processModel $process)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Process');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取工时缓存
            $data = Cache::get("{$post_data['task_id']}_time_list");


            if (!$data){//没有缓存
                //查询表中数据
                $where['time_limit'] = 1;
                $data= $process->getProcess($where);
                //存入历史记录(列表)
                Cache::set("{$post_data['task_id']}_time_list",$data);
                //取出缓存
                $data = Cache::get("{$post_data['task_id']}_time_list");
            }


            return $crypt->response(['code' => 200, 'message' => '成功', 'data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



}

