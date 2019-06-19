<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"/www/wwwroot/yangming/public/../application/console/view/task/detail.html";i:1533546991;s:66:"/www/wwwroot/yangming/application/console/view/public_sidebar.html";i:1517903988;s:65:"/www/wwwroot/yangming/application/console/view/public_header.html";i:1516943998;}*/ ?>
<!DOCTYPE html><html lang="en"><head>    <meta charset="utf-8">    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">    <meta name="description" content="">    <meta name="author" content="">    <link rel="shortcut icon" href="/static/console/images/favicon.png" type="image/png">    <title><?php echo $title; ?>管理</title>    <!-- 后台CSS -->    <link rel="stylesheet" href="/static/console/css/style.default.css"/>    <link rel="stylesheet" href="/static/console/css/jquery.datatables.css">    <script src="/static/console/datepicker/WdatePicker.js"></script>    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->    <!--[if lt IE 9]>    <script src="/static/console/js/html5shiv.js"></script>    <script src="/static/console/js/respond.min.js"></script>    <![endif]--></head><body><div id="preloader">    <div id="status"><i class="fa fa-spinner fa-spin"></i></div></div><section>    <div class="leftpanel">

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
</div><!-- leftpanel -->    <div class="mainpanel">        <script src="/static/console/js/jquery-1.11.1.min.js"></script>
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
<!-- headerbar -->        <div class="pageheader">            <h2><i class="fa fa-user"></i><?php echo $title; ?>列表<span>欢迎光临平台...</span></h2>            <div class="breadcrumb-wrapper">                <span class="label">您现在的位置:</span>                <ol class="breadcrumb">                    <li>平台首页</li>                    <li><?php echo $title; ?>管理</li>                    <li class="active"><?php echo $title; ?>列表</li>                </ol>            </div>        </div>        <div class="contentpanel">            <div class="panel panel-default"><!-- panel-body start -->                <div class="panel-heading" style="border-bottom:none;">                    <div>                        <a class="btn btn-primary" href="<?php echo url('Console/Task/inexcel_process',['task_id'=>$task_id]); ?>"                           style="float:right;">导出Excel</a>                    </div>                    <h4 class="panel-title"><?php echo $title; ?>列表</h4>                </div>                <div class="table-responsive" style="margin-left:30px; width:96%; margin-top:20px; bottom:50px;">                    <form name="form" id="form" method="post" action="">                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">                            <thead>                            <tr>                                <th width="100">工序名称</th>                                <th width="100">进行状态</th>                                <th width="100">是否返工</th>                                <th width="100">是否超时</th>                                <th width="100">负责部门</th>                                <th width="100">负责人</th>                            </tr>                            </thead>                            <tbody>                            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>                            <tr class="modules" title="<?php echo $vo['id']; ?>">                                <td align="center"><?php echo $vo['title']; ?></td>                                <td>                                    <?php if(($vo['process_status'] == 0)): ?>                                    未领取                                    <?php elseif(($vo['process_status'] == 1)): ?>                                    进行中                                    <?php else: ?>                                    已完成                                    <?php endif; ?>                                </td>                                <td>                                    <?php if(($vo['return_status'] == 1)): ?>                                    <font color="red">返工</font>                                    <?php else: ?>                                    正常                                    <?php endif; ?>                                </td>                                <td>                                    <?php if(($vo['overtime_status'] == 1)): ?>                                    <font color="red">超时(<?php echo $vo['timeout']; ?>)</font>                                    <?php else: ?>                                    正常                                    <?php endif; ?>                                </td>                                <td><?php echo $vo['department']; ?></td>                                <td><?php echo $vo['duty_name']; ?></td>                            </tr>                            <?php endforeach; endif; else: echo "" ;endif; ?>                            </tbody>                        </table>                        <?php echo $list->render(); ?>                    </form>                </div><!-- panel-body end -->            </div><!-- panel -->        </div><!-- contentpanel -->    </div><!-- mainpanel --></section><script src="/static/console/js/jquery-1.11.1.min.js"></script><script src="/static/console/js/jquery-migrate-1.2.1.min.js"></script><script src="/static/console/js/bootstrap.min.js"></script><script src="/static/console/js/modernizr.min.js"></script><script src="/static/console/js/jquery.sparkline.min.js"></script><script src="/static/console/js/toggles.min.js"></script><script src="/static/console/js/retina.min.js"></script><script src="/static/console/js/jquery.cookies.js"></script><script src="/static/console/js/jquery.datatables.min.js"></script><script src="/static/console/js/select2.min.js"></script><script src="/static/console/js/custom.js"></script><script src="/static/console/js/jquery-ui.js"></script><script src="/static/console/layer/layer.js"></script><!--全局JS--><script src="/static/common/js/public.js"></script><script type="text/javascript">    //删除操作    function doDel(id) {        layer.confirm('请谨慎操作,删除不可恢复', {            title :'提示信息',            btn: ['确定','取消'] //按钮        }, function(){            location.href="<?php echo url('Console/User/delate'); ?>"+"?id="+id;        });    }</script></body></html>