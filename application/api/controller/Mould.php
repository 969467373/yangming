<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;
use app\common\model\Mould as MouldModel;
use mrmiao\encryption\RSACrypt;


//绑扎胎具
class Mould extends ApiBase
{

    //获取绑扎胎具列表
    public function getMouldList(RSACrypt $crypt,MouldModel $mould)
    {
        try {
            $data = $mould->getMouldList();

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }

    //新建任务时,获取胎具列表
    public function getMould(RSACrypt $crypt,MouldModel $mould)
    {
        try {
            $data = $mould->getMould();

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }




}

