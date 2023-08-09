<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-06-18 12:16:27
 * @LastEditTime: 2022-04-21 14:49:53
 */

//启用 session
@session_start();

//微信配置接口验证
if (!empty($_REQUEST['echostr']) && !empty($_REQUEST['signature'])) {
    //微信接口校验
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce     = $_GET["nonce"];

    $token  = _pz('oauth_weixingzh_option', '', 'token');
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);

    if ($tmpStr == $signature) {
        echo $_REQUEST['echostr'];
        exit();
    }
}
//获取后台配置
$wxConfig = get_oauth_config('weixingzh');

//微信APP内跳转登录
if (zib_is_wechat_app()) {
    // 在微信APP内使用无感登录接口

    $wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
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
            'type'        => 'weixingzh',
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
            '<h2>' . __('出现错误') . '</h2>' .
            '<p>' . json_encode($userInfo) . '</p>',
            403
        );
        exit;
    }
    exit();
}

//扫码登录流程
require_once get_theme_file_path('/oauth/sdk/weixingzh.php');
$wxOAuth = new \Weixin\GZH\OAuth2($wxConfig['appid'], $wxConfig['appkey']);

$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'callback';

switch ($action) {
    case 'callback':
        //接受微信发过来的信息
        $callback = $wxOAuth->callback();
        if ($callback) {
            $EventKey = str_replace('qrscene_', '', $callback['EventKey']);
            update_option('weixingzh_event_key_' . $EventKey, $callback); //储存临时数据
            //给用户发送消息
            if (!empty($wxConfig['subscribe_msg']) && $callback['Event'] == 'subscribe') {
                $wxOAuth->sendMessage($wxConfig['subscribe_msg']);
                exit();
            } elseif (!empty($wxConfig['scan_msg']) && $callback['Event'] == 'SCAN') {
                $wxOAuth->sendMessage($wxConfig['scan_msg']);
                exit();
            }
        }
        //自动回复
        $wxOAuth->autoReply($wxConfig['auto_reply']);
        exit();
        break;

    case 'check_callback':
        //代理登录
        zib_agent_login();
        //前端验证是否回调
        $state = !empty($_REQUEST['state']) ? $_REQUEST['state'] : '';
        if (!$state) {
            echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
            exit();
        }

        $option = get_option('weixingzh_event_key_' . $state); //读取临时数据
        if (!$option) {
            echo (json_encode(array('error' => 1)));
            exit();
        }
        delete_option('weixingzh_event_key_' . $state); //删除临时数据
        $goto_uery_arg = array(
            'action' => 'login',
            'openid' => $option['FromUserName'],
        );
        if (!empty($_REQUEST['oauth_rurl'])) {
            $goto_uery_arg['oauth_rurl'] = $_REQUEST['oauth_rurl'];
        }

        echo (json_encode(array('goto' => add_query_arg($goto_uery_arg, $wxConfig['backurl']), 'option' => $option)));
        exit();

        break;

    case 'login':
        //前台登录或者绑定
        //代理登录验证
        zib_agent_login();
        $openId = !empty($_REQUEST['openid']) ? $_REQUEST['openid'] : '';
        if (!$openId) {
            wp_die('参数传入错误');
        }
        $userInfo = $wxOAuth->getUserInfo($openId); //第三方用户信息

        // 处理本地业务逻辑
        if (!empty($userInfo['openid'])) {
            $userInfo['name']   = !empty($userInfo['nickname']) ? $userInfo['nickname'] : '';
            $userInfo['avatar'] = !empty($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '';

            $oauth_data = array(
                'type'        => 'weixingzh',
                'openid'      => $userInfo['openid'],
                'name'        => $userInfo['name'],
                'avatar'      => $userInfo['avatar'],
                'description' => '',
                'getUserInfo' => $userInfo,
            );
            //代理登录回调
            zib_agent_callback($oauth_data);

            $oauth_result = zib_oauth_update_user($oauth_data);

            if ($oauth_result['error']) {
                wp_die('处理出错：' . (isset($oauth_result['msg']) ? $oauth_result['msg'] : ''));
                exit;
            } else {
                $rurl = !empty($_SESSION['oauth_rurl']) ? $_SESSION['oauth_rurl'] : (!empty($_REQUEST['oauth_rurl']) ? $_REQUEST['oauth_rurl'] : $oauth_result['redirect_url']);
                wp_safe_redirect($rurl);
                exit;
            }
        } else {
            //   file_put_contents(__DIR__ . '/error.log', var_export($userInfo, TRUE));
        }

        break;
}

wp_safe_redirect(home_url());
exit;
