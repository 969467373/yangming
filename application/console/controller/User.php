<?php

namespace app\console\controller;

use app\common\controller\ConsoleBase;
use app\common\tool\Jump as Jump;
use app\common\model\User as userModel;
use app\common\model\Department;

use think\Controller;
use think\Loader;
use think\Request;


class User extends ConsoleBase
{

    private $group = 'User/index';

    private $title = '用户';


    // @# 用户管理-管理用户-管理用户-index
    public function index(Department $department)
    {
        $model = new userModel;

        //搜索关键字
        $search = Request::instance()->param();

        //查询所有部门
        $where=[];

        if (!empty($search['keyword'])) {

            $where['u.name|u.username'] = ['like', '%' . $search['keyword']. '%'];

        }

        if (!empty($search['department_id'])) {

            $where['u.department_id'] = $search['department_id'];

        }

        $list = $model->getConsoleList($where);
        //halt($list);
        $where2=[];
        //查询所有部门
        $depart = $department->getConsoleList($where2);

        //halt($depart);

        return $this->fetch('',[

            'title'=> $this->title,
            'list'=> $list,
            'depart'=> $depart,
            'search'=> $search,
        ]);
    }

    // @# 用户管理-管理用户-添加用户-add
    public function add()
    {
        $request = Request::instance();
        if ($request->isPost()) {

            try {

                $post_data = $request->post();

                $validate = Loader::validate('User');
                if(!$validate->scene('add')->check($post_data)){
                    Jump::fail($validate->getError());
                }

                $model = new userModel();
                $model->add($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {
                Jump::fail("失败=>{$e->getMessage()}");
            }
        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $departModel = new Department();
        //获取部门列表
        $where=[];
        $depart = $departModel->getConsoleList($where);

        $view = [
            'title' => $this->title,
            'depart' => $depart,
        ];

        return view('modify', $view);
    }



    // @# 用户管理-管理用户-编辑用户-edit
    public function edit()
    {
        $request = Request::instance();

        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $validate = Loader::validate('User');
                if(!$validate->scene('edit')->check($post_data)){
                    Jump::fail($validate->getError());
                }

                $model = new userModel();
                $model->edit($post_data);

                Jump::win('成功！', session('back_url'));
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }

        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $id = $request->param('id');
        $data = userModel::get($id);

        $departModel = new Department();
        //获取部门列表
        $where=[];
        $depart = $departModel->getConsoleList($where);

        $view = [
            'title' => $this->title,
            'data' => $data,
            'depart' => $depart,
        ];

        return view('modify',$view);
    }

    // @# 用户管理-管理用户-删除用户-delete
    public function delate()
    {
        try {
            //缓存列表入口地址

            session('back_url', $_SERVER['HTTP_REFERER']);

            $model = new userModel();

            $id = request()->param('id');

            $model->where('id',$id)->delete();

            Jump::win('删除成功！', session('back_url'));

        } catch (\Exception $e) {

            Jump::fail("删除失败=>{$e->getMessage()}");

        }

    }
}
