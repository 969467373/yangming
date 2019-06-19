<?php

namespace app\console\controller;

use app\common\controller\ConsoleBase;
use app\common\model\TaskFlow;
use app\common\tool\Jump as Jump;
use app\common\model\TaskProcess;
use app\common\model\MakeBeam;
use app\common\model\User;
use app\common\model\ChangeUser;

use think\Cache;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;


class Task extends ConsoleBase
{

    private $group = 'Task/index';

    private $title = '任务';


    // @# 任务管理-管理任务-管理任务-index
    public function index()
    {
        $model = new MakeBeam;

        //搜索关键字
        $search = Request::instance()->param();

        //查询所有部门
        $where=[];
        $order='f.create_time asc';//默认按时间顺序排列

        //排序
        $param = Request::instance()->param('order');

        //halt($param);
        if (!empty($param)) {

            if ($param == 'create_time'){
                $order = 'f.create_time asc';
            }else if ($param == 'status'){
                $order = 'f.status asc';
            }else if ($param == 'name'){
                $order = 'u.name asc';
            }

        }

        if (!empty($search['keyword'])) {

            $where['mb.title'] = ['like', '%' . $search['keyword'] . '%'];

        }

        //任务状态
        if (!empty($search['status'])&&$search['status']){

            $where['f.status'] = $search['status'];
        }

        $list = $model->getConsoleTask($where,$order);

        $page = $list->render();
        $this->assign('page',$page);

        if (!empty($param)&& $param=='used_time') {

            $array = $list->toArray();
            $list = $this->my_sort($array['data'],'used_time');
        }

        //halt($list);


        return $this->fetch('index',[

            'title'=> $this->title,
            'list'=> $list,
            'search'=> $search,
            'order'=> $param,
        ]);
    }
//    public function index()
//    {
//        $model = new MakeBeam;
//
//        //搜索关键字
//        $search = Request::instance()->post();
//
//        //查询所有部门
//        $where=[];
//        $order=[];
//
//        if (!empty($search['keyword'])) {
//
//            $where['mb.title'] = ['like', '%' . $search['keyword'] . '%'];
//
//        }
//
//        //任务状态
//        if (!empty($search['status'])&&$search['status']){
//
//            $where['f.status'] = $search['status'];
//        }
//
//        $list = $model->getConsoleTask($where,$order);
//
//        //halt($list);
//
//        return $this->fetch('index',[
//
//            'title'=> $this->title,
//            'list'=> $list,
//            'search'=> $search,
//        ]);
//    }


    // @# 任务管理-管理任务-任务排序-order
    /*public function order()
    {
        $model = new MakeBeam;

        //搜索关键字
        $param = Request::instance()->param('order');

        //halt($param);
        $order=[];

        if (!empty($param)) {

            if ($param == 'create_time'){
                $order = 'f.create_time asc';
            }else if ($param == 'status'){
                $order = 'f.status asc';
            }else if ($param == 'name'){
                $order = 'u.name asc';
            }

        }

        $where=[];
        $list = $model->getConsoleTask($where,$order);

        if (!empty($param)&& $param=='used_time') {

            $array = $list->toArray();
            $list = $this->my_sort($array['data'],'used_time');

        }

        //halt($list);

        return $this->fetch('index',[

            'title'=> $this->title,
            'list'=> $list,
            'order'=> $param,

        ]);
    }*/



    // @# 任务管理-管理任务-任务详情-detail
    public function detail()
    {
        $task_id = request()->param('task_id');

        $model = new TaskProcess();

        $list = $model->getConsoleProcess($task_id);

        foreach($list as &$item) {
            //获取工序时长缓存
            $cache = Cache::get("{$task_id}_time");
            if (in_array($item['process_id'],[6,8,2,4,9,14,15,17,21,5,7,10,32,18,20,23,27,29,31])){
                //该工序限制时长(单位:秒)
                $duration = $cache[$item['process_id']]['duration'] * 3600;
                //$item['duration'] = $duration;
                if($item['process_status']==1){//进行中

                    //现在的时间, 减本该完成的时间
                    $seconds = time() - ($item['confirm_time'] + $duration) ;
                    $item['timeout'] = $this->changeTimeType($seconds);

                }else{//返工未领取,或已完成

                    //已用时间, 减限制时间
                    $seconds = $item['used_time'] - $duration ;
                    $item['timeout'] = $this->changeTimeType($seconds);
                }
            }

        }



        //halt($list->toArray());

        return $this->fetch('',[

            'title'=> $this->title,
            'list'=> $list,
            'task_id'=> $task_id,
        ]);


    }


    // @# 任务管理-管理任务-任务相关人员-member
    public function member()
    {
        $task_id = request()->param('task_id');

        $model = new TaskFlow();

        $data = $model->getDetail($task_id);

        return $this->fetch('member',[

            'title'=> $this->title,
            'data'=> $data,
        ]);
    }


    // @# 任务管理-管理任务-更换人员-changeUser
    public function changeUser(ChangeUser $changUser, TaskFlow $taskFlow)
    {

        $request = Request::instance();

        if ($request->isPost())
        {
            Db::startTrans();
            try {
                $post_data = request()->post();

                if ($post_data['old_user'] == $post_data['new_user'])
                    Jump::fail("更换人员与原有人员相同");
                //添加更换记录
                $changUser->add($post_data);


                switch ($post_data['department_id']){

                    case 1;//工程部
                        $data['technologist_id'] = $post_data['new_user'];
                        break;

                    case 2;//钢筋班
                        $data['rebar_id'] = $post_data['new_user'];
                        break;

                    case 3;//制梁班
                        $data['beam_id'] = $post_data['new_user'];
                        break;

                    case 4;//安质部
                        $data['quality_id'] = $post_data['new_user'];
                        break;

                    case 5;//试验室
                        $data['lab_id'] = $post_data['new_user'];
                        break;

                    case 6;//物机部
                        $data['machine_id'] = $post_data['new_user'];
                        break;

                    case 7;//预应力班
                        $data['prestress_id'] = $post_data['new_user'];
                        break;

                    case 8;//拌合站
                        $data['blend_id'] = $post_data['new_user'];
                        break;

                    default;
                        $data=[];
                        break;
                }

                //修改task_flow表
                $taskFlow->where('task_id',$post_data['task_id'])->update($data);

                Db::commit();

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }

        }
        session('back_url', $_SERVER['HTTP_REFERER']);

        $param = request()->param();
        $user= new User();

        $data['task_id'] = $param['task_id'];
        $data['title'] = Db::name('make_beam')->where('task_id',$param['task_id'])->value('title');
        $data['department_id'] = $param['department_id'];
        $data['department'] = Db::name('department')->where('id',$param['department_id'])->value('title');
        $data['old_user'] = $param['old_user'];
        $data['old_username'] = Db::name('user')->where('id',$param['old_user'])->value('name');
        //该部门所有人员
        $depart = $user->getDepartUser($param['department_id']);


        //halt($data);
        return $this->fetch('change_user',[

            'title'=> $this->title,
            'data'=> $data,
            'depart'=> $depart,
        ]);
    }

    // @# 任务管理-管理任务-人员更换记录-changeList
    public function changeList(ChangeUser $changeUser)
    {

        $task_id = request()->param('task_id');

        $list = $changeUser->getChangeList($task_id);

        return $this->fetch('change_list',[

            'title'=> $this->title,
            'list'=> $list,
        ]);
    }



    // @# 任务管理-管理任务-任务导出-inexcel
    public function inexcel()
    {

        $where=[];
        $order=[];
        //获取到要导出的数据，如果有限制条件可以加入where方法
        $model = new MakeBeam;
        $list = $model->getConsoleTask($where,$order)->toArray();

        foreach($list['data'] as &$item)
        {

            if($item['status'] == 0){
                $item['status'] = "未开始";
            } elseif($item['status'] == 1) {
                $item['status'] = "进行中";
            } elseif($item['status'] == 2) {
                $item['status'] = "已完成";
            }

            $item['create_time'] = date('Y-m-d H:i:s',$item['create_time']);

            if ($item['finish_time'] != 0){
                $item['finish_time'] = date('Y-m-d H:i:s',$item['finish_time']);
            }else{
                $item['finish_time'] = '';
            }


        }
        //halt($list['data']);

        $phpexcelSrc = Loader::import('PHPExcel.PHPExcel');//我们将手动下载的phpexcel放置到了extend目录
        $phpexcel =new \PHPExcel();//实例化PHPExcel类对象，方便操作即将生成的excel表格
        $phpexcel->setActiveSheetIndex(0);//选中我们生成的excel文件的第一张工作表
        $sheet=$phpexcel->getActiveSheet();//获取到选中的工作表，方面后面数据插入操作
        $phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);/*设置列宽*/
        $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        //return ('879');
        if(empty($list['data'])){
            //return ['code'=>'400','message'=>'没有数据可导出'];

            Jump::fail("导出失败=>没有数据可导出");
        }
        //die();

        //此处设置的是生成的excel表的第一行标题
        $arr=[
            'task_id'=>'任务id',
            'title'=>'任务名称',
            'name'=>'技术员',
            'status'=>'状态',
            'create_time'=>'开始时间',
            'finish_time'=>'结束时间',
            'used_time'=>'累计用时',
            'return_num'=>'返工次数',
            'overtime_num'=>'超时次数',
        ];
        array_unshift($list['data'], $arr);//将我们上面手动设置的标题信息放到数组中，作为第一行写入数据表
        $currow=0;//因为我们生成的excel表格的行数是从1开始，所以我们预先设置好一个变量，供下面foreach循环的$k使用

        foreach ($list['data'] as $k => $v) {
            $currow=$k+1;//表示从生成的excel表格的第一行开始插入数据
            $sheet->setCellValue('A'.$currow,$v['task_id'])
                ->setCellValue('B'.$currow,$v['title'])
                ->setCellValue('C'.$currow,$v['name'])
                ->setCellValue('D'.$currow,$v['status'])
                ->setCellValue('E'.$currow,$v['create_time'])
                ->setCellValue('F'.$currow,$v['finish_time'])
                ->setCellValue('G'.$currow,$v['used_time'])
                ->setCellValue('H'.$currow,$v['return_num'])
                ->setCellValue('I'.$currow,$v['overtime_num'])
            ;
        }

        $fileName = date("Y_m_d_H_i_s", time()) . ".xls";
        $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表


        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel;charset=UTF-8');//设置下载前的头信息

        header("Content-Disposition: attachment;filename='$fileName'");
        header('Cache-Control: max-age=0');

        $phpwriter=new \PHPExcel_Writer_Excel2007($phpexcel);//此处的2007代表的是高版本的excel表格

        $phpwriter->save('php://output');//生成并下载excel表格

        return;

    }



    // @# 任务管理-管理任务-工序导出-inexcel_process
    public function inexcel_process()
    {

        $task_id = request()->param('task_id');
        //获取到要导出的数据，如果有限制条件可以加入where方法
        $model = new TaskProcess();
        $list = $model->getConsoleProcess($task_id)->toArray();

        $title = Db::name('make_beam')->where('task_id',$task_id)->value('title');
        //halt($list);
        foreach($list['data'] as &$item) {
            //获取工序时长缓存
            $cache = Cache::get("{$task_id}_time");
            if (in_array($item['process_id'],[6,8,2,4,9,14,15,17,21,5,7,10,32,18,20,23,27,29,31])){
                //该工序限制时长(单位:秒)
                $duration = $cache[$item['process_id']]['duration'] * 3600;

                if($item['process_status']==1){//进行中

                    //现在的时间, 减本该完成的时间
                    $seconds = time() - ($item['confirm_time'] + $duration) ;
                    $item['timeout'] = $this->changeTimeType($seconds);

                }else{//返工未领取,或已完成

                    //已用时间, 减限制时间
                    $seconds = $item['used_time'] - $duration ;
                    $item['timeout'] = $this->changeTimeType($seconds);
                }
            }

        }


        foreach ($list['data'] as &$v){
            if($v['process_status'] == 0){
                $v['process_status'] = "未领取";
            } elseif($v['process_status'] == 1) {
                $v['process_status'] = "进行中";
            } elseif($v['process_status'] == 2) {
                $v['process_status'] = "已完成";
            }

            if($v['return_status'] == 0){
                $v['return_status'] = "正常";
            } elseif($v['return_status'] == 1) {
                $v['return_status'] = "返工";
            }


            if($v['overtime_status'] == 0){
                $v['overtime_status'] = "正常";
            } elseif($v['overtime_status'] == 1) {
                $v['overtime_status'] = "超时".$v['timeout'];
            }

        }


        $phpexcelSrc = Loader::import('PHPExcel.PHPExcel');//我们将手动下载的phpexcel放置到了extend目录
        $phpexcel =new \PHPExcel();//实例化PHPExcel类对象，方便操作即将生成的excel表格
        $phpexcel->setActiveSheetIndex(0);//选中我们生成的excel文件的第一张工作表
        $sheet=$phpexcel->getActiveSheet();//获取到选中的工作表，方面后面数据插入操作
        $phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);/*设置列宽*/
        $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $phpexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        //return ('879');
        if(empty($list['data'])){
            //return ['code'=>'400','message'=>'没有数据可导出'];

            Jump::fail("导出失败=>没有数据可导出");
        }
        //die();

        //此处设置的是生成的excel表的第一行标题
        $arr=[
            'process_id'=>'序号',
            'title'=>'工序名称',
            'process_status'=>'进行状态',
            'return_status'=>'是否返工',
            'overtime_status'=>'是否超时',
            'department'=>'负责部门',
            'duty_name'=>'负责人',
        ];
        array_unshift($list['data'], $arr);//将我们上面手动设置的标题信息放到数组中，作为第一行写入数据表
        $sheet->mergeCells('A1:G1');//合并单元格
        $sheet->getStyle('A1')->applyFromArray(//加粗居中
            array(
                'font' => array ('bold' => true),

                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )
            )
        );

        $sheet->setCellValue('A1', '任务名称:'.$title);
        $currow=0;//因为我们生成的excel表格的行数是从1开始，所以我们预先设置好一个变量，供下面foreach循环的$k使用

        //halt($list['data']);
        foreach ($list['data'] as $k => $v) {
            $currow=$k+2;//表示从生成的excel表格的第一行开始插入数据
            $sheet->setCellValue('A'.$currow,$v['process_id'])
                ->setCellValue('B'.$currow,$v['title'])
                ->setCellValue('C'.$currow,$v['process_status'])
                ->setCellValue('D'.$currow,$v['return_status'])
                ->setCellValue('E'.$currow,$v['overtime_status'])
                ->setCellValue('F'.$currow,$v['department'])
                ->setCellValue('G'.$currow,$v['duty_name'])
            ;
        }

        $fileName = date("Y_m_d_H_i_s", time()) . ".xls";
        $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表

        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel;charset=UTF-8');//设置下载前的头信息
        header("Content-Disposition: attachment;filename='$fileName'");
        header('Cache-Control: max-age=0');

        $phpwriter=new \PHPExcel_Writer_Excel2007($phpexcel);//此处的2007代表的是高版本的excel表格

        $phpwriter->save('php://output');//生成并下载excel表格

        return;

    }





    //秒数转换时分秒
    function changeTimeType($seconds){
        if ($seconds >3600){
            $hours =intval($seconds/3600);
            $minutes = $seconds % 3600;
            $time = $hours.":".gmstrftime('%M:%S',$minutes);
        }else{
            $time = gmstrftime('%H:%M:%S',$seconds);
        }
        return$time;
    }


    //按字段排序数组
    function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC )
    {
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }



}
