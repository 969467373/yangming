<?php

namespace app\console\controller;



use app\common\model\Adminer;

use app\common\tool\Jump;



use think\Controller;

use think\Url;

use think\Request;

use think\Response;

use think\Session;



class Login extends Controller

{

    protected $beforeActionList = [

        //'checkLogin' => ['only' => 'index'],
        //'checkLogin',                     //执行任何方法之前都会执行这个checkLogin
    ];



    /**

     * 后台登录

     * username,用户账号

     * pass,登录密码

     * @return mixed

     */

    public function index()

    {

        if (request()->isPost()) {

            try {

                $login = request()->post('username');

                $pass = request()->post('pass');

                //halt($login);

                Adminer::login($login,$pass);

                Jump::win('登录成功', url('console/dashboard/index'));

            } catch (\Exception $e) {

                Jump::fail("登陆失败=>{$e->getMessage()}");

            }

        }


        return $this->fetch('index', [

            "meta_title" => '管理登录'

        ]);

    }



    /**

     * 检测用户是否登录

     */

    protected function checkLogin()

    {

        if (!Session::has('admin_user_id')) {

            //halt(Session::get('admin_user_id'));
            $this->redirect(url('console/dashboard/index'));

        }

    }



    /**

     * 注销登录

     */

    public function logout()

    {

        session(null);

        $this->redirect(url('console/login/index'));

    }


    public function test()
    {
        echo '这是个测试';
    }


    public function frist()
    {

        echo '成功了';

    }

}

