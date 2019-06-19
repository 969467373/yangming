<?php

namespace app\console\controller;

use app\common\controller\ConsoleBase;
use app\common\tool\Jump as Jump;
use app\common\model\Department as departModel;

use think\Controller;
use think\Request;


class Department extends ConsoleBase
{

    private $group = 'Department/index';

    private $title = '部门';


    // @# 部门管理-管理部门-管理部门-index
    public function index()
    {
        $model = new departModel;

        //搜索关键字
        $keyword = Request::instance()->post('keyword');

        //查询所有部门
        $where=[];

        if (!empty($keyword)) {

            $where['title'] = ['like', '%' . $keyword . '%'];

        }

        $list = $model->getConsoleList($where);

        //halt($list);

        return $this->fetch('index',[

            'title'=> $this->title,
            'list'=> $list,
            'keyword'=> $keyword,
        ]);
    }

    // @# 部门管理-管理部门-添加部门-add
    public function add()
    {
        $request = Request::instance();
        if ($request->isPost()) {

            try {

                $post_data = $request->post();

                $model = new departModel();
                $model->add($post_data);

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



    // @# 部门管理-管理部门-编辑部门-edit
    public function edit()
    {
        $request = Request::instance();

        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new departModel();
                $model->edit($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }

        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $id = $request->param('id');
        $data = departModel::get($id);

        $view = [
            'title' => $this->title,
            'data' => $data,
        ];

        return view('modify',$view);
    }

    // @# 部门管理-管理部门-删除部门-delete
    public function delate()
    {
        try {
            //缓存列表入口地址

            session('back_url', $_SERVER['HTTP_REFERER']);

            $model = new departModel();

            $id = request()->param('id');

            $model->where('id',$id)->delete();

            Jump::win('删除成功！', session('back_url'));

        } catch (\Exception $e) {

            Jump::fail("删除失败=>{$e->getMessage()}");

        }

    }
}
