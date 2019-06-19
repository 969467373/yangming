<?php
namespace app\home\controller;

use app\home\model\User;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use traits\controller\Jump;

class Login extends Controller
{

    //密码加密
    public function setPasswordAttr($value)
    {
        //密码哈希加密
        return password_hash($value, PASSWORD_DEFAULT);
    }

    //登录
    public function login(){
        try{
            if(Request::instance()->isPost()){

                $data = Request::instance()->param();
                
            }else{
                echo 123;
            }
        }catch (\Exception $e){

        }
    }
    
    public function text()
    {
        session('url', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
       
        $model = new User();
        $list =$model->text();
        dump($list);
        //dump(session('url'));die;
        //\app\home\tool\Jump::win('成功',session('url'));
        //return view('user/login');
        //Jump::success('成功','user/login');

    }



}
