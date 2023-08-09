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

$page_type = 'user_center';
do_action('locate_template_' . $page_type);
get_header();
?>
<main class="main-min-height">
    <?php do_action($page_type . '_page_header'); ?>
    <div class="container">
        <?php do_action($page_type . '_page_content'); ?>
    </div>
    <?php do_action($page_type . '_page_footer'); ?>
</main>
<?php
get_footer();
