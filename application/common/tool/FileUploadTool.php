<?php

namespace app\common\tool;
require_once EXTEND_PATH . 'UploadFile.class.php';
require_once EXTEND_PATH . 'jcrop_image.class.php';

class FileUploadTool
{
    static function upload(){
        $maxSize = 1024 * 1024; //1M 设置附件上传大小
        $allowExts = array("gif", "jpg", "jpeg", "png"); // 设置附件上传类型
        $file_save = "upload/";


        $upload = new \UploadFile(); // 实例化上传类

        $upload->maxSize = $maxSize;
        $upload->allowExts = $allowExts;
        $upload->savePath = $file_save; // 设置附件
        $upload->saveRule = time() . sprintf('%04s', mt_rand(0, 1000));
        if (!$upload->upload()) {// 上传错误提示错误信息
            $errormsg = $upload->getErrorMsg();
            $arr = array(
                'code'=>400,
                'error' => $errormsg, //返回错误
            );
            return json_encode($arr);

        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            $imgurl = $info[0]['savename'];

            $file_save = "http://".$_SERVER['HTTP_HOST']."upload/";
            $pic_name = ['code'=>200,'pic_name'=>$file_save . $imgurl];

            return json_encode($pic_name);
        }
    }
}