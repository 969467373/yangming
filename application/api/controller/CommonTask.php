<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;

use app\common\model\Overtime;
use app\common\model\TaskProcess;
use app\common\model\ConcretePouring;
use app\common\model\Stripping;
use app\common\model\InitialTension;
use app\common\model\FinalTension;
use app\common\model\CutWire;
use app\common\model\Mudjack;
use app\common\model\Blocking;
use app\common\model\Painting;
use app\common\model\User;
use app\common\model\TaskFlow;
use app\common\tool\Jpush;
use mrmiao\encryption\RSACrypt;
use think\Cache;
use think\Db;


//多部门相关任务
class CommonTask extends ApiBase
{

    //首次领取任务(安质部,实验室,物机部)
    public function getOneTask(RSACrypt $crypt,TaskFlow $taskFlow,User $user)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'CommonTask.one');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);



            //获取部门id
            $depart_id = $user->where('id',$post_data['user_id'])->value('department_id');

            //判断是否被领取
            if ($depart_id == 4){//安质部
                $user_id = $taskFlow->where(['task_id'=>$post_data['task_id']])->value('quality_id');

            }else if ($depart_id == 5){
                $user_id = $taskFlow->where(['task_id'=>$post_data['task_id']])->value('lab_id');

            }else{
                $user_id = $taskFlow->where(['task_id'=>$post_data['task_id']])->value('machine_id');
            }


            if ($user_id != 0)
                return ['code' => 402, 'message' => '该任务已被领取'];

            //按部门存入用户id
            if ($depart_id == 4){//安质部

                $data['quality_id'] = $post_data['user_id'];

            }else if ($depart_id == 5){//试验室

                $data['lab_id'] = $post_data['user_id'];

            }else if ($depart_id == 6){//物机部

                $data['machine_id'] = $post_data['user_id'];
            }

            //修改主流程表
            $taskFlow->editFlow($post_data['task_id'],$data);



            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //领取任务
    public function getAffirm(RSACrypt $crypt,User $user,TaskFlow $taskFlow,TaskProcess $taskProcess,ConcretePouring $pouring)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'CommonTask.affirm');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //当前工序状态
            $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                ->value('process_status');

            if ($process_status == 1)
                return ['code' => 402, 'message' => '该任务已在进行中'];

            if ($process_status == 2)
                return ['code' => 402, 'message' => '该任务已完成'];

            //获取部门id
            $post_data['depart_id'] = $user->where('id',$post_data['user_id'])->value('department_id');

            //领取任务
            $taskProcess->beginProcess($post_data);

            switch ($post_data['process_id']){

                case 12;//发布混凝土配比(试验室)

                    //把用户id存进浇筑令表
                    $pouring->where('task_id',$post_data['task_id'])
                        ->update(['lab_id'=>$post_data['user_id'],'lab_time'=>time()]);
                    break;

                case 13;//准备混凝土设备(拌合站)

                    //把用户id存进主流程
                    $taskFlow->where('task_id',$post_data['task_id'])
                        ->update(['blend_id'=>$post_data['user_id']]);
                    break;

                case 14;//砼浇筑(制梁班)
                    //把用户id存进浇筑令表
                    $pouring->where('task_id',$post_data['task_id'])
                        ->update(['beam_id'=>$post_data['user_id'],'beam_time'=>time(),'pour_date'=>time()]);
                    break;

                case 17;//拆模(制梁班)
                    //把用户id存进拆模表
                    $model = new Stripping();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['beam_id'=>$post_data['user_id'],'beam_time'=>time(),'stripping_time'=>time()]);
                    break;

                case 18;//钢绞线穿束(预应力班)
                    //把用户id存进拆模表
                    $taskFlow->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id']]);
                    break;

                case 20;//张拉(预应力班)
                    //把用户id存进预张拉表
                    $model = new InitialTension();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id'],'prestress_time'=>time()]);
                    break;

                case 23;//终张拉(预应力班)
                    //把用户id存进终张拉表
                    $model = new FinalTension();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id'],'prestress_time'=>time()]);
                    break;

                case 25;//割丝(预应力班)
                    //把用户id存进割丝表
                    $model = new CutWire();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id'],'prestress_time'=>time()]);
                    break;

                case 27;//压浆(预应力班)
                    //把用户id存进压浆表
                    $model = new Mudjack();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id'],'prestress_time'=>time()]);
                    break;

                case 29;//封端(预应力班)
                    //把用户id存进封端表
                    $model = new Blocking();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id'],'prestress_time'=>time()]);
                    break;

                case 31;//涂刷(预应力班)
                    //把用户id存进预防水涂刷表
                    $model = new Painting();
                    $model->where('task_id',$post_data['task_id'])
                        ->update(['prestress_id'=>$post_data['user_id'],'prestress_time'=>time()]);
                    break;

            }


            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //领取任务(无关人员)
    public function otherAffirm(RSACrypt $crypt,User $user,TaskProcess $taskProcess)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'CommonTask.affirm');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取部门id
            $depart_id = $user->where('id',$post_data['user_id'])->value('department_id');

            switch ($depart_id)
            {
                case 2://钢筋班
                    $data['rebar_id'] = $post_data['user_id'];
                    break;
                case 3://制梁班
                    $data['beam_id'] = $post_data['user_id'];
                    break;
                case 4://安质部
                    $data['quality_id'] = $post_data['user_id'];
                    break;
                case 5://试验室
                    $data['lab_id'] = $post_data['user_id'];
                    break;
                case 6://物机部
                    $data['machine_id'] = $post_data['user_id'];
                    break;
                case 7://预应力班
                    $data['prestress_id'] = $post_data['user_id'];
                    break;
                case 8://拌合站
                    $data['blend_id'] = $post_data['user_id'];
                    break;
            }

            //修改子流程表(添加用户id)
            $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                ->update($data);

            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //任务完成
    public function finishTask(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcess $taskProcess,User $user)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'CommonTask.finish');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //当前任务状态
            $process_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                ->value('process_status');

            if ($process_status == 0)
                return ['code' => 402, 'message' => '该任务尚未领取'];

            if ($process_status == 2)
                return ['code' => 402, 'message' => '该任务已完成'];


            //修改子流程表
            $taskProcess->editProcess($post_data);

            $data2['task_id'] = $post_data['task_id'];
            if ($post_data['process_id'] = 14){//砼浇筑
                $data2['process_id'] = $post_data['process_id'] + 2 ;//跳过砼养护
            }else{
                $data2['process_id'] = $post_data['process_id'] + 1 ;
            }

            switch ($data2['process_id']){
                case 9;//模端内模安装

                    $data2['receive_department'] = '技术员,制梁班,安质部';//领取部门
                    $data2['finish_department'] = '制梁班';//完成部门

                    break;

                case 10;//检验

                    $data2['receive_department'] = '技术员,安质部';//领取部门
                    $data2['finish_department'] = '技术员,安质部';//完成部门

                    break;

                case 14;//砼浇筑

                    $data2['receive_department'] = '制梁班';//领取部门
                    $data2['finish_department'] = '制梁班';//完成部门

                    break;

                case 15;//砼养护

                    $data2['receive_department'] = '制梁班';//领取部门
                    $data2['finish_department'] = '制梁班';//完成部门

                    break;

                case 16;//拆模通知单

                    $data2['receive_department'] = '技术员,试验室,安质部';//领取部门
                    $data2['finish_department'] = '试验室';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 18;//钢绞线穿束

                    $data2['receive_department'] = '预应力班';//领取部门
                    $data2['finish_department'] = '预应力班';//完成部门

                    break;

                case 19;//初张拉通知单

                    $data2['receive_department'] = '技术员,试验室';//领取部门
                    $data2['finish_department'] = '试验室';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 21;//起移梁

                    $data2['receive_department'] = '技术员,制梁班';//领取部门
                    $data2['finish_department'] = '制梁班';//完成部门

                    break;

                case 22;//终张拉通知单

                    $data2['receive_department'] = '技术员,试验室';//领取部门
                    $data2['finish_department'] = '试验室';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 24;//割丝通知单

                    $data2['receive_department'] = '技术员';//领取部门
                    $data2['finish_department'] = '技术员';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 26;//压浆通知单

                    $data2['receive_department'] = '技术员';//领取部门
                    $data2['finish_department'] = '技术员';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 28;//封端通知单

                    $data2['receive_department'] = '技术员';//领取部门
                    $data2['finish_department'] = '技术员';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 30;//防水涂刷通知单

                    $data2['receive_department'] = '技术员';//领取部门
                    $data2['finish_department'] = '技术员';//完成部门
                    $data2['inform_paper'] = 1;//是否是拆模通知单

                    break;

                case 32;//成品检测

                    $data2['receive_department'] = '技术员,安质部';//领取部门
                    $data2['finish_department'] = '安质部';//完成部门

                    break;
            }


            //新增下一步工序
            $taskProcess->addProcess($data2);

            //主流程表工序自增1
            $taskFlow->where('task_id',$post_data['task_id'])->setInc('process_id');

            if ($data2['process_id'] == 18){//钢绞线穿束
                //给预应力班 推送消息(新任务)
                $push_arr = $user->getDepartUser(7);

                $push = new Jpush();
                foreach ($push_arr as $item){
                    $push->push_user('您有新任务待领取',$item['id'],1);
                }

            }


            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //判断任务是否超时
    public function overtimeTask(RSACrypt $crypt,TaskFlow $taskFlow,TaskProcess $taskProcess,User $user,Overtime $overtime)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'CommonTask.over');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            if (!in_array($post_data['process_id'],[6,8,2,4,9,14,15,17,21,5,7,10,32,18,20,23,27,29,31]))
                return ['code' => 402, 'message' =>'该工序没有限制时长'];

            //获取缓存
            $cache= Cache::get("{$post_data['task_id']}_time");

            //限制时长
            $time = $cache[$post_data['process_id']]['duration'] * 3600;

            //已用 大于 限制时间(超时)
            if ($post_data['used_time'] > $time){//执行

                //超时状态
                $overtime_status = $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                    ->value('overtime_status');

                //判断是否已修改过状态
                if ($overtime_status == 0) {//还未修改状态
                    //1.子流程状态
                    $taskProcess->where(['task_id'=>$post_data['task_id'],'process_id'=>$post_data['process_id']])
                        ->update(['overtime_status'=>1]);

                    //2.给相关人员发超时提醒
                    if (in_array($post_data['process_id'],[6,8])){//钢筋班的任务
                        $department_id = 2;
                        $field = 'rebar_id';
                    }else if (in_array($post_data['process_id'],[2,4,9,14,15,17,21])){//制梁班的任务
                        $department_id = 3;
                        $field = 'beam_id';
                    }else if (in_array($post_data['process_id'],[5,7,10,32])){//安质部任务
                        $department_id = 4;
                        $field = 'quality_id';
                    }else if (in_array($post_data['process_id'],[18,20,23,27,29,31])){//预应力班任务
                        $department_id = 7;
                        $field = 'prestress_id';
                    }
                    //负责人id
                    $duty_id = $taskFlow->where('task_id',$post_data['task_id'])->value($field);

                    $users = $user->where('department_id',9)->field('id')->select();
                    $leader = $user->where('department_id',10)->select();
                    //部长id
                    $leader_id = $leader[0]['id'];

                    foreach ($users as $v){
                        $a[] = $v['id'];

                    }
                    $str = implode(',',$a);
                    $str = $str.','.$leader_id.','.$duty_id;//需要发消息的人

                    $user_arr = explode(',',$str);

                    foreach($user_arr as $k=>$v)
                    {
                        $data[$k] =[
                            'user_id'=>$v,
                            'task_id'=>$post_data['task_id'],
                            'process_id'=>$post_data['process_id'],
                            'department_id' => $department_id,
                            'duty_id' => $duty_id,
                        ];

                        //推送消息
                        $push = new Jpush();
                        $push->push_user('您有新的超时提醒',$v,3);
                    }

                    //批量新增超时提醒
                    $overtime->saveAll($data);
                }else{
                    return ['code' => 202, 'message' => '任务已处于超时状态'];
                }
            }else{

                return ['code' => 202, 'message' => '任务未超时'];
            }


            return $crypt->response(['code' => 200, 'message' => '超时状态已修改,发送超时提醒成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }



    //获取指令文档信息
    public function getDocument(RSACrypt $crypt)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'CommonTask.document');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $data = [];
            //梁型预制指令
            $data1['id'] = 1;
            $data1['depart_id'] = 0;
            $data1['has'] = 1;
            $data1['edit'] = 0;
            $data1['add'] = 0;
            array_push($data,$data1);
            //制梁通知单
            $data2['id'] = 2;
            $data2['depart_id'] = 0;
            $data2['has'] = 1;
            $data2['edit'] = 0;
            $data2['add'] = 0;
            array_push($data,$data2);
            //工序时效
            $data3['id'] = 3;
            $data3['depart_id'] = 0;
            $data3['has'] = 1;
            $data3['edit'] = 0;
            $data3['add'] = 0;
            array_push($data,$data3);

            $post_data['process_id'] = Db::name('task_flow')->where('task_id',$post_data['task_id'])->value('process_id');

            if (strstr($post_data['process_id'],',')){//同时进行两个工序
                for ($i=4; $i<13;$i++)
                {
                    $arr['id'] = $i;
                    $arr['depart_id'] = 0;
                    $arr['has'] = 0;
                    $arr['edit'] = 0;
                    $arr['add'] = 0;
                    array_push($data,$arr);
                }
            }else{//只有一个工序
                if ($post_data['process_id'] < 8){//并行任务
                    for ($i=4; $i<13;$i++)
                    {
                        $arr['id'] = $i;
                        $arr['depart_id'] = 0;
                        $arr['has'] = 0;
                        $arr['edit'] = 0;
                        $arr['add'] = 0;
                        array_push($data,$arr);
                    }
                }else{//合并任务
                    //当前工序状态
                    $process_status = Db::name('task_process')
                        ->where(['task_id'=>$post_data['task_id'], 'process_id'=>$post_data['process_id']])
                        ->value('process_status');

                    //浇筑令(11)
                    $data4['id'] = 4;
                    $data4['depart_id'] = 1;//技术员
                    $has = Db::name('concrete_pouring')->where('task_id',$post_data['task_id'])->value('id');
                    $data4['has'] = $has==''?0:1;
                    if ($post_data['process_id']==12&&$process_status==0){//处于下一工序,且未领取
                        $data4['edit'] = 1;
                    }else{
                        $data4['edit'] = 0;
                    }
                    if ($post_data['process_id']==11&&$process_status==1){//处于当前工序,已领取任务
                        $data4['add'] = 1;
                    }else{
                        $data4['add'] = 0;
                    }
                    array_push($data,$data4);

                    //混凝土配比(12)
                    $data5['id'] = 5;
                    $data5['depart_id'] = 5;//试验室
                    $has = Db::name('concrete_ratio')->where('task_id',$post_data['task_id'])->value('id');
                    $data5['has'] = $has==''?0:1;
                    if ($post_data['process_id']==13&&$process_status==0){//处于下一工序,且未领取
                        $data5['edit'] = 1;
                    }else{
                        $data5['edit'] = 0;
                    }
                    if ($post_data['process_id']==12&&$process_status==1){//处于当前工序,已领取任务
                        $data5['add'] = 1;
                    }else{
                        $data5['add'] = 0;
                    }
                    array_push($data,$data5);

                    //拆模通知单(16)
                    $data6['id'] = 6;
                    $data6['depart_id'] = 5;//试验室
                    $has = Db::name('stripping')->where('task_id',$post_data['task_id'])->value('id');
                    $data6['has'] = $has==''?0:1;
                    if ($post_data['process_id']==17&&$process_status==0){//处于下一工序,且未领取
                        $data6['edit'] = 1;
                    }else{
                        $data6['edit'] = 0;
                    }
                    if ($post_data['process_id']==16&&$process_status==1){//处于当前工序,已领取任务
                        $data6['add'] = 1;
                    }else{
                        $data6['add'] = 0;
                    }
                    array_push($data,$data6);

                    //预张拉(19)
                    $data7['id'] = 7;
                    $data7['depart_id'] = 5;//试验室
                    $has = Db::name('initial_tension')->where('task_id',$post_data['task_id'])->value('id');
                    $data7['has'] = $has==''?0:1;
                    if ($post_data['process_id']==20&&$process_status==0){//处于下一工序,且未领取
                        $data7['edit'] = 1;
                    }else{
                        $data7['edit'] = 0;
                    }
                    if ($post_data['process_id']==19&&$process_status==1){//处于当前工序,已领取任务
                        $data7['add'] = 1;
                    }else{
                        $data7['add'] = 0;
                    }
                    array_push($data,$data7);

                    //终张拉(22)
                    $data8['id'] = 8;
                    $data8['depart_id'] = 5;//试验室
                    $has = Db::name('final_tension')->where('task_id',$post_data['task_id'])->value('id');
                    $data8['has'] = $has==''?0:1;
                    if ($post_data['process_id']==23&&$process_status==0){//处于下一工序,且未领取
                        $data8['edit'] = 1;
                    }else{
                        $data8['edit'] = 0;
                    }
                    if ($post_data['process_id']==22&&$process_status==1){//处于当前工序,已领取任务
                        $data8['add'] = 1;
                    }else{
                        $data8['add'] = 0;
                    }
                    array_push($data,$data8);

                    //割丝(24)
                    $data9['id'] = 9;
                    $data9['depart_id'] = 1;//技术员
                    $has = Db::name('cut_wire')->where('task_id',$post_data['task_id'])->value('id');
                    $data9['has'] = $has==''?0:1;
                    if ($post_data['process_id']==25&&$process_status==0){//处于下一工序,且未领取
                        $data9['edit'] = 1;
                    }else{
                        $data9['edit'] = 0;
                    }
                    if ($post_data['process_id']==24&&$process_status==1){//处于当前工序,已领取任务
                        $data9['add'] = 1;
                    }else{
                        $data9['add'] = 0;
                    }
                    array_push($data,$data9);

                    //压浆(26)
                    $data10['id'] = 10;
                    $data10['depart_id'] = 1;//技术员
                    $has = Db::name('mudjack')->where('task_id',$post_data['task_id'])->value('id');
                    $data10['has'] = $has==''?0:1;
                    if ($post_data['process_id']==27&&$process_status==0){//处于下一工序,且未领取
                        $data10['edit'] = 1;
                    }else{
                        $data10['edit'] = 0;
                    }
                    if ($post_data['process_id']==26&&$process_status==1){//处于当前工序,已领取任务
                        $data10['add'] = 1;
                    }else{
                        $data10['add'] = 0;
                    }
                    array_push($data,$data10);

                    //封端(28)
                    $data11['id'] = 11;
                    $data11['depart_id'] = 1;//技术员
                    $has = Db::name('blocking')->where('task_id',$post_data['task_id'])->value('id');
                    $data11['has'] = $has==''?0:1;
                    if ($post_data['process_id']==29&&$process_status==0){//处于下一工序,且未领取
                        $data11['edit'] = 1;
                    }else{
                        $data11['edit'] = 0;
                    }
                    if ($post_data['process_id']==28&&$process_status==1){//处于当前工序,已领取任务
                        $data11['add'] = 1;
                    }else{
                        $data11['add'] = 0;
                    }
                    array_push($data,$data11);

                    //封端(30)
                    $data12['id'] = 12;
                    $data12['depart_id'] = 1;//技术员
                    $has = Db::name('painting')->where('task_id',$post_data['task_id'])->value('id');
                    $data12['has'] = $has==''?0:1;
                    if ($post_data['process_id']==31&&$process_status==0){//处于下一工序,且未领取
                        $data12['edit'] = 1;
                    }else{
                        $data12['edit'] = 0;
                    }
                    if ($post_data['process_id']==30&&$process_status==1){//处于当前工序,已领取任务
                        $data12['add'] = 1;
                    }else{
                        $data12['add'] = 0;
                    }
                    array_push($data,$data12);
                }
            }

            return $crypt->response(['code' => 200, 'message' => '成功','data' => $data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }




}

