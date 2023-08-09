<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-05-09 10:43:12
 * @LastEditTime: 2022-04-21 14:41:05
 */


//启用 session
@session_start();

if (empty($_SESSION['YURUN_WEIXIN_STATE'])) {
    wp_safe_redirect(home_url());
    exit;
}

//获取后台配置
$wxConfig = get_oauth_config('weixin');
$wxOAuth  = new \Yurun\OAuthLogin\Weixin\OAuth2($wxConfig['appid'], $wxConfig['appkey']);

if ($wxConfig['agent']) {
    $wxOAuth->loginAgentUrl = esc_url(home_url('/oauth/weixinagent'));
}

// 获取accessToken，把之前存储的state传入，会自动判断。获取失败会抛出异常！
$accessToken = $wxOAuth->getAccessToken($_SESSION['YURUN_WEIXIN_STATE']);
//验证AccessToken是否有效
$areYouOk = $wxOAuth->validateAccessToken($accessToken);
if (!$areYouOk) {
    wp_die(
        '<h1>' . __('回调错误.') . '</h1>' .
        '<p>' . json_encode($wxOAuth->result) . '</p>',
        403
    );
    exit;
}

$openid   = $wxOAuth->openid; // 唯一ID
$userInfo = $wxOAuth->getUserInfo(); //第三方用户信息
// 处理本地业务逻辑
if ($openid && $userInfo) {
    $userInfo['name'] = !empty($userInfo['nickname']) ? $userInfo['nickname'] : '';

    $oauth_data = array(
        'type'        => 'weixin',
        'openid'      => $openid,
        'name'        => $userInfo['name'],
        'avatar'      => !empty($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '',
        'description' => '',
        'getUserInfo' => $userInfo,
    );
    //代理登录
    zib_agent_callback($oauth_data);

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
        '<h1>' . __('处理错误') . '</h1>' .
        '<p>' . json_encode($userInfo) . '</p>' .
        '<p>openid:' . $openid . '</p>',
        403
    );
    exit;
}
wp_safe_redirect(home_url());
exit;
