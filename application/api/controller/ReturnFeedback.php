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
use app\common\model\ReturnFeedback as ReturnFeedbackModel;
use mrmiao\encryption\RSACrypt;


//返工反馈
class ReturnFeedback extends ApiBase
{

    //添加返工反馈
    public function addFeedback(RSACrypt $crypt,ReturnFeedbackModel $feedback)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ReturnFeedback.add');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);


            //处理图片
            $image = request()->file();

            if (!empty($image)) {
                $result = [];
                foreach ($image as $k=>$file) {
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'feedback');
                    if ($info) {
                        $result[] ='http://'.$_SERVER['HTTP_HOST']. '/uploads/feedback/' . date('Ymd') . '/' . $info->getFilename();
                    }
                }
                $post_data['img'] = serialize($result);
            } else {
                $post_data['img'] = '';
            }

            $data = $feedback->add($post_data);


            return $crypt->response(['code' => 200, 'message' => '成功'], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }




    //查看返工反馈详情
    public function getFeedbackDetail(RSACrypt $crypt,ReturnFeedbackModel $feedback)
    {
        try {
            $post_data = $crypt->request();

            //验证参数
            $result = $this->validate($post_data, 'ReturnFeedback.detail');
            if (true !== $result)
                return $crypt->response(['code' => 400, 'message' => $result],true);

            //获取反馈详情
            $data = $feedback->getDetail($post_data['feedback_id']);


            return $crypt->response(['code' => 200, 'message' => '成功','data'=>$data], true);

        } catch (\Exception $e) {
            return ['code' => 400, 'message' => $e->getMessage()];
        }

    }





}

