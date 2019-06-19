<?php

namespace app\console\behavior;


use app\common\model\Adminer;
use app\common\model\Console as ConsoleModel;
use app\common\tool\Jump;

class ConsoleAuth
{
    protected $common_auth = [
        'dashboard_index',
        'adminuser_modify',
    ];
    protected $common_left_menu = [
        'level_one'=>[
            'title'=>'主面板',
            'include'=>['dashboard_index'],
        ],
        'level_two'=>[
            [
                'title'=>'主面板',
                'url'=>"/console/dashboard/index",
                'include'=>['dashboard_index'],
            ],
        ],
    ];
    protected $super_auth = [
        'adminer_index',
        'adminer_add',
        'adminer_edit',
        'adminer_delete',
        'authgroup_index',
        'authgroup_add',
        'authgroup_edit',
        'authgroup_delete',
    ];
    protected $super_left_menu = [
        'level_one'=>[
            'title'=>'后台权限管理',
            'include'=>[
                'adminer_index',
                'adminer_add',
                'adminer_edit',
                'adminer_delete',
                'authgroup_index',
                'authgroup_add',
                'authgroup_edit',
                'authgroup_delete',
            ],
        ],
        'level_two'=>[
            [
                'title'=>'管理员管理',
                'url'=>"/console/adminer/index",
                'include'=>[
                    'adminer_index',
                    'adminer_add',
                    'adminer_edit',
                    'adminer_delete',
                ],
            ],
            [
                'title'=>'权限组管理',
                'url'=>"/console/auth_group/index",
                'include'=>[
                    'authgroup_index',
                    'authgroup_add',
                    'authgroup_edit',
                    'authgroup_delete',
                ],
            ],
        ],
    ];

    protected $pass = [
        'Login'=>'*',
    ];

    public function run(&$params)
    {
        //检测用户登录状态
        if (false === session("admin_user_id")) {
            redirect(url('console/login/index'));
        }

        if (!self::checkPass()) {
            list($user_auth,$left_menu) = $this->getUserConsoleData(session('admin_user_id'));
            if(session('admin_user_type')<3){
                $user_auth = array_merge($user_auth,$this->super_auth);
                array_unshift($left_menu,$this->super_left_menu);
            }
            $user_auth = array_merge($user_auth,$this->common_auth);
            array_unshift($left_menu,$this->common_left_menu);
            session('user_auth',$user_auth);
            session('left_menu',$left_menu);

            $current_auth = strtolower(request()->controller().'_'.request()->action());

            if (session('admin_user_type')>2 && !in_array($current_auth,$user_auth))
                Jump::fail('错误的访问方式,没有相应的权限');
        }
    }

    //检查app行为是否需要验证权限
    protected function checkPass(){
        $controller = request()->controller();
        $action = request()->action();
        if (explode('_',$action)[0]=='ajax')
            return true;

        if (isset($this->pass[$controller])){
            if ($this->pass[$controller] == '*'||in_array($action,$this->pass[$controller]) ){
                return true;
            }
        }
        return false;
    }

    protected function getUserConsoleData($user_id){
        $adminer_model = new Adminer();
        $console_model = new ConsoleModel();
        $where = [];
        //普通用户获取对应权限组的数据,超级用户获取全部数据
        if (session('admin_user_type')==3){
            $ids = $adminer_model->getAuthConsoleIds($user_id);
            //处理用户权限组被删除的特殊情况
            if (!$ids)
                return [[],[]];
            $where['id'] = ['in',$ids];
        }

        $user_auth_data = $console_model->where($where)->select();

        foreach ($user_auth_data as $value){
            $user_auth[] = strtolower($value['controller'].'_'.$value['action']);
            $console_data[$value['level_one']]['level_one']['title'] = $value['level_one'];
            $console_data[$value['level_one']]['level_one']['include'][] = strtolower($value['controller'].'_'.$value['action']);


            if ($value['level_two']==$value['function_name']){
                $console_data[$value['level_one']]['level_two'][$value['level_two']]['title'] = $value['level_two'];
                $console_data[$value['level_one']]['level_two'][$value['level_two']]['url'] = url("console/{$value['controller']}/{$value['action']}",'',false);
            }
            $console_data[$value['level_one']]['level_two'][$value['level_two']]['include'][] = strtolower($value['controller'].'_'.$value['action']);

        }
        return [$user_auth,$console_data];
    }
}