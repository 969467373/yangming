<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"/www/wwwroot/yangming/public/../application/console/view/dashboard/serverinfo.html";i:1519980846;s:66:"/www/wwwroot/yangming/application/console/view/public_sidebar.html";i:1517903988;s:65:"/www/wwwroot/yangming/application/console/view/public_header.html";i:1516943998;}*/ ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="/static/console/images/favicon.png" type="image/png">

    <title><?php echo $title; ?></title>
    <!-- Bootstrap Core CSS -->
    <link href="/static/console/css/style.default.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/static/console/js/html5shiv.js"></script>
    <script src="/static/console/js/respond.min.js"></script>
    <![endif]-->
  </head>

<body>
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
	
      <div class="pageheader">
        <h2><i class="fa fa-home"></i><?php echo $title; ?><span>欢迎光临平台...</span></h2>
        <div class="breadcrumb-wrapper">
          <span class="label">您现在的位置:</span>
          <ol class="breadcrumb">
            <li>平台首页</li>
            <li class="active"><?php echo $title; ?></li>
          </ol>
        </div>
      </div>

        <div class="row">
          <div class="col-sm-12">
            <div class="table-responsive">
              <table  class="table table-bordered mb30">
                <thead>
                  <tr><th colspan="2" scope="col">服务器信息<img onclick="show();" style="float:right;margin-top:5px;" src="/static/console/images/xl.png"/></th></tr>
                </thead>
                <tbody id="server">
                <?php if(is_array($info) || $info instanceof \think\Collection || $info instanceof \think\Paginator): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                  <tr>
                    <td width="600px;"><?php echo $key; ?></td>
                    <td width="1200px"><?php echo $vo; ?></td>
                  </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
              </table>
            </div><!-- table-responsive -->
          </div><!-- col-sm-7 -->
        </div><!-- row -->


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
  <script>
    function show()
    {
      var a = $("#server").css("display");
      if(a === "none")
      {
          $("#server").css("display","block");
      }else{
          $("#server").css("display","none");
      }
    }
  </script>
  <!--<script src="/static/console/js/flot/jquery.flot.min.js"></script>-->
  <!--<script src="/static/console/js/flot/jquery.flot.resize.min.js"></script>-->
  <!--<script src="/static/console/js/flot/jquery.flot.spline.min.js"></script>-->
  <!--<script src="/static/console/js/morris.min.js"></script>-->
  <!--<script src="/static/console/js/raphael-2.1.0.min.js"></script>-->

  <script src="/static/console/js/custom.js"></script>
  <!--<script src="/static/console/js/dashboard.js"></script>-->
</body>
</html>
