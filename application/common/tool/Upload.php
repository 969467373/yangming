<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2017/6/22
 * Time: 13:38
 */

namespace app\common\tool;
use think\Image;


/**
 * 上传工具
 */
class Upload
{
    static function upload($file_name, $save_directory, $type = 'image')
    {
        $files = request()->file($file_name);
        $files = is_array($files) ? $files : [$files];
        //图片存放的路径
        $path = DS . 'uploads' . DS . $type . DS . $save_directory;

        $flag = true;
        $date = date('Ymd');
        $save_files = [];

        foreach($files as $file){
            try {
                $info = $file->move(ROOT_PATH . 'public' . DS . $path);
                $flag = $flag&&$info;
                $save_files[] = $path . DS . $date . DS . $info->getFilename();
            } catch (\Exception $e) {
                $flag = false;
            }
        }

        $data = [
            'flag'=>$flag,
            'files'=>$save_files,
        ];

        return $data;
    }


    /**
     * 剪裁图片的上传
     * @param $file_name 文件的名字
     * @param $temp_crop_data 剪裁的数据 x y width height
     * @param $save_directory 保存的文件夹名字
     * @param string $type 类型 默认 image
     * @return array
     */
    static function cropUpload($file_name, $temp_crop_data,$save_directory ,$type = 'image')
    {
        $file = request()->file($file_name);
        //剪辑图片
        $crop_data = json_decode($temp_crop_data,true);
        $flag = true;
        try{
            $image = Image::open($file);
            $date = date('Ymd');
            //文件保存命名规则 年月日时分秒+3位毫秒
            $save_file_name = date('YmdHis').round(microtime(),2)*1000 .'.jpg';

            //文件存放的路径
            $path = '../public/uploads/'.$type.'/'.$save_directory.'/'.$date;
            //存数据库的路径
            $sql_path = DS . 'uploads' . DS . $type . DS . $save_directory. DS . $date . DS . $save_file_name;
            if (!is_dir($path)) {
                mkdir($path,0777,true);
            }
            $image->crop($crop_data['width'],$crop_data['height'],$crop_data['x'], $crop_data['y']);
            $save_files = $path.'/'. $save_file_name;
            $image->save($save_files);
        }catch (\Exception $e){
            $flag =  false;
        }

        $data = [
            'flag' => $flag,
            'file'=>$sql_path,
        ];
        return $data;
    }

    //删除文件
    static function deleteFile($file_names)
    {
        $file_names = is_array($file_names) ? $file_names : [$file_names];
        $flag = true;
        foreach($file_names as $filename){
            $flag = $flag&&@unlink(ROOT_PATH . 'public' . $filename);
        }
        return $flag;
    }

    static function uploadThrowException($file_name, $save_directory, $type = 'image')
    {
        $files = request()->file($file_name);
        $files = is_array($files) ? $files : [$files];
        //图片存放的路径
        $path = DS . 'uploads' . DS . $type . DS . $save_directory;

        $date = date('Ymd');
        $data = [];
        foreach($files as $file){
            $info = $file->move(ROOT_PATH . 'public' . DS . $path);
            $data[] = $path . DS . $date . DS . $info->getFilename();
        }

        return $data;
    }


    //删除文件
    static function deleteFileThrowException($file_names)
    {
        $file_names = is_array($file_names) ? $file_names : [$file_names];
        $flag = true;
        foreach($file_names as $filename){
            $flag = $flag&&unlink(ROOT_PATH . 'public' . $filename);
        }
        return $flag;
    }

    //剪裁图片
    static function cropImage($image_name,$temp_crop_data){
        $crop_data = json_decode($temp_crop_data);
        $image = Image::open($image_name);
        // 返回图片的宽度
        $width = $image->width();
        // 返回图片的高度
        $height = $image->height();
        $image->crop($width, $height,$crop_data['x'], $crop_data['y'], $crop_data['width'],  $crop_data['height']);
    }
}