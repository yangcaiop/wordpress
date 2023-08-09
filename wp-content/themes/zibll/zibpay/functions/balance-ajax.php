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

//余额充值
function zibpay_ajax_balance_charge_modal()
{

    $user_id = get_current_user_id();

    echo zibpay_get_balance_charge_modal($user_id);
    exit;
}
add_action('wp_ajax_balance_charge_modal', 'zibpay_ajax_balance_charge_modal');

//购买积分
function zibpay_ajax_points_pay_modal()
{

    $user_id = get_current_user_id();

    echo zibpay_get_points_pay_modal($user_id);
    exit;
}
add_action('wp_ajax_points_pay_modal', 'zibpay_ajax_points_pay_modal');

//积分支付
function zibpay_points_initiate_pay()
{
    $post_id = !empty($_REQUEST['post_id']) ? (int) $_REQUEST['post_id'] : 0;
    $user_id = get_current_user_id();

    if (!$user_id) {
        zib_send_json_error('请先登录');
    }

    $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);
    $post     = get_post($post_id);

    if (empty($post->ID) || empty($pay_mate['pay_type']) || 'no' == $pay_mate['pay_type'] || !zibpay_post_is_points_modo($pay_mate)) {
        zib_send_json_error('商品数据获取错误');
    }

    $order_type = $pay_mate['pay_type'];
    $price      = (int)$pay_mate['points_price'];
    $vip_level  = zib_get_user_vip_level($user_id);
    if ($vip_level && _pz('pay_user_vip_' . $vip_level . '_s', true)) {
        $vip_price = isset($pay_mate['vip_' . $vip_level . '_points']) ? (int)$pay_mate['vip_' . $vip_level . '_points'] : 0;
        //会员金额和正常金额取更小值
        $price = $vip_price < $price ? $vip_price : $price;
    }

    $post_author    = $post->post_author;
    $add_order_data = array(
        'user_id'     => $user_id,
        'post_id'     => $post_id,
        'post_author' => $post_author,
        'order_price' => 0,
        'order_type'  => $order_type,
        'pay_type'    => 'points',
        'pay_price'   => 0,
        'pay_detail'  => array(
            'points' => $price,
        ),
        'pay_time'    => current_time("Y-m-d H:i:s"),
    );

    //分成数据
    if (_pz('pay_income_s')) {
        $points_ratio  = zibpay_get_user_income_points_ratio($post_author);
        $income_points = (int) (($price * $points_ratio) / 100);
        if ($income_points > 0) {
            $add_order_data['income_detail'] = array(
                'points' => $income_points,
            );
        }
    }

    //创建新订单
    $order = ZibPay::add_order($add_order_data);
    if (!$order) {
        zib_send_json_error('订单创建失败');
    }

    $pay = array(
        'order_num' => $order['order_num'],
        'pay_type'  => 'points',
        'pay_price' => 0,
        'pay_num'   => $order['order_num'],
    );

    // 更新订单状态
    ZibPay::payment_order($pay);

    $update_points_data = array(
        'order_num' => $order['order_num'], //订单号
        'value'     => -$price, //值 整数为加，负数为减去
        'type'      => '积分支付', //类型说明
        'desc'      => zibpay_get_pay_type_name($order_type), //说明
        'time'      => current_time('Y-m-d H:i'),
    );

    zibpay_update_user_points($user_id, $update_points_data);

    zib_send_json_success(['reload' => true, 'msg' => '购买成功']);

}
add_action('wp_ajax_points_initiate_pay', 'zibpay_points_initiate_pay');

//管理员后台添加或扣除余额或者积分
function zibpay_ajax_admin_update_user_balance_or_points()
{

    if (!is_super_admin()) {
        zib_send_json_error('权限不足，仅管理员可操作');
    }

    $action  = $_REQUEST['action'];
    $user_id = !empty($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;
    $val     = !empty($_REQUEST['val']) ? ($action === 'admin_update_user_balance' ? round((float)$_REQUEST['val'], 2) : (int) $_REQUEST['val']) : 0;
    $decs    = !empty($_REQUEST['decs']) ? esc_attr($_REQUEST['decs']) : '';
    $type    = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';

    if (!$type) {
        zib_send_json_error('请选择添加或扣除');
    }

    if (!$val) {
        zib_send_json_error('请输入数额');
    }

    if (!$user_id) {
        zib_send_json_error('数据或或环境异常');
    }

    $val = $type === 'add' ? $val : -$val;

    $data = array(
        'value' => $val, //值 整数为加，负数为减去
        'type'  => '管理员手动' . ($type === 'add' ? '添加' : '扣除'),
        'desc'  => $decs, //说明
    );

    if ($action === 'admin_update_user_balance') {
        //余额管理
        if (!_pz('pay_balance_s')) {
            zib_send_json_error('余额功能已关闭');
        }
        zibpay_update_user_balance($user_id, $data);
    } else {
        //积分管理
        if (!_pz('points_s')) {
            zib_send_json_error('积分功能已关闭');
        }
        zibpay_update_user_points($user_id, $data);
    }

    zib_send_json_success('操作成功，请刷新页面后查看最新数据');

}
add_action('wp_ajax_admin_update_user_balance', 'zibpay_ajax_admin_update_user_balance_or_points');
add_action('wp_ajax_admin_update_user_points', 'zibpay_ajax_admin_update_user_balance_or_points');
