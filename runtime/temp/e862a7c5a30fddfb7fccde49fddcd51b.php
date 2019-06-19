<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:76:"/www/wwwroot/yangming/public/../application/console/view/adminer/modify.html";i:1516952422;s:66:"/www/wwwroot/yangming/application/console/view/public_sidebar.html";i:1517903988;s:65:"/www/wwwroot/yangming/application/console/view/public_header.html";i:1516943998;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="/static/console/images/favicon.png" type="image/png">

    <title>编辑<?php echo $title; ?></title>

    <link rel="stylesheet" href="/static/console/css/style.default.css"/>
    <link rel="stylesheet" href="/static/console/css/layui.css"/>
    <link rel="stylesheet" type="text/css" href="/static/common/css/validform.css?">
    <link rel="stylesheet" type="text/css" href="/static/Fileinput/css/fileinput.css">

</head>

<body>
<div id="preloader">
    <div id="status"><i class="fa fa-spinner fa-spin"></i></div>
</div>
<section>
    <div class="leftpanel">

    <div class="leftpanelinner"><!-- This is only visible to small devices -->
        <ul class="nav nav-pills nav-stacked nav-bracket">
            <?php if(is_array(\think\Session::get('left_menu')) || \think\Session::get('left_menu') instanceof \think\Collection || \think\Session::get('left_menu') instanceof \think\Paginator): $i = 0; $__LIST__ = \think\Session::get('left_menu');if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?>
            <li class="nav-parent <?php if(in_array(strtolower(request()->controller().'_'.request()->action()),$menu['level_one']['include'])): ?> nav-active active <?php endif; ?>">
                <a href=""><i class="glyphicon glyphicon-align-justify"></i><span><?php echo $menu['level_one']['title']; ?></span></a>
                <ul class="children" <?php if(in_array(strtolower(request()->controller().'_'.request()->action()),$menu['level_one']['include'])): ?> style="display:block;" <?php endif; ?>>
                <?php if(is_array($menu['level_two']) || $menu['level_two'] instanceof \think\Collection || $menu['level_two'] instanceof \think\Paginator): $i = 0; $__LIST__ = $menu['level_two'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$slave): $mod = ($i % 2 );++$i;?>
                    <li <?php if(in_array(strtolower(request()->controller().'_'.request()->action()),$slave['include'])): ?> class="active" <?php endif; ?>>
                        <a href="<?php echo $slave['url']; ?>"><i class="fa fa-caret-right"></i><?php echo $slave['title']; ?></a>
                    </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div><!-- leftpanel -->
    <div class="mainpanel">
        <script src="/static/console/js/jquery-1.11.1.min.js"></script>
<script src="/static/console/layer/layer.js"></script>
<script>
    //注销操作
    function logout() {
        layer.confirm('即将退出后台', {
            title :'提示信息',
            btn: ['确定','取消'] //按钮
        }, function(){
            location.href="<?php echo url('Console/Login/logout'); ?>";
        });
    }
</script>
<div class="headerbar">
    <a class="menutoggle"><i class="fa fa-bars"></i></a>
    <form class="searchform" action="index.html" method="post"></form>

    <div class="header-right">
        <ul class="headermenu">
            <li>
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <img src="/static/console/images/photos/loggeduser.png" alt=""/>
                        <?php echo \think\Session::get('admin_user_name'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                        <li><a href="<?php echo url('Console/AdminUser/modify'); ?>"><i class="glyphicon glyphicon-log-out"></i>修改个人信息</a></li>
                        <li><a href="#" onclick="logout()"><i class="glyphicon glyphicon-log-out"></i>注销登录</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div><!-- header-right -->
</div>
<!-- headerbar -->
        <div class="pageheader">
            <h2><i class="fa fa-home"></i>编辑<?php echo $title; ?><span>欢迎光临平台...</span></h2>
            <div class="breadcrumb-wrapper">
                <span class="label">您现在的位置:</span>
                <ol class="breadcrumb">
                    <li>平台首页</li>
                    <li><?php echo $title; ?>管理</li>
                    <li class="active">编辑<?php echo $title; ?></li>
                </ol>
            </div>
        </div>

        <div class="contentpanel">
            <div class="panel panel-default">

                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-close">&times;</a>
                        <a href="" class="minimize">&minus;</a>
                    </div>
                    <h4 class="panel-title">编辑<?php echo $title; ?></h4>
                </div>

                <div class="panel-body panel-body-nopadding">
                    <form action="" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data"
                          id="myupload">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">用户名</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="login" <?php if(isset($data['login'])): ?>value="<?php echo $data['login']; ?>"<?php endif; ?> datatype="*"
                                       nullmsg="请输入用户名！" placeholder="请输入用户名！"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">姓名</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="name" <?php if(isset($data['name'])): ?>value="<?php echo $data['name']; ?>"<?php endif; ?> datatype="*"
                                       nullmsg="请输入昵称！" placeholder="请输入昵称！"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">密码</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="pass" ignore="ignore" type='password' value="" datatype="*6-15" errormsg="密码范围在6~15位之间！" nullmsg="请输入密码!" placeholder="请输入密码！（注:不输入则不修改密码）"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">确认密码</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="password_confirm" ignore="ignore" type='password' value="" datatype="*6-15" recheck="pass" errormsg="您两次输入的账号密码不一致！" nullmsg="请再次输入密码！" placeholder="请输入密码！（注:不输入则不修改密码）"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">后台权限组</label>
                            <div class="col-sm-6">
                                <select class="form-control"  name='auth_group_id' style="width:200px;height:40px;" datatype="*"
                                        nullmsg="权限组选择" errormsg="权限组选择">
                                    <?php if(is_array($auth_groups) || $auth_groups instanceof \think\Collection || $auth_groups instanceof \think\Paginator): $i = 0; $__LIST__ = $auth_groups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo $group['id']; ?>" <?php if((!empty($data['auth_group_id']) && $data['auth_group_id']==$group['id'])): ?>checked<?php endif; ?> ><?php echo $group['name']; ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php if(isset($data['id'])): ?>
                        <input name='id' value="<?php echo $data['id']; ?>" type="hidden">
                        <?php endif; ?>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="submit" value="提交" class="btn btn-primary">
                                    <input type="button" value="返回" class="btn btn-default" onclick="window.history.go(-1)">
                                </div>
                            </div>
                        </div><!-- panel-footer -->

                    </form>
                </div><!-- panel-body -->
            </div><!-- panel -->
        </div><!-- contentpanel -->
    </div><!-- mainpanel -->
</section>
<script src="/static/console/js/jquery-1.11.1.min.js"></script>
<script src="/static/console/js/jquery-migrate-1.2.1.min.js"></script>
<script src="/static/console/js/jquery-ui-1.10.3.min.js"></script>
<script src="/static/console/js/bootstrap.min.js"></script>
<script src="/static/console/js/modernizr.min.js"></script>
<script src="/static/console/js/jquery.sparkline.min.js"></script>
<script src="/static/console/js/toggles.min.js"></script>
<script src="/static/console/js/retina.min.js"></script>
<script src="/static/console/js/jquery.cookies.js"></script>


<script src="/static/console/js/jquery.autogrow-textarea.js"></script>
<script src="/static/console/js/bootstrap-timepicker.min.js"></script>
<script src="/static/console/js/jquery.maskedinput.min.js"></script>
<script src="/static/console/js/jquery.tagsinput.min.js"></script>
<script src="/static/console/js/jquery.mousewheel.js"></script>
<script src="/static/console/js/custom.js"></script>
<!-- 时间 -->
<script src='/static/common/datepicker/WdatePicker.js' type='text/javascript'></script>
<!-- 文件上传 -->
<script type="text/javascript" src="/static/common/js/jquery.form.js"></script>
<!-- JQ验证 -->
<script src="/static/common/js/Validform_v5.3.2_min.js"></script>
<!-- 编辑器 -->
<script type="text/javascript" charset="utf-8" src="/static/Editor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/Editor/ueditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/Editor/lang/zh-cn/zh-cn.js"></script>
<!-- 图片上传 -->
<script src="/static/Fileinput/js/fileinput.js"></script>
<script src="/static/Fileinput/js/plugins/canvas-to-blob.js"></script>
<script src="/static/Fileinput/js/plugins/purify.js"></script>
<script src="/static/Fileinput/js/plugins/sortable.js"></script>
<script type="text/javascript">
    $(function () {
        $("#myupload").Validform();  //就这一行代码！;
    });
</script>
</body>
</html>
