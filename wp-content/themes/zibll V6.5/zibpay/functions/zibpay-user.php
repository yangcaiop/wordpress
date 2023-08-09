<?php
/*
 * @Author        : Qinver
 * @Url           : dkewl.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-04-28 21:25:40
 * @About         : 刀客源码
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**挂钩到用户中心 */
function zibpay_user_page_tabs_array($tabs_array)
{
    $user_id = get_current_user_id();
    $tabs    = array();

    //vip会员
    if (_pz('pay_user_vip_1_s') || _pz('pay_user_vip_2_s')) {
        $vip_icon = zibpay_get_vip_icon(zib_get_user_vip_level(), 'em12', 0);

        $tabs['vip'] = array(
            'title'    => zib_get_user_page_nav_title(zib_get_svg('vip_1', null, 'icon fa-fw mr10 em16') . '我的会员', $vip_icon),
            'nav_attr' => 'drawer-title="我的会员"',
            'loader'   => '<div class="zib-widget"><i class="placeholder s1"></i><p class="placeholder t1"></p>
            <p style="height: 110px;" class="placeholder k1"></p><p class="placeholder k2"></p><p style="height: 110px;" class="placeholder k1"></p><p class="placeholder t1"></p><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></div>',
        );
    }

    //余额或积分
    if (_pz('pay_balance_s') || _pz('points_s')) {
        $user_balance = _pz('pay_balance_s') ? zibpay_get_user_balance($user_id) : '';
        $b            = $user_balance ? zibpay_get_pay_mark() . $user_balance : '';
        if (!$b) {
            $user_points = zibpay_get_user_points($user_id);
            $b           = $user_points ? zibpay_get_points_mark() . _cut_count($user_points) : '';
        }

        $tabs['balance'] = array(
            'title'    => zib_get_user_page_nav_title(zib_get_svg('wallet-color', null, 'icon fa-fw mr10 em16') . '我的资产', $b),
            'nav_attr' => 'drawer-title="我的资产"',
            'loader'   => '<div class="row gutters-10 user-pay"><div class="col-sm-6"><div class="zib-widget"><div class="placeholder s1"></div><div class="em3x c-blue">--</div><i class="placeholder s1 mr10"></i><i class="placeholder s1"></i></div></div><div class="col-sm-6"><div class="zib-widget"><div class="placeholder s1"></div><div class="em3x c-blue">--</div><i class="placeholder s1 mr10"></i><i class="placeholder s1"></i></div></div></div><div class="box-body notop"><div class="title-theme"><b>订单明细</b></div></div>' . str_repeat('<div class="zib-widget"><p class="placeholder k1"></p><p class="placeholder t1"></p><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></div>', 3),
        );
    }

    //销售分成
    if (_pz('pay_income_s')) {
        $b          = '';
        $today_data = zibpay_get_user_today_income_data($user_id);
        if ($today_data['sum']) {
            $b = '今日收入' . floatval($today_data['sum']) . '元';
        } else {
            $all_data = zibpay_get_user_income_data($user_id);
            if ($all_data['sum']) {
                $b = '总收入' . floatval($all_data['sum']) . '元';
            }
        }

        $tabs['income'] = array(
            'title'    => zib_get_user_page_nav_title(zib_get_svg('merchant-color', null, 'icon fa-fw mr10 em16') . '创作分成', $b), //今日收入
            'nav_attr' => 'drawer-title="创作分成"',
            'loader'   => '<div class="row gutters-10 user-pay"><div class="col-sm-6"><div class="zib-widget"><div class="placeholder s1"></div><div class="em3x c-blue">--</div><i class="placeholder s1 mr10"></i><i class="placeholder s1"></i></div></div><div class="col-sm-6"><div class="zib-widget"><div class="placeholder s1"></div><div class="em3x c-blue">--</div><i class="placeholder s1 mr10"></i><i class="placeholder s1"></i></div></div></div><div class="box-body notop"><div class="title-theme"><b>订单明细</b></div></div>' . str_repeat('<div class="zib-widget"><p class="placeholder k1"></p><p class="placeholder t1"></p><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></div>', 3),
        );
    }

    //推广返利
    if (_pz('pay_rebate_s')) {
        $rebate_price_effective = zibpay_get_user_rebate_data($user_id, 'effective')['sum'];
        $b                      = $rebate_price_effective ? '' . $rebate_price_effective . '元待提现' : ''; //钱
        $tabs['rebate']         = array(
            'title'    => zib_get_user_page_nav_title(zib_get_svg('money-color', null, 'icon fa-fw mr10 em16') . '推广中心', $b),
            'nav_attr' => 'drawer-title="推广中心"',
            'loader'   => '<div class="row gutters-10"><div class="col-sm-6"><div class="zib-widget jb-red" style="height: 136px;"></div></div>
            <div class="col-sm-6"><div style="height: 136px;" class="zib-widget jb-blue"></div></div></div><div class="zib-widget"><div class="box-body"><p class="placeholder k1"></p><p class="placeholder k2"></p><p class="placeholder k1" style="height: 120px;"></p><p class="placeholder t1"></p>
            <p class="placeholder k1"></p><p class="placeholder t1"></p><p class="placeholder k1"></p>
            <p class="placeholder k1"></p>
            <p class="placeholder k1"></p>
            </div></div>',
        );
    }

    //订单明细
    if (_pz('pay_show_user')) {
        $count_all = zibpay_get_user_order_count($user_id);
        $b         = $count_all ? '共' . $count_all . '笔' : '';

        $tabs['order'] = array(
            'title'    => zib_get_user_page_nav_title(zib_get_svg('order-color', null, 'icon fa-fw mr10 em16') . '我的订单', $b),
            'nav_attr' => 'drawer-title="我的订单"',
            'loader'   => '<div class="row gutters-10 user-pay"><div class="col-sm-6"><div class="zib-widget"><div class="placeholder s1"></div><div class="em3x c-blue">--</div><i class="placeholder s1 mr10"></i><i class="placeholder s1"></i></div></div><div class="col-sm-6"><div class="zib-widget"><div class="placeholder s1"></div><div class="em3x c-blue">--</div><i class="placeholder s1 mr10"></i><i class="placeholder s1"></i></div></div></div><div class="box-body notop"><div class="title-theme"><b>订单明细</b></div></div>' . str_repeat('<div class="zib-widget"><p class="placeholder k1"></p><p class="placeholder t1"></p><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></div>', 3),
        );
    }
    if ($tabs) {
        return $tabs + $tabs_array;
    }
    return $tabs_array;
}
add_filter('user_ctnter_main_tabs_array', 'zibpay_user_page_tabs_array');

//用户中心订单tab
function zibpay_user_page_tab_content_order()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }

    $order_ias = array(
        'id'     => '',
        'class'  => 'user-pay-statistical mb20',
        'loader' => str_repeat('<div class="zib-widget"><p class="placeholder k1"></p><p class="placeholder t1"></p><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></div>', 3), // 加载动画
        'query'  => array('action' => 'user_pay_order'), // add_query_arg
    );

    $html = '<div class="user-pay">';
    $html .= zibpay_get_user_pay_statistical($user_id);
    $html .= '<div class="box-body notop"><div class="title-theme"><b>订单明细</b></div></div>';
    $html .= zib_get_ias_ajaxpager($order_ias);
    $html .= '</div>';

    return zib_get_ajax_ajaxpager_one_centent($html);
}
add_filter('main_user_tab_content_order', 'zibpay_user_page_tab_content_order');

//用户中心vip tab
function zibpay_user_page_tab_content_vip()
{
    $user_id = get_current_user_id();

    return zib_get_ajax_ajaxpager_one_centent(zibpay_user_vip_box($user_id));
}
add_filter('main_user_tab_content_vip', 'zibpay_user_page_tab_content_vip');

/**
 * 用户订单金额统计
 */
function zibpay_get_user_pay_price($user_id, $type = '', $order_type = '')
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return 0;
    }

    global $wpdb;
    $sum        = 0;
    $order_type = $order_type ? 'AND `order_type` = ' . $order_type : '';
    if ('order_price' == $type) {
        $sum = $wpdb->get_var("SELECT SUM(order_price) FROM $wpdb->zibpay_order WHERE `status` = 1 and `user_id` = $user_id $order_type");
    } elseif ('pay_price' == $type) {
        $sum = $wpdb->get_var("SELECT SUM(pay_price) FROM $wpdb->zibpay_order WHERE `status` = 1 and `user_id` = $user_id $order_type");
    }
    return $sum ? $sum : 0;
}

/**
 * 用户订单数量统计
 */
function zibpay_get_user_order_count($user_id, $type = '')
{

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return 0;
    }

    global $wpdb;
    if ($type) {
        $count = $wpdb->get_var("SELECT COUNT(user_id) FROM $wpdb->zibpay_order WHERE `status` = 1 and `user_id` = $user_id AND `order_type` = $type ");
    } else {
        $count = $wpdb->get_var("SELECT COUNT(user_id) FROM $wpdb->zibpay_order WHERE `status` = 1 and `user_id` = $user_id ");
    }
    return $count ? $count : 0;
}
/**
 * 用户中心统计信息
 */
function zibpay_get_user_pay_statistical($user_id)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return 0;
    }

    $count_all = zibpay_get_user_order_count($user_id);

    $sumprice_all = zibpay_get_user_pay_price($user_id, 'pay_price');

    $con = '<div class="row gutters-10">';
    $con .= '<div class="col-sm-6">
            <div class="zib-widget" style="padding-left: 24px;">
                <div>
                ' . zib_get_svg('order-color', null, 'em12 mr6') . '全部订单
                </div>
                <div class="mt10" style="color: #8080f0;line-height: 1.2;">
                <span class="em3x font-bold mr6">' . $count_all . '</span>笔
                </div>
                <div class="abs-right em3x">' . zib_get_svg('order-color', null, 'em12') . '</div>
            </div>
        </div>';
    $con .= '<div class="col-sm-6">
        <div class="zib-widget" style="padding-left: 24px;">
            <div>
            ' . zib_get_svg('money-color', null, 'em12 mr6') . '支付金额
            </div>
            <div class="mt10" style="color: #fc7032;line-height: 1.2;">
            '.zibpay_get_pay_mark().'<span class="em3x font-bold ml6">' . $sumprice_all . '</span>
            </div>
            <div class="abs-right em3x">' . zib_get_svg('money-color', null, 'em12') . '</div>
        </div>
    </div>';

    $con .= '</div>';

    return $con;
}

/**
 * @description: 获取用户支付订单列表
 * @param int $user_id 用户ID：默认为当前登录ID
 * @param int $paged 获取的页码
 * @param int $ice_perpage 每页加载数量
 * @return {*}
 */
function zibpay_get_user_order($user_id = '', $paged = 1, $ice_perpage = 10)
{

    $user_id = $user_id ? $user_id : get_current_user_id();
    if (!$user_id) {
        return;
    }

    //准备查询参数
    $paged       = !empty($_REQUEST['paged']) ? $_REQUEST['paged'] : $paged;
    $ice_perpage = !empty($_REQUEST['ice_perpage']) ? $_REQUEST['ice_perpage'] : $ice_perpage;
    $offset      = $ice_perpage * ($paged - 1);

    global $wpdb;
    $db_order = $wpdb->get_results("SELECT * FROM $wpdb->zibpay_order WHERE `status` = 1 and `user_id` = $user_id  order by pay_time DESC limit $offset,$ice_perpage");
    $lists    = '';
    if ($db_order) {
        $count_all = zibpay_get_user_order_count($user_id);
        $mark      = zibpay_get_pay_mark();

        foreach ($db_order as $order) {

            $order_num   = $order->order_num;
            $order_price = $order->order_price;

            $pay_time        = $order->pay_time;
            $post_id         = $order->post_id;
            $order_type_name = zibpay_get_pay_type_name($order->order_type);

            $get_permalink = get_permalink($post_id);
            $pay_mate      = get_post_meta($post_id, 'posts_zibpay', true);
            $order_price   = !empty($pay_mate['pay_original_price']) ? $pay_mate['pay_original_price'] : $order_price;

            $class = 'order-type-' . $order->order_type;

            $posts_title = get_the_title($post_id);

            $pay_title = $order_type_name ? '<div class="pay-tag badg badg-sm mr6">' . $order_type_name . '</div>' : '';

            $pay_title .= !empty($pay_mate['pay_title']) ? $pay_mate['pay_title'] : $posts_title;
            $pay_title = '<a target="_blank" href="' . $get_permalink . '">' . $pay_title . '</a>';

            $pay_doc = $pay_time;

            $pay_num = '订单号：' . $order_num;

            // $_thumb = zib_post_thumbnail('', 'fit-cover radius8');

            $lists .= '<div class="zib-widget ajax-item mb10 ' . $class . '">';
            $lists .= '<div class="mb6 text-ellipsis em12">' . $pay_title . '</div>';
            $lists .= '<div class="meta-time em09 muted-2-color mb6">' . $pay_num . '</div>';
            $lists .= '<div class="meta-time em09 muted-2-color flex ac jsb hh">' . $pay_doc . '<div class="pull-right">
            <span class="em12 ml10 c-red">' . zibpay_get_order_pay_detail_lists($order, 'mr6') . '</span>
            </div></div>';
            $lists .= '</div>';
        }

        // 显示下一页按钮
        $ajax_url = esc_url(add_query_arg('action', 'user_pay_order', admin_url('admin-ajax.php')));
        $lists .= zib_get_ajax_next_paginate($count_all, $paged, $ice_perpage, $ajax_url);
    } else {
        $lists .= zib_get_ajax_null('暂无支付订单', 40, 'null-order.svg');
    }

    $html = $lists;
    return $html;
}
