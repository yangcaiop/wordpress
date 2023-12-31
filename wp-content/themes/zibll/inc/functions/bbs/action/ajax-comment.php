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

//获取采纳回答的模态框
//处理申请提交审批
function zib_bbs_ajax_answer_adopt_modal()
{
    $comment_id = !empty($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
    //权限判断
    if (!$comment_id) {
        zib_ajax_notice_modal('danger', '参数错误');
    }

    echo zib_bbs_get_answer_adopt_modal($comment_id);
    exit;
}
add_action('wp_ajax_answer_adopt_modal', 'zib_bbs_ajax_answer_adopt_modal');

function zib_bbs_ajax_answer_adopt()
{
    $comment_id = !empty($_REQUEST['comment_id']) ? (int) $_REQUEST['comment_id'] : 0;
    $desc       = !empty($_REQUEST['desc']) ? strip_tags(trim($_REQUEST['desc'])) : 0;

    //评论判断
    $comment = get_comment($comment_id);
    if (!$comment) {
        zib_send_json_error('当前回答不存在或者参数错误');
    }
    $posts_id   = $comment->comment_post_ID;
    $comment_id = $comment->comment_ID;

    //执行安全验证
    zib_ajax_verify_nonce();

    //权限判断
    if (!zib_bbs_current_user_can('question_answer_adopt', $posts_id)) {
        zib_ajax_notice_modal('danger', '您没有采纳回答的权限');
    }

    //评论状态判断
    if (wp_get_comment_status($comment) !== 'approved') {
        zib_send_json_error('当前回答暂未通过审核');
    }

    //执行采纳回答
    zib_bbs_answer_adopt($comment, $desc);

    zib_send_json_success(array('msg' => '已采纳该回答', 'hide_modal' => true));
}
add_action('wp_ajax_answer_adopt', 'zib_bbs_ajax_answer_adopt');
