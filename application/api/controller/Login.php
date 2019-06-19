<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\api\controller;



use app\common\controller\ApiBase;
use app\common\model\User;
use mrmiao\encryption\RSACrypt;
use think\Request;

use think\Session;




class Login extends ApiBase
{


    //用户登录
    function login(RSACrypt $crypt)
    {

        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'Login.login');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            $model = new User();
            $user = $model->login($post_data);


            if ($user){
                //使用缓存记录用户设备mac地址
                //cache("mac_{$user['id']}",$post_data['mac']);

                return $crypt->response([
                    'code' => 200,
                    'message' => '登录成功',
                    'data' => [
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'phone' => $user['phone'],
                        'name' => $user['name'],
                        'department_id' => $user['department_id'],
                    ]
                ],true);
            }
        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }




}

