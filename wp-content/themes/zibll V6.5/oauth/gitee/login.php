<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2021-07-04 02:51:50
 */
require dirname(__FILE__) . '/../../../../../wp-load.php';
//启用 session
session_start();
// 要求noindex
//wp_no_robots();

//获取后台配置
$Config = get_oauth_config('gitee');
$OAuth  = new \Yurun\OAuthLogin\Gitee\OAuth2($Config['appid'], $Config['appkey'], $Config['backurl']);

if ($Config['agent']) {
    $OAuth->loginAgentUrl = esc_url(home_url('/oauth/giteeagent'));
}
//代理登录
zib_agent_login();
// 可选属性
/*
// 是否在登录页显示注册
$alipayOAuth->allowSignup = false;
*/

$url = $OAuth->getAuthUrl();
// 存储sdk自动生成的state，回调处理时候要验证
$_SESSION['YURUN_GITEE_STATE'] = $OAuth->state;
// 储存返回页面
$_SESSION['oauth_rurl']  = !empty($_GET["rurl"]) ? $_GET["rurl"] : '';

// 跳转到登录页
header('location:' . $url);
