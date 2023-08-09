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

//后台设置微信公众号的自定义菜单
function zib_weixin_gzh_create_menu()
{
    $json = $_REQUEST['json'];
    if (!$json) {
        zib_send_json_error('输入自定义菜单的json配置代码');
    }

    $data = json_decode(wp_unslash(trim($json)), true);

    if (!$data || !is_array($data)) {
        zib_send_json_error('json格式错误');
    }

    $wxConfig = get_oauth_config('weixingzh');
    require_once get_theme_file_path('/oauth/sdk/weixingzh.php');
    if (!$wxConfig['appid'] || !$wxConfig['appkey']) {
        zib_send_json_error('微信公众号配置错误，请检查AppID或AppSecret');
    }

    try {
        $wxOAuth    = new \Weixin\GZH\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
        $CreateMenu = $wxOAuth->CreateMenu($data);

        if (isset($CreateMenu['errcode'])) {
            if (0 == $CreateMenu['errcode']) {
                zib_send_json_success('设置成功，5-10分钟后生效，请耐心等待');
            } else {
                zib_send_json_error('设置失败，请对照一下错误检查</br>错误码：' . $CreateMenu['errcode'] . '</br>错误消息：' . $CreateMenu['errmsg']);
            }
        }

    } catch (\Exception $e) {
        zib_send_json_error($e->getMessage());
    }

}
add_action('wp_ajax_weixin_gzh_menu', 'zib_weixin_gzh_create_menu');

//后台配置ajax提交内容审核测试
function zib_audit_test()
{

    $action     = $_REQUEST['action'];
    $option_key = 'audit_baidu_access_token';

    //刷新数据库保存的access_token
    update_option($option_key, false);

    switch ($action) {
        case 'text_audit_test':
            if (empty($_POST['content'])) {
                echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入需要测试的内容')));
                exit();
            }
            $rel = ZibAudit::text($_POST['content']);
            break;
        case 'img_audit_test':

            $test_img = get_theme_file_path('/inc/csf-framework/assets/images/audit_test.jpg');

            $rel = ZibAudit::image($test_img);
            break;
    }

    if (!empty($rel['error'])) {
        echo (json_encode(array('error' => $rel['error'], 'ys' => 'danger', 'msg' => $rel['msg'])));
        exit();
    }

    if (!empty($rel['conclusion'])) {
        $msg = '审核结果：' . $rel['conclusion'] . '<br/>结果代码：' . $rel['conclusion_type'] . '<br/>消息：' . $rel['msg'];
        echo (json_encode(array('error' => 0, 'msg' => $msg, 'data' => $rel['data'])));
        exit();
    }

    echo (json_encode(array('error' => 0, 'msg' => $rel)));
    exit();
}
add_action('wp_ajax_text_audit_test', 'zib_audit_test');
add_action('wp_ajax_img_audit_test', 'zib_audit_test');

/**
 * @description: 后台AJAX发送测试邮件
 * @param {*}
 * @return {*}
 */
function zib_test_send_mail()
{
    if (empty($_POST['email'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入邮箱帐号')));
        exit();
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo (json_encode(array('error' => 1, 'msg' => '邮箱格式错误')));
        exit();
    }
    $blog_name = get_bloginfo('name');
    $blog_url  = get_bloginfo('url');
    $title     = '[' . $blog_name . '] 测试邮件';

    $message = '您好！ <br />';
    $message .= '这是一封来自' . $blog_name . '[' . $blog_url . ']的测试邮件<br />';
    $message .= '该邮件由网站后台发出，如果非您本人操作，请忽略此邮件 <br />';
    $message .= current_time("Y-m-d H:i:s");

    try {
        $test = wp_mail($_POST['email'], $title, $message);
    } catch (\Exception $e) {
        echo array('error' => 1, 'msg' => $e->getMessage());
        exit();
    }
    if ($test) {
        echo (json_encode(array('error' => 0, 'msg' => '后台已操作')));
    } else {
        echo (json_encode(array('error' => 1, 'msg' => '发送失败')));
    }
    exit();
}
add_action('wp_ajax_test_send_mail', 'zib_test_send_mail');

//后台下载老数据
function zib_export_old_options()
{

    $nonce = (!empty($_GET['nonce'])) ? sanitize_text_field(wp_unslash($_GET['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'export_nonce')) {
        die(esc_html__('安全效验失败！', 'csf'));
    }
    // Export
    header('Content-Type: application/json');
    header('Content-disposition: attachment; filename=zibll-old-options-' . date('Y-m-d') . '.json');
    header('Content-Transfer-Encoding: binary');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo json_encode(get_option('Zibll'));
    die();
}
add_action('wp_ajax_export_old_options', 'zib_export_old_options');

function zib_test_send_sms()
{
    if (empty($_POST['phone_number'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入手机号码')));
        exit();
    }

    echo json_encode(ZibSMS::send($_POST['phone_number'], '888888'));
    exit();
}
add_action('wp_ajax_test_send_sms', 'zib_test_send_sms');

//导入主题设置
function zib_ajax_options_import()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }

    $data = !empty($_REQUEST['import_data']) ? $_REQUEST['import_data'] : '';

    if (!$data) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请粘贴需导入配置的json代码')));
        exit();
    }

    $import_data = json_decode(wp_unslash(trim($data)), true);

    if (empty($import_data) || !is_array($import_data)) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => 'json代码格式错误，无法导入')));
        exit();
    }

    zib_options_backup('导入配置 自动备份');

    $prefix = 'zibll_options';
    update_option($prefix, $import_data);
    echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '主题设置已导入，请刷新页面')));
    exit();
}
add_action('wp_ajax_options_import', 'zib_ajax_options_import');

//备份主题设置
function zib_ajax_options_backup()
{
    $type   = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '手动备份';
    $backup = zib_options_backup($type);
    echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '当前配置已经备份')));
    exit();
}
add_action('wp_ajax_options_backup', 'zib_ajax_options_backup');

function zib_ajax_options_backup_delete()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }
    if (empty($_REQUEST['key'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
        exit();
    }

    $prefix = 'zibll_options';
    if ('options_backup_delete_all' == $_REQUEST['action']) {
        update_option($prefix . '_backup', false);
        echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '已删除全部备份数据')));
        exit();
    }

    $options_backup = get_option($prefix . '_backup');

    if ('options_backup_delete_surplus' == $_REQUEST['action']) {
        if ($options_backup) {
            $options_backup = array_reverse($options_backup);
            update_option($prefix . '_backup', array_reverse(array_slice($options_backup, 0, 3)));
            echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '已删除多余备份数据，仅保留最新3份')));
            exit();
        }
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '暂无可删除的数据')));
    }

    if (isset($options_backup[$_REQUEST['key']])) {
        unset($options_backup[$_REQUEST['key']]);

        update_option($prefix . '_backup', $options_backup);
        echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '所选备份已删除')));
    } else {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '此备份已删除')));
    }
    exit();
}
add_action('wp_ajax_options_backup_delete', 'zib_ajax_options_backup_delete');
add_action('wp_ajax_options_backup_delete_all', 'zib_ajax_options_backup_delete');
add_action('wp_ajax_options_backup_delete_surplus', 'zib_ajax_options_backup_delete');

function zib_ajax_options_backup_restore()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }
    if (empty($_REQUEST['key'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
        exit();
    }

    $prefix         = 'zibll_options';
    $options_backup = get_option($prefix . '_backup');
    if (isset($options_backup[$_REQUEST['key']]['data'])) {
        update_option($prefix, $options_backup[$_REQUEST['key']]['data']);
        echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '主题设置已恢复到所选备份[' . $_REQUEST['key'] . ']')));
    } else {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '备份恢复失败，未找到对应数据')));
    }
    exit();
}
add_action('wp_ajax_options_backup_restore', 'zib_ajax_options_backup_restore');
