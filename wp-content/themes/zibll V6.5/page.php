<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2022-05-01 19:23:14
 */

get_header();
$header_style = zib_get_page_header_style();
?>
<main class="container">
    <div class="content-wrap">
        <div class="content-layout">
            <?php while (have_posts()) : the_post(); ?>
                <?php if ($header_style != 1) {
                    echo zib_get_page_header();
                } ?>
                <div class="nopw-sm box-body theme-box radius8 main-bg main-shadow">
                    <?php if ($header_style == 1) {
                        echo zib_get_page_header();
                    } ?>
                    <article class="article wp-posts-content">
                        <?php the_content();
                        wp_link_pages(
                            array(
                                'before'           => '<p class="text-center post-nav-links radius8 padding-6">',
                                'after'            => '</p>',
                            )
                        ); ?>
                    </article>
                </div>
            <?php endwhile;  ?>
            <?php comments_template('/template/comments.php', true); ?>
        </div>
    </div>
    <?php get_sidebar(); ?>
</main>
<?php
get_footer();
