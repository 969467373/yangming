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
class Text extends ApiBase
{


    public function text(Process $process,TaskProcess $taskProcess)
    {

        //查询表中数据
        $where['time_limit'] = 1;
        $data= $process->getProcess($where);

        //存入历史记录(列表)
        Cache::set("1_time_list",$data);


        $data2= $process->getProcess($where)->toArray();
        //存入历史记录(数组)
        foreach($data2['data'] as $k=>$v){

            $arr[$v['id']] =$v;
        }

        Cache::set("1_time",$arr);

        //halt($arr);




        //获取单条
        //Cache::set('1_time',$arr);
        /*$data = $taskProcess->alias('tp')
            ->join('process p','p.id=tp.process_id')
            ->field([
                'tp.process_id',
                'p.title',
                'p.time_limit',
                'tp.process_status',
                'tp.confirm_time',
                'tp.overtime_status',
                'tp.timeout',
                'tp.return_status',
                'tp.technologist_affirm',

            ])
            ->where('task_id',4)
            ->paginate(200, false)
            ->toArray();

        halt($data);
        $time = Cache::get('1_time');

        //halt($time);

        foreach ($data['data'] as &$item){
            if ($item['time_limit'] == 1){
                $item['duration'] = $time[$item['process_id']]['duration'];
            }
        }

        halt($data);*/


    }


    public function del(){

        Cache::clear();

//        $list = Cache::get('1_time_list');
//
//        halt($list);
    }


    //极光全部推送
    public function qqq()
    {
        $title = request()->param('title');
        $content = request()->param('content');

        $push = new Jpush();
        $ok = $push->push_user_all($title,$content);

        return $ok;
    }

    //极光个人推送
    public function www()
    {
        $title = request()->param('title');
        $user_id = request()->param('user_id');

        $push = new Jpush();
        $ok = $push->push_user($title,$user_id,1,2);

        return $ok;
    }






}

