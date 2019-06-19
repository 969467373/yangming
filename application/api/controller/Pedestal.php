<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;
use app\common\model\Pedestal as PedestalModel;
use mrmiao\encryption\RSACrypt;

//制梁台座
class Pedestal extends ApiBase
{

    public function getPedestalList(RSACrypt $crypt,PedestalModel $pedestal)
    {
        try {
            $data = $pedestal->getPedestalList();

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


    //吊装入模确认时,获取台座列表
    public function getPedestal(RSACrypt $crypt,PedestalModel $mould)
    {
        try {
            $data = $mould->getPedestal();

            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }


}

