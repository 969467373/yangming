<?php

namespace app\console\controller;



use app\common\controller\ConsoleBase;
use app\common\model\Pedestal as PedestalModel;

use app\common\model\PedestalLog;
use app\common\tool\Jump;
use think\Controller;
use think\Request;


class Pedestal extends ConsoleBase

{

    private $title = '台座';


    // @# 设备管理-管理台座-管理台座-index
    public function index()
    {
        $model = new PedestalModel();

        $list = $model->getPedestalList();

        //halt(collection($list)->toArray());
        return $this->fetch('index',[
            'title'=> $this->title,
            'list'=> $list
        ]);
    }


    // @# 设备管理-管理台座-添加台座-add
    public function add()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new PedestalModel();
                $model->addPedestal($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {
                Jump::fail("失败=>{$e->getMessage()}");
            }
        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $view = [
            'title' => $this->title,
        ];

        return view('modify', $view);
    }



    // @# 设备管理-管理台座-编辑台座-edit
    public function edit()
    {
        $request = Request::instance();

        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new PedestalModel();
                $model->edit($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }
        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $id = $request->param('id');
        $data = PedestalModel::get($id);

        $view = [
            'title' => $this->title,
            'data' => $data,
        ];

        return view('modify',$view);
    }

    // @# 设备管理-管理台座-删除台座-delete
    public function delate()
    {
        try {
            //缓存列表入口地址
            session('back_url', $_SERVER['HTTP_REFERER']);

            $model = new PedestalModel();

            $id = request()->param('id');

            $model->where('id',$id)->delete();

            Jump::win('删除成功！', session('back_url'));

        } catch (\Exception $e) {

            Jump::fail("删除失败=>{$e->getMessage()}");

        }

    }


    // @# 设备管理-管理台座-查看台座使用记录-log
    public function log()
    {
        $model = new PedestalLog();

        //搜索关键字
        $search = Request::instance()->param();
        //halt($search);
        //排序
        $param = Request::instance()->param('order');

        //查询所有
        $where=[];
        $order='l.create_time desc';//默认按时间顺序排列

        if (!empty($param)) {
            if ($param == 'create_time'){
                $order = 'l.create_time desc';
            }else if ($param == 'pedestal_title'){
                $order = 'p.title';
            }
        }
        //任务名称
        if (!empty($search['keyword'])) {

            $where['t.title'] = ['like', '%' . $search['keyword'] . '%'];
        }

        //胎具id
        if (!empty($search['pedestal_id'])&&$search['pedestal_id']){

            $where['l.pedestal_id'] = $search['pedestal_id'];
        }

        //halt($where);

        $list = $model->getList($where,$order);

        $pedestal = (new PedestalModel())->field(['id','title'])->select();

        //halt($list);

        return $this->fetch('log',[
            'title'=> $this->title,
            'list'=> $list,
            'pedestal'=> $pedestal,
            'search'=> $search,
            'order'=> $param,
        ]);

    }


}

