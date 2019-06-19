<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"/www/wwwroot/yangming/public/../application/console/view/task/detail.html";i:1533546991;s:66:"/www/wwwroot/yangming/application/console/view/public_sidebar.html";i:1517903988;s:65:"/www/wwwroot/yangming/application/console/view/public_header.html";i:1516943998;}*/ ?>
<!DOCTYPE html>

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