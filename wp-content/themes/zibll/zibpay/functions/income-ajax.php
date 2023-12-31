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

function zibpay_ajax_income_order_lists()
{

    $user_id = get_current_user_id();

    zib_ajax_send_ajaxpager(zibpay_get_user_income_order_lists($user_id));
}
add_action('wp_ajax_income_order_lists', 'zibpay_ajax_income_order_lists');

function zibpay_ajax_income_post_lists()
{

    $user_id = get_current_user_id();

    zib_ajax_send_ajaxpager(zibpay_get_user_income_post_lists($user_id));
}
add_action('wp_ajax_income_post_lists', 'zibpay_ajax_income_post_lists');
