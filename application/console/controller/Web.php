<?php

namespace app\console\controller;

use app\common\controller\ConsoleBase;
use app\common\tool\Jump as Jump;
use app\common\model\Web as webModel;

use think\Controller;
use think\Request;


class Web extends ConsoleBase
{


    private $title = '页面';


    // @# 页面管理-管理页面-管理页面-index
    public function index()
    {
        $request = Request::instance();

        if ($request->isPost()) {
            try {
                $post_data = $request->post();

                $model = new webModel();
                $model->edit($post_data);

                Jump::win('成功！', 'index');
            } catch (\Exception $e) {

                Jump::fail("失败=>{$e->getMessage()}");
            }

        }

        $model = new webModel();

        $data = $model->getWeb(1);

        //halt($data);
        return $this->fetch('web',[

            'title'=> $this->title,
            'data'=> $data

        ]);

    }




}
