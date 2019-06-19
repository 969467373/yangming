<?php

namespace app\common\tool;


class JsonTool
{

    //判断是否为json
    static function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}