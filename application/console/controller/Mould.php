<?php

namespace app\console\controller;



use app\common\controller\ConsoleBase;
use app\common\model\Mould as MouldModel;

use app\common\model\MouldLog;
use app\common\tool\Jump;
use think\Controller;
use think\Request;


class Mould extends ConsoleBase

{

    private $title = '胎具';


    // @# 设备管理-管理胎具-管理胎具-index
    public function index()
    {
        $model = new MouldModel();

        $list = $model->getMouldList();

        //halt(collection($list)->toArray());
        return $this->fetch('index',[
            'title'=> $this->title,
            'list'=> $list
        ]);
    }


    // @# 设备管理-管理胎具-添加胎具-add
    public function add()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new MouldModel();
                $model->addMould($post_data);

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



    // @# 设备管理-管理胎具-编辑胎具-edit
    public function edit()
    {
        $request = Request::instance();

        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new MouldModel();
                $model->edit($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }
        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $id = $request->param('id');
        $data = MouldModel::get($id);

        $view = [
            'title' => $this->title,
            'data' => $data,
        ];

        return view('modify',$view);
    }

    // @# 设备管理-管理胎具-删除胎具-delete
    public function delate()
    {
        try {
            //缓存列表入口地址
            session('back_url', $_SERVER['HTTP_REFERER']);

            $model = new MouldModel();

            $id = request()->param('id');

            $model->where('id',$id)->delete();

            Jump::win('删除成功！', session('back_url'));

        } catch (\Exception $e) {

            Jump::fail("删除失败=>{$e->getMessage()}");

        }
    }


    // @# 设备管理-管理胎具-查看胎具使用记录-log
    public function log()
    {
        $model = new MouldLog();

        //搜索关键字
        $search = Request::instance()->param();
        //排序
        $param = Request::instance()->param('order');

        //查询所有部门
        $where=[];
        $order='l.create_time desc';//默认按时间顺序排列

        if (!empty($param)) {
            if ($param == 'create_time'){
                $order = 'l.create_time desc';
            }else if ($param == 'mould_title'){
                $order = 'm.title';
            }
        }
        //任务名称
        if (!empty($search['keyword'])) {

            $where['t.title'] = ['like', '%' . $search['keyword'] . '%'];

        }

        //胎具id
        if (!empty($search['mould_id'])&&$search['mould_id']){

            $where['l.mould_id'] = $search['mould_id'];
        }


        $list = $model->getList($where,$order);

        $mould = (new MouldModel())->field(['id','title'])->select();

        //halt($list);

        return $this->fetch('log',[
            'title'=> $this->title,
            'list'=> $list,
            'mould'=> $mould,
            'search'=> $search,
            'order'=> $param,
        ]);

    }

}

