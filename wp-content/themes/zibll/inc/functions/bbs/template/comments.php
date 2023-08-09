<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2021-09-30 13:53:09
 */
defined('ABSPATH') or die('无法直接加载此文件.');

global $post;
$post_id = get_queried_object_id();
if (!comments_open($post_id) || _pz('close_comments')) return;

$count_t = $post->comment_count;
$user_id = get_current_user_id();

?>


<div id="comments">
    <div class="comment-box">
        <?php echo zib_bbs_get_respond(); ?>
    </div>
    <div class="zib-widget comment-box" id="postcomments">
        <ol class="commentlist list-unstyled bbs-commentlist">
            <?php
            if ($count_t) {
                echo zib_bbs_get_comment_title();
            }
            if (have_comments()) {
                wp_list_comments(
                    array(
                        'type'              => 'comment',
                        'callback'          => 'zib_comments_list',
                    )
                );
                $loader = '<div style="display:none;" class="post_ajax_loader"><ul class="list-inline flex"><div class="avatar-img placeholder radius"></div><li class="flex1"><div class="placeholder s1 mb6" style="width: 30%;"></div><div class="placeholder k2 mb10"></div><i class="placeholder s1 mb6"></i><i class="placeholder s1 mb6 ml10"></i></li></ul><ul class="list-inline flex"><div class="avatar-img placeholder radius"></div><li class="flex1"><div class="placeholder s1 mb6" style="width: 30%;"></div><div class="placeholder k2 mb10"></div><i class="placeholder s1 mb6"></i><i class="placeholder s1 mb6 ml10"></i></li></ul><ul class="list-inline flex"><div class="avatar-img placeholder radius"></div><li class="flex1"><div class="placeholder s1 mb6" style="width: 30%;"></div><div class="placeholder k2 mb10"></div><i class="placeholder s1 mb6"></i><i class="placeholder s1 mb6 ml10"></i></li></ul><ul class="list-inline flex"><div class="avatar-img placeholder radius"></div><li class="flex1"><div class="placeholder s1 mb6" style="width: 30%;"></div><div class="placeholder k2 mb10"></div><i class="placeholder s1 mb6"></i><i class="placeholder s1 mb6 ml10"></i></li></ul></div>';
                echo $loader;
                zib_bbs_comment_paginate();
            } else {
                echo zib_get_null('没有回复内容', 50, 'null.svg', 'comment-null');
                echo '<div class="pagenav hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
            }
            ?>
        </ol>
    </div>
</div>