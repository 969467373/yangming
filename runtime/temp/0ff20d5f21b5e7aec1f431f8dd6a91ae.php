<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:76:"/www/wwwroot/yangming/public/../application/console/view/auth_group/add.html";i:1516944370;s:66:"/www/wwwroot/yangming/application/console/view/public_sidebar.html";i:1517903988;s:65:"/www/wwwroot/yangming/application/console/view/public_header.html";i:1516943998;}*/ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="/static/console/images/favicon.png" type="image/png">

        <title>添加<?php echo $title; ?></title>

        <link rel="stylesheet" href="/static/console/css/style.default.css" />
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
                    <h2><i class="fa fa-home"></i>管理<?php echo $title; ?><span>欢迎光临平台...</span></h2>
                    <div class="breadcrumb-wrapper">
                        <span class="label">您现在的位置:</span>
                        <ol class="breadcrumb">
                            <li>平台首页</li>
                            <li><?php echo $title; ?>管理</li>
                            <li class="active">管理<?php echo $title; ?></li>
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
                            <h4 class="panel-title">管理<?php echo $title; ?></h4>
                        </div>

                        <div class="panel-body panel-body-nopadding">
                            <form action="" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data"
                                  id="form">
                                <div class="form-group">
                                    <div class="panel-body">
                                        <div class="col-sm-3">
                                            <a class="btn btn-success">权限组名称</a>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" name="name"
                                                   <?php if(isset($name)): ?>value="<?php echo $name; ?>"<?php endif; ?> datatype="*"
                                                   nullmsg="请输入权限组名称！" placeholder="权限组名称！"/>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group" id="accordion">
                                    <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$level_one_item): $mod = ($i % 2 );++$i;?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" href="#<?php echo $level_one_item['level_one']; ?>">
                                                    <button class="btn btn-success"><?php echo $level_one_item['level_one']; ?></button>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="<?php echo $level_one_item['level_one']; ?>" class="panel-collapse collapse in">
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <?php if(is_array($level_one_item['level_two']) || $level_one_item['level_two'] instanceof \think\Collection || $level_one_item['level_two'] instanceof \think\Paginator): $i = 0; $__LIST__ = $level_one_item['level_two'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$level_two_item): $mod = ($i % 2 );++$i;?>
                                                    <tr>
                                                        <?php if(is_array($level_two_item) || $level_two_item instanceof \think\Collection || $level_two_item instanceof \think\Paginator): $i = 0; $__LIST__ = $level_two_item;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                                        <td>
                                                            <label <?php if(($item['type']==1)): ?>class="btn btn-warning"<?php endif; ?>><input name="console_ids[]"
                                                                                                                          <?php if((in_array($item['id'],$console_ids))): ?>checked<?php endif; ?>
                                                            class="<?php if(($item['type']==2)): ?>slave-input<?php else: ?>master-input<?php endif; ?>"
                                                            type="checkbox" value="<?php echo $item['id']; ?>"><?php echo $item['title']; ?></label>
                                                        </td>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                    </tr>
                                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; endif; else: echo "" ;endif; if(isset($id)): ?>
                                    <input type="hidden" value="<?php echo $id; ?>" name="id" />
                                    <?php endif; ?>
                                    <div class="panel panel-default">
                                        <div style="float:right;">
                                            <input type="submit" value="提交" class="btn btn-primary">
                                            <input type="button" value="返回" class="btn btn-default" onclick="window.history.go(-1)">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
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
                $("#form").Validform();  //就这一行代码！;

                $('.slave-input').click(function () {
                    $(this).parents('tr').find(".master-input").prop('checked', true)
                })
            })
        </script>
    </body>
</html>
