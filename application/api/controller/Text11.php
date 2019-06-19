<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\model\Process;
use app\common\model\TaskProcess;
use app\common\tool\Jpush;
use think\Cache;



//测试
class Text11 extends ApiBase
{

    public function push($title,$user_id,$type)
    {
        $push = new Jpush();

        $push->push_user($title,$user_id,$type);

    }



}

