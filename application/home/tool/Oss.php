<?php
namespace app\common\tool;

use OSS\Core\OssException;
use OSS\OssClient;

class Oss
{
    protected static function init()
    {
        $accessKeyId = config('oss_config.accessKeyId');
        $accessKeySecret = config('oss_config.accessKeySecret');
        $endpoint = config('oss_config.endpoint');
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            throw $e;
        }
        return $ossClient;
    }

    public static function makeObject($file, $oss_dir, $oss_name)
    {
        $name_arr = explode(".", $file->getInfo('name'));
        $ext = array_pop($name_arr);
        $object = $oss_dir . DS . "$oss_name.$ext";
        return $object;
    }

    public static function upload($file, $object)
    {
        set_time_limit(0);
        try {
            $bucket = config('oss_config.bucket');
            $filePath = $file->getInfo('tmp_name');
            $ossClient = self::init();
            $ossClient->uploadFile($bucket, urlencode($object), $filePath);
            return $object;
        } catch (OssException $e) {
            throw new \Exception('Oss上传失败');
        }
    }

    public static function delete($object)
    {
        try {
            $bucket = config('oss_config.bucket');
            $ossClient = self::init();
            $ossClient->deleteObject($bucket, $object);
            return true;
        } catch (OssException $e) {
            throw new \Exception('Oss删除失败');
        }
    }

    //批量删除object
    public static function deleteObjects($objects)
    {
        try {
            $bucket = config('oss_config.bucket');
            $ossClient = self::init();
            $ossClient->deleteObjects($bucket, $objects);
            return true;
        } catch (OssException $e) {
            return false;
        }
    }
}