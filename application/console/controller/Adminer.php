<?php



/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2017/6/7 0007

 * Time: 14:03

 */



namespace app\console\controller;



use app\common\controller\BaseController;

use app\common\controller\ConsoleBase;

use app\common\model\ConsoleAuthGroup;

use think\Db;

use think\Request;

use think\Session;


use app\common\model\Adminer as AdminerModel;

use app\common\tool\Jump as Jump;

use think\Loader;

use think\Url;

use think\Log;




class Adminer extends ConsoleBase
{

    private $group = 'Adminer/index';

    private $title = '管理员';

    /**

     * @return array|mixed|\think\response\View

     */

    public function index()

    {

        $model = new AdminerModel();

        //搜索关键字

        $keyword = Request::instance()->post('keyword');

        //查询所有管理员

        $where=[];

        if (!empty($keyword)) {

            $where['login|name'] = ['like', '%' . $keyword . '%'];

        }

        //查询全部低级管理员数据,分页

        $list = $model->consoleGetListByWhere($where);



        $view = [

            'title' => $this->title,

            'list' => $list,

            'keyword' => $keyword,

        ];

        return view('index', $view);

    }



    /**

     * 增加管理员

     */

    public function add()

    {

        $request = Request::instance();

        //判断数据来源

        if ($request->isPost()) {

            try {

                $post_data = $request->post();

                $validate = Loader::validate('Adminer');
                if(!$validate->scene('add')->check($post_data)){
                    Jump::fail($validate->getError());
                }

                $model = new AdminerModel();

                $model->add($post_data);

                Jump::win('成功！', session('back_url'));

            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");

            }

        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $auth_groups = ConsoleAuthGroup::all();

        $view = [

            'title' => $this->title,

            'auth_groups' => $auth_groups

        ];

        return view('modify', $view);

    }



    /**

     * 编辑管理员

     * @return array|mixed|\think\response\View

     */

    public function edit()

    {

        $request = Request::instance();

        if ($request->isPost()) {

            try {

                $post_data = $request->post();

                $validate = Loader::validate('Adminer');
                if(!$validate->scene('edit')->check($post_data)){
                    Jump::fail($validate->getError());
                }


                $model = new AdminerModel();

                $model->edit($post_data);

                Jump::win('成功！', session('back_url'));

            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");

            }

        }

        session('back_url', $_SERVER['HTTP_REFERER']);

        $id = $request->param('id');

        $data = AdminerModel::get($id);

        $auth_groups = ConsoleAuthGroup::all();

        $view = [

            'title' => $this->title,

            'data' => $data,

            'auth_groups' => $auth_groups

        ];

        return view('modify',$view);

    }



    /**

     * 删除管理员，根据主键删除

     */

    public function delete()

    {

        try {

            //缓存列表入口地址

            session('back_url', $_SERVER['HTTP_REFERER']);

            $model = new AdminerModel();

            $id = request()->param('id');

            $model->where('id',$id)->delete();

            Jump::win('删除成功！', session('back_url'));

        } catch (\Exception $e) {

            Jump::fail("删除失败=>{$e->getMessage()}");

        }

    }

}

