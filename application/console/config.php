<?php

// 配置文件
return [
//    'template' => [
//        'view_depr' => '_'
//    ],
    'view_replace_str' => [
        '__Layer__' => '/static/layer', //总后台
        '__STATIC__' => '/static', //总后台
        '__ASSETS__' => '/static/console', //总后台
        '__PUBLIC__' => '/static/common', //全局
        '__FILE__' => '/static/Fileinput', //图片上传
        '__EDITOR__' => '/static/Editor',//编辑器

    ]


];