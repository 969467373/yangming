<?php
namespace app\home\tool;

class Jump
{
    static function win($msg, $url, $time = 2333, $note = "页面跳转中")
    {
        header('Content-Type:text/html;charset=utf-8');
        // $url = site_url($url);
        echo "<link rel='stylesheet'href='http://devlib.qiniudn.com/sweetalert/sweet-alert.css'/><script src='http://devlib.qiniudn.com/sweetalert/sweet-alert.min.js'></script><script>window.onload=myfun;countDown();function myfun(){swal('$msg','$note','success');delayURL(\"$url\",$time);document.querySelector('.confirm').onclick=function(){location.href='$url'}};function delayURL(url,time){setTimeout(\"top.location.href='\" + url + \"'\",time)}</script>";
        exit();
    }

    static function back($msg, $url, $time = 2333, $note = "页面跳转中")
    {
        header('Content-Type:text/html;charset=utf-8');
        // $url = site_url($url);
        echo "<link rel='stylesheet'href='http://devlib.qiniudn.com/sweetalert/sweet-alert.css'/>
<script src='http://devlib.qiniudn.com/sweetalert/sweet-alert.min.js'></script>
<script>window.onload=myfun;countDown();function myfun(){swal('$msg','$note','success');
document.querySelector('.confirm').onclick=function(){location.href='$url'}};
function delayURL(url,time){setTimeout(\"top.location.href='\" + url + \"'\",time)}</script>";
        exit();
    }


    static function fail($msg, $time = 2333, $note = "页面跳转中")
    {
        header('Content-Type:text/html;charset=utf-8');
        echo "<link rel='stylesheet'href='http://devlib.qiniudn.com/sweetalert/sweet-alert.css'/><script src='http://devlib.qiniudn.com/sweetalert/sweet-alert.min.js'></script><script>window.onload=myfun;function myfun(){swal('$msg','$note','error');delayURL(3000);document.querySelector('.confirm').onclick=function(){window.history.back()}};function delayURL(time){setTimeout(\"window.history.back();\",$time)};</script>";
        exit();
    }

    static function success($msg,$back=-1, $time = 2333, $note = "页面跳转中")
    {
        header('Content-Type:text/html;charset=utf-8');
        echo "<link rel='stylesheet'href='http://devlib.qiniudn.com/sweetalert/sweet-alert.css'/><script src='http://devlib.qiniudn.com/sweetalert/sweet-alert.min.js'></script><script>window.onload=myfun;function myfun(){swal('$msg','$note','success');delayURL(3000);document.querySelector('.confirm').onclick=function(){history.go($back)}};function delayURL(time){setTimeout(\"window.history.go($back);\",$time)};</script>";
        exit();
    }
}

