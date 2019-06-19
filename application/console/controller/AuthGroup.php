<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/7 0007
 * Time: 14:03
 */

namespace app\console\controller;

use app\common\controller\ConsoleBase;
use app\common\model\Adminer as AdminerModel;
use app\common\model\Console as ConsoleModel;
use app\common\model\ConsoleAuthGroup;
use app\common\tool\Jump as Jump;
use think\Controller;

class AuthGroup extends ConsoleBase
{

    public function index()
    {
        $auth_group_model = new ConsoleAuthGroup();
        $keyword = request()->param('keyword');
        $where = [];
        if ($keyword != '') {
            $where['name'] = ['like', '%' . $keyword . '%'];
        }
        $list = $auth_group_model->consoleGetListByWhere($where);
        $view = [
            'title' => "权限组",
            'list' => $list,
            'keyword' => $keyword
        ];
        return view('index', $view);
    }

    /**
     * 增加管理员
     */
    public function add()
    {
        if (request()->isPost()) {
            try {
                $auth_group_model = new ConsoleAuthGroup();
                //数据处理
                $data = request()->post();
                //halt($data);
                $auth_group_model->add($data);
                Jump::win('添加成功！', session('back_url'));
            } catch (\Exception $e) {
                Jump::fail("失败=>{$e->getMessage()}");
            }
        }
        session('back_url', $_SERVER['HTTP_REFERER']);
        $console_model = new ConsoleModel();
        $data = $console_model->getConsoleData();
        $console_ids = [];
        $view = [
            'title' => '权限组',
            'data' => $data,
            'console_ids' => $console_ids,
        ];
        return view('add', $view);
    }

    /**
     * 编辑管理员
     * @return array|mixed|\think\response\View
     */
    public function edit()
    {
        $auth_group_model = new ConsoleAuthGroup();
        if (request()->isPost()) {
            try {
                //数据处理
                $data = request()->post();
                $auth_group_model->edit($data);
                Jump::win('添加成功！', session('back_url'));
            } catch (\Exception $e) {
                Jump::fail("删除失败=>{$e->getMessage()}");
            }
        }
        //缓存列表入口地址
        session('back_url', $_SERVER['HTTP_REFERER']);
        $console_auth_group_id = request()->param('console_auth_group_id');
        $console_model = new ConsoleModel();
        $data = $console_model->getConsoleData();
        $auth_group = $auth_group_model->find($console_auth_group_id);
        $view = [
            'title' => '权限组',
            'data' => $data,
            'id' => $console_auth_group_id,
            'console_ids' => $auth_group->console_ids,
            'name' => $auth_group->name,
        ];
        return view('add', $view);
    }

    /**
     * 删除
     */
    public function delete()
    {
        try {
            //缓存列表入口地址
            session('back_url', $_SERVER['HTTP_REFERER']);
            $console_auth_group_id = request()->param('console_auth_group_id');
            $auth_group_model = new ConsoleAuthGroup();
            $auth_group_model->deleteById($console_auth_group_id);
            Jump::win('删除成功！', session('back_url'));
        } catch (\Exception $e) {
            Jump::fail("删除失败=>{$e->getMessage()}");
        }
    }
}
