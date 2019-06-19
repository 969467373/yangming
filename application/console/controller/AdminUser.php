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
use app\common\tool\Jump as Jump;
use app\console\controller\Console;
use think\Exception;
use think\Session;

class AdminUser extends ConsoleBase
{
    private $part = '管理后台用户';

    public function modify()
    {
        //读取当前后台用户id
        $id = Session::get('admin_user_id');

        if (request()->isPost()) {
            try {
                $adminer_model = AdminerModel::get($id);
                $post_data = request()->post();
                //验证参数
                $result = $this->validate($post_data, 'AdminUser.modify');
                if (true !== $result)
                    throw new Exception($result);

                //判断原密码是否正确
                if (!password_verify($post_data['old_pass'], $adminer_model->pass))
                    throw new Exception('原密码输入错误，请重试');

                $adminer_model->allowField('name,pass')->save($post_data);

                Jump::win('修改成功！', session('back_url'));
            } catch (\Exception $e) {
                Jump::fail("删除失败=>{$e->getMessage()}");
            }
        }
        session('back_url', $_SERVER['HTTP_REFERER']);

        /*$data = AdminerModel::get($id);

        halt($data);*/
        $view = [
            'part' => $this->part,
            'title' => '修改密码',
            'id' => $id,
            'data' => AdminerModel::get($id)
        ];
        return view('modify', $view);
    }
}
