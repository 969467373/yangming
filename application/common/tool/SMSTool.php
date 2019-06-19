<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2017/6/22
 * Time: 13:38
 */

namespace app\common\tool;

require_once EXTEND_PATH . 'api_sdk/vendor/autoload.php';

use think\Cache;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
use think\Exception;

// 加载区域结点配置
Config::load();

class SMSTool
{

    /**
     * 构造器
     *
     * @param string $accessKeyId 必填，AccessKeyId
     * @param string $accessKeySecret 必填，AccessKeySecret
     */
    public function __construct()
    {
        //此处需要替换成自己的AK信息
        $accessKeyId = config('sms_config.appkey');
        $accessKeySecret = config('sms_config.secretKey');

        // 短信API产品名
        $product = "Dysmsapi";

        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }


    /**
     * 发送短信范例
     *
     * @param string $signName <p>
     * 必填, 短信签名，应严格"签名名称"填写，参考：<a href="https://dysms.console.aliyun.com/dysms.htm#/sign">短信签名页</a>
     * </p>
     * @param string $templateCode <p>
     * 必填, 短信模板Code，应严格按"模板CODE"填写, 参考：<a href="https://dysms.console.aliyun.com/dysms.htm#/template">短信模板页</a>
     * (e.g. SMS_0001)
     * </p>
     * @param string $phoneNumbers 必填, 短信接收号码 (e.g. 12345678901)
     * @param array|null $templateParam <p>
     * 选填, 假如模板中存在变量需要替换则为必填项 (e.g. Array("code"=>"12345", "product"=>"阿里通信"))
     * </p>
     * @param string|null $outId [optional] 选填, 发送短信流水号 (e.g. 1234)
     * @return stdClass
     */
    public function sendSms($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null) {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($phoneNumbers);

        // 必填，设置签名名称
        $request->setSignName($signName);

        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);

        // 可选，设置模板参数
        if($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }

        // 可选，设置流水号
        if($outId) {
            $request->setOutId($outId);
        }

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);

        // 打印请求结果
//         var_dump($acsResponse);

        return $acsResponse;

    }


    public function sendCodeSMS($mobile,$prefix,$area_code=86){
        $rand_code = rand(1000, 9999);
        $key = $prefix ."_". $mobile;
        $interval_key = 'interval_'.$prefix ."_". $mobile;
//        dump($rand_code);
//        $res=true;
        //阿里短信发送
        $sign_name = config('sms_config.sign_name');
        $sms_code = config('sms_config.sms_code')[$prefix];
        $param = ['code'=>"$rand_code"];
        $acsResponse = $this->sendSms($sign_name,$sms_code,$mobile,$param);
        $res = $acsResponse->Code=='OK' ? true : false;


        if ($res){
            Cache::set($key, (string)$rand_code,config('cache_time.sms_code'));
            Cache::set($interval_key, (string)$rand_code,config('cache_time.sms_code_interval'));
            return true;
        }
        throw new Exception('阿里云短信发送失败');
    }

}