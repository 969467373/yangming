<?php
/**
 * Created by PhpStorm.
 * User: by hq
 * Date: 2017/10/26 0026
 * Time: 16:36
 */
namespace app\common\tool;

use think\Db;

class Jpush{


    protected function request_post($url = "", $param = "", $header = "")
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
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

    /**
     * 极光推送 - 发送
     */
    // push_user('其他人共享线索给我', $v['user_id']);
    //用户端      Appkey c61099031b4f771d4fbb47c1  Master Secret   82d7060d676bc04337686df6
    public function push_user($title,$uid,$type=null,$content=1)
    {
        $url = 'https://api.jpush.cn/v3/push';
        $base64 = base64_encode("b572a3dbacb418599ca01db8:0515772f47ca9baf8d5e931c");
        $header = array(
            "Authorization:Basic $base64",
            "Content-Type:application/json"
        );
        $param = '{
                "platform": "all",
                "audience": {
                    "alias": [
                        "' . $uid . '"
                    ]
                },
                "notification": {
                    "android": {
                    	"alert" : "'.$title.'",
                        "extras" : {"type":"'.$type.'","content":"'.$content.'"}
                    },
                    "ios": {
                        "alert": "' . $title . '",
                        "sound": "default",
                        "content-available":true,
                        "extras" : {"type":"'.$type.'","content":"'.$content.'"}
                    }
                },
                "options": {
                    "time_to_live": 10,
                    "apns_production": false
                }
            }';
        $res = $this->request_post($url, $param, $header);
        $res_arr = json_decode($res, true);

    /*dump($res_arr);
    dump($param);*/
    }

    public function push_user_all($title,$content)
    {
        $url = 'https://api.jpush.cn/v3/push';
        $base64 = base64_encode("b572a3dbacb418599ca01db8:0515772f47ca9baf8d5e931c");
        $header = array(
            "Authorization:Basic $base64",
            "Content-Type:application/json"
        );
        $param = '{
				"platform" : "all",
   				"audience" : "all",
				"notification":{
					"alert":"' . $title . '",
					"android":{
						"extras": {
                            "content": "' . $content . '"
                        }
					},
					"ios":{
						"extras": {
                            "content": "' . $content . '"
                        }
					}
				},
				"options" : {
					"time_to_live" : 60,"apns_production":false
			 	}
			  }';
        $res = $this->request_post($url, $param, $header);
        $res_arr = json_decode($res, true);
//     dump($res_arr);
    }

}