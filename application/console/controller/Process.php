<?php

namespace app\console\controller;

use app\common\controller\ConsoleBase;
use app\common\tool\Jump as Jump;
use app\common\model\Process as processModel;

use think\Controller;
use think\Request;


class Process extends ConsoleBase
{

    private $group = 'Process/index';

    private $title = '工序';


    // @# 工序管理-管理工序-管理工序-index
    public function index()
    {
        $model = new processModel;

        //搜索关键字
        $keyword = Request::instance()->post('keyword');

        //查询所有部门
        $where=[];

        if (!empty($keyword)) {

            $where['title'] = ['like', '%' . $keyword . '%'];

        }

        $list = $model->getConsoleList($where);

        //halt($list);

        return $this->fetch('',[

            'title'=> $this->title,
            'list'=> $list,
            'keyword'=> $keyword,
        ]);
    }

    // @# 工序管理-管理工序-添加工序-add
    public function add()
    {
        $request = Request::instance();
        if ($request->isPost()) {

            try {

                $post_data = $request->post();

                $model = new processModel();
                $model->addProcess($post_data);

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



    // @# 工序管理-管理工序-编辑工序-edit
    public function edit()
    {
        $request = Request::instance();

        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new processModel();
                $model->edit($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }

        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $id = $request->param('id');
        $data = processModel::get($id);

        $view = [
            'title' => $this->title,
            'data' => $data,
        ];

        return view('modify',$view);
    }

    // @# 工序管理-管理工序-删除工序-delete
    public function delate()
    {
        try {
            //缓存列表入口地址

            session('back_url', $_SERVER['HTTP_REFERER']);

            $model = new processModel();

            $id = request()->param('id');

            $model->where('id',$id)->delete();

            Jump::win('删除成功！', session('back_url'));

        } catch (\Exception $e) {

            Jump::fail("删除失败=>{$e->getMessage()}");

        }

    }
}
