<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2021-07-16 02:03:37
 */
//启用 session
session_start();
// 要求noindex
//wp_no_robots();
require_once(get_theme_file_path('/oauth/sdk/weixingzh.php'));


//获取后台配置
$wxConfig = get_oauth_config('weixingzh');
if (!$wxConfig['appid'] || !$wxConfig['appkey']) {
    echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '微信公众号参数错误')));
    exit();
}

//代理登录
zib_agent_login();
$_SESSION['oauth_rurl']  = !empty($_REQUEST["rurl"]) ? $_REQUEST["rurl"] : ''; // 储存返回页面

try {
    if (zib_is_wechat_app()) {
        // 在微信APP内使用此接口
        $wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
        $url = $wxOAuth->getWeixinAuthUrl($wxConfig['backurl']);
        $_SESSION['YURUN_WEIXIN_STATE'] = $wxOAuth->state; //储存验证信息

        header('location:' . $url);
        exit();
    } else {
        $WeChat = new \Weixin\GZH\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
        $qrcode_array = $WeChat->getQrcode();                 //生成二维码
        $qrcode = zib_get_qrcode_base64($qrcode_array['url']);
        $_SESSION['YURUN_WEIXIN_GZH_STATE'] = $WeChat->state; //储存验证信息

        $text = '微信扫码' . (!empty($_REQUEST["bind"]) ? '绑定' : '登录');
        $html = '<img class="signin-qrcode-img" src="' . $qrcode . '" alt="' . $text . '">';
        $html .= '<div class="text-center mt20 em12"><i class="c-green fa fa-weixin mr6"></i>' . $text . '</div>';

        echo (json_encode(array('html' => $html, 'url' => $wxConfig['backurl'], 'state' => $WeChat->state)));
    }
    exit();
} catch (\Exception $e) {
    echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => $e->getMessage())));
    exit();
}

//结束，下面为跳转内容
echo '<img src="' . $qrcode . '">';
?>
<script type="text/javascript" src="http://www.lrfun.com/statics/fun2/js/jquery.min.js"></script>
<script type="text/javascript">
    checkLogin();

    function checkLogin() {
        $.post("<?= $wxConfig['backurl'] ?>", {
            state: "<?= $WeChat->state ?>",
            action: "check_callback"
        }, function(n) {
            //做逻辑判断，登录跳转
            if (n.goto) {
                window.location.href = n.goto;
                window.location.reload;
            } else {
                setTimeout(function() {
                    checkLogin();
                }, 2000);
            }
        }, "Json");
    }
</script>