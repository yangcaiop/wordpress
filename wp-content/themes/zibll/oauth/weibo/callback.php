<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2021-07-04 03:30:43
 */
//启用 session
session_start();
// 要求noindex
//wp_no_robots();

if (empty($_SESSION['YURUN_WEIBO_STATE'])) {
    wp_safe_redirect(home_url());
    exit;
}

//获取后台配置
$weiboConfig = get_oauth_config('weibo');

$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($weiboConfig['appid'],$weiboConfig['appkey'], $weiboConfig['backurl']);

if ($weiboConfig['agent']) {
    $weiboOAuth->loginAgentUrl = esc_url(home_url('/oauth/weiboagent'));
}

// 获取accessToken，把之前存储的state传入，会自动判断。获取失败会抛出异常！
$accessToken = $weiboOAuth->getAccessToken($_SESSION['YURUN_WEIBO_STATE']);
//验证AccessToken是否有效
$areYouOk = $weiboOAuth->validateAccessToken($accessToken);
if(!$areYouOk){
    wp_die(
		'<h1>' . __( '回调错误.' ) . '</h1>' .
		'<p>' . json_encode($weiboOAuth->result) . '</p>',
		403
	);
    exit;
}

$openid   = $weiboOAuth->openid; // 唯一ID
$userInfo = $weiboOAuth->getUserInfo(); //第三方用户信息
// 处理本地业务逻辑
if ($openid && $userInfo) {
    $userInfo['name'] = !empty($userInfo['screen_name']) ? $userInfo['screen_name'] : '';

    $oauth_data = array(
        'type'   => 'weibo',
        'openid' => $openid,
        'name' => $userInfo['name'],
        'avatar' => !empty($userInfo['avatar_large']) ? $userInfo['avatar_large'] : '',
        'description' => '',
        'getUserInfo' => $userInfo,
    );
      //代理登录
      zib_agent_callback($oauth_data);

    $oauth_result = zib_oauth_update_user($oauth_data);
    if($oauth_result['error']){
        wp_die('<meta charset="UTF-8" />'.($oauth_result['msg']?$oauth_result['msg']:'处理失败'));
        exit;
    }else{
        $rurl = !empty($_SESSION['oauth_rurl']) ? $_SESSION['oauth_rurl'] : $oauth_result['redirect_url'];
        wp_safe_redirect($rurl);
        exit;
    }
}else{
    wp_die(
		'<h1>' . __( '处理错误' ) . '</h1>' .
        '<p>' . json_encode($userInfo) . '</p>' .
        '<p>openid:' .$openid. '</p>',
		403
	);
    exit;
}
wp_safe_redirect(home_url());
exit;
