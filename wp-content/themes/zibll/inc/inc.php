<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-05-25 14:35:52
 * @LastEditTime: 2022-04-12 18:28:27
 */

/*
 *                        _oo0oo_
 *                       o8888888o
 *                       88" . "88
 *                       (| -_- |)
 *                       0\  =  /0
 *                     ___/`---'\___
 *                   .' \\|     |// '.
 *                  / \\|||  :  |||// \
 *                 / _||||| -:- |||||- \
 *                |   | \\\  - /// |   |
 *                | \_|  ''\---/''  |_/ |
 *                \  .-\__  '-'  ___/-. /
 *              ___'. .'  /--.--\  `. .'___
 *           ."" '<  `.___\_<|>_/___.' >' "".
 *          | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *          \  \ `_.   \_ __\ /__ _/   .-` /  /
 *      =====`-.____`.___ \_____/___.-`___.-'=====
 *                        `=---='
 *
 *
 *      ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 *            佛祖保佑       永不宕机     永无BUG     刀客源码
 *
 */

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

//定义常量
define('ZIB_TEMPLATE_DIRECTORY_URI', get_template_directory_uri()); //本主题
define('ZIB_ROOT_PATH', dirname(__DIR__) . '/'); //本主题的路径

//载入文件
$require_once = array(
    'inc/dependent.php',
    'vendor/autoload.php',
    'inc/class/class.php',
    //'inc/options/zib-code.php',
    //'inc/options/zib-update.php',
    'inc/code/tool.php',
    //'inc/code/update.php',
    'inc/codestar-framework/codestar-framework.php',
    'inc/widgets/widget-class.php',
    'inc/options/options.php',
    'inc/functions/functions.php',
    'inc/widgets/widget-index.php',
    'oauth/oauth.php',
    'zibpay/functions.php',
    'action/function.php',
    'inc/csf-framework/classes/zib-csf.class.php',
);

class ZibAut{
    static public function is_aut(){return true;}
    static public function is_local(){return true;}
    static public function is_update(){return false;}
    static public function aut_required(){}
}


foreach ($require_once as $require) {
    require get_theme_file_path('/' . $require);
}

//codestar演示
//require_once get_theme_file_path('/inc/codestar-framework/samples/admin-options.php');
