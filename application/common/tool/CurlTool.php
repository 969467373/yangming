<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2017/8/31
 * Time: 10:51
 */

namespace app\common\tool;


class CurlTool
{
//
//    /**
//     * 使用魔术方法统一请求和返回入口,作为前置钩子hook
//     * @param $name,请求方法名
//     * @param $arguments,请求参数数组
//     * @return mixed
//     * @throws \Exception
//     */
//    public function __call($name, $arguments){
//        return call_user_func_array([__CLASS__,$name],$arguments);
//    }
//

    static function request_post_no_param($url = "", $param = "", $header = ['Accept: application/json']) {
        if (empty($url)) {
            return false;
        }
        $opt_header='';
        foreach ($header as $item){
            $opt_header .= $item."\r\n";
        }
        $opts = [
            'http'=>[
                'method'=>"POST",
                'header'=>$opt_header
            ]
        ];
        $context = stream_context_create($opts);
        $data = file_get_contents($url, false, $context);
        return $data;
    }

    /**
     * @param string $url
     * @param string $param
     * @param array $header
     * @return bool|mixed
     */
    static function request_post($url = "", $param = "", $header = ['Accept: application/json']) {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        //$curlPost = is_array($param)?json_encode($param,true):$param;
        $curlPost = $param;
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch); // 运行curl

        curl_close($ch);
        return $data;
    }

    static function request_put($url = "", $param = "", $header = ['Accept: application/json']) {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = is_array($param)?json_encode($param,true):$param;
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch); // 运行curl

        curl_close($ch);
        return $data;
    }

    /**
     * @param string $url
     * @param string $param
     * @param array $header
     * @return bool|mixed
     */
    static function request_delete($url = "", $param = "", $header = ['Accept: application/json']) {
        if (empty($url)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = is_array($param)?json_encode($param,true):$param;
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch); // 运行curl

        curl_close($ch);
        return $data;
    }

    /**
     * @param string $url
     * @param string $param
     * @param array $header
     * @return bool|mixed
     */
    static function request_get($url = "", $param = "", $header = ['Accept: application/json']) {
        if (empty($url)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = is_array($param)?json_encode($param,true):$param;
        $ch = curl_init(); // 初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); // 抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); // post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch); // 运行curl

        curl_close($ch);
        return $data;
    }
}