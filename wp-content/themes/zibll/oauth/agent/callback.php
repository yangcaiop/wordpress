<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-07-04 00:38:13
 * @LastEditTime: 2022-04-21 14:47:17
 */

//启用 session
session_start();

if (empty($_SESSION['OAUTH_AGENT_STATE'])) {
    wp_safe_redirect(home_url());
    exit;
}

//获取后台配置
$config = _pz('oauth_agent_client_option');

require_once(get_theme_file_path('/oauth/sdk/agent.php'));
$OAuth  = new \agent\OAuth2($config);

if (!empty($_REQUEST['openid']) && !empty($_REQUEST['type']) && $OAuth->verifySign()) {
    if (isset($_REQUEST['sign'])) unset($_REQUEST['sign']);
    if (isset($_REQUEST['oauth_rurl'])) {
        $_SESSION['oauth_rurl'] = $_REQUEST['oauth_rurl'];
        unset($_REQUEST['oauth_rurl']);
    }
    $oauth_data = $_REQUEST;
    $oauth_data['getUserInfo'] = $_REQUEST;

    $oauth_result = zib_oauth_update_user($oauth_data);

    if ($oauth_result['error']) {
        wp_die('<meta charset="UTF-8" />' . ($oauth_result['msg'] ? $oauth_result['msg'] : '处理失败'));
        exit;
    } else {
        $rurl = !empty($_SESSION['oauth_rurl']) ? $_SESSION['oauth_rurl'] : $oauth_result['redirect_url'];
        wp_safe_redirect($rurl);
        exit;
    }
} else {
    wp_die(
        '<h2>' . __('出现错误错误，请重试') . '</h2>',
        403
    );
    exit;
}


wp_safe_redirect(home_url());
exit;
