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

/**获取分类全部文章数量 */
function zib_get_cat_postcount($id, $type = 'category')
{

    $cache_num = wp_cache_get($id, 'term_all_posts_count', true);
    if ($cache_num !== false) {
        return $cache_num;
    }

    $args = array(
        'posts_per_page' => 1,
        'paged'          => 1,
        'tax_query'      => array(
            array(
                'taxonomy'         => $type,
                'field'            => 'id',
                'terms'            => array($id),
                'include_children' => true,
            ),
        ),
    );

    $the_query = new WP_Query($args);
    $count     = $the_query->found_posts;
    wp_reset_query();
    wp_reset_postdata();
    //添加缓存，12小时有效
    wp_cache_set($id, $count, 'term_all_posts_count', 43200);
    return $count;
}

//获取分类全部文章的阅读总和
function get_term_posts_meta_count($term_id, $mata)
{
    $term_id = (int) $term_id;
    global $wpdb;
    $cache_num = wp_cache_get($term_id, 'term_posts_' . $mata . '_count', true);
    if ($cache_num === false) {
        $num = $wpdb->get_var("SELECT sum(meta_value) FROM $wpdb->posts
        LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
        INNER JOIN $wpdb->term_taxonomy ON ( $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id )
        WHERE ( $wpdb->postmeta.meta_key = '$mata')
        AND ($wpdb->posts.post_type = 'post')
        AND ($wpdb->posts.post_status = 'publish')
        AND ( $wpdb->term_taxonomy.term_id = $term_id
        OR $wpdb->term_taxonomy.parent = $term_id)");
        //添加缓存，12小时有效
        wp_cache_set($term_id, $num, 'term_posts_' . $mata . '_count', 43200);
    } else {
        $num = $cache_num;
    }
    return $num ? _cut_count($num) : 0;
}

function zib_topics_cover($cat_id = '')
{
    $desc = trim(strip_tags(category_description()));
    if (is_super_admin() && !$desc) {
        $desc = '请在Wordress后台-文章-文章专题中添加专题描述！';
    }
    $desc .= zib_get_term_admin_edit('编辑此专题');

    global $wp_query;
    if (!$cat_id) {
        $cat_id = get_queried_object_id();
    }
    $cat   = get_term($cat_id, 'topics');
    $count = $cat->count;
    $title = '<b class="em12"><i class="fa fa-cube mr6" aria-hidden="true"></i>' . $cat->name . '</b>';
    $img   = zib_get_taxonomy_img_url(null, null, _pz('topics_default_cover'));
    zib_page_cover($title, $img, $desc, '', true);
}

function zib_cat_cover($cat_id = '')
{
    if (!$cat_id) {
        $cat_id = get_queried_object_id();
    }
    $desc = trim(strip_tags(category_description()));
    if (is_super_admin() && !$desc) {
        $desc = '请在Wordress后台-文章-文章分类中添加分类描述！';
    }

    $desc .= zib_get_term_admin_edit('编辑此分类');

    //global $wp_query;

    $cat   = get_category($cat_id);
    $count = zib_get_cat_postcount($cat_id, 'category');
    $title = '<i class="fa fa-folder-open em12 mr10 ml6" aria-hidden="true"></i>' . $cat->cat_name;
    $title .= '<span class="icon-spot">共' . $count . '篇</span>';
    //$title .='<pre>'. json_encode($wp_query) .'</pre>';
    if (_pz('page_cover_cat_s', true)) {
        $img = zib_get_taxonomy_img_url(null, null, _pz('cat_default_cover'));
        zib_page_cover($title, $img, $desc);
    } else {
        echo '<div class="zib-widget">';
        echo '<h4 class="title-h-center">' . $title . '</h4>';
        echo '<div class="muted-2-color">' . $desc . '</div>';
        echo '</div>';
    }
}

function zib_tag_cover()
{
    $desc = trim(strip_tags(tag_description()));
    if (is_super_admin() && !$desc) {
        $desc = '请在Wordress后台-文章-文章分类中添加标签描述！';
    }

    $desc .= zib_get_term_admin_edit('编辑此标签');
    global $wp_query;
    $tag_id = get_queried_object_id();
    $tag    = get_tag($tag_id);
    $count  = $tag->count;
    $title  = '<i class="fa fa-tag em12 mr10 ml6" aria-hidden="true"></i>' . $tag->name;
    $title .= '<span class="icon-spot">共' . $count . '篇</span>';
    if (_pz('page_cover_tag_s', true)) {
        $img = zib_get_taxonomy_img_url(null, null, _pz('tag_default_cover'));
        zib_page_cover($title, $img, $desc);
    } else {
        echo '<div class="zib-widget">';
        echo '<h4 class="title-h-center">' . $title . '</h4>';
        echo '<div class="muted-2-color">' . $desc . '</div>';
        echo '</div>';
    }
}

function zib_page_cover($title, $img, $desc, $more = '', $center = false)
{
    $paged = (get_query_var('paged', 1));
    $attr  = '';
    if ($paged && $paged > 1) {
        $title .= ' <small class="icon-spot">第' . $paged . '页</small>';
    } else {
        $attr = 'win-ajax-replace="page-cover"';
    }
    $src = ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-lg.svg';
    $img = $img ? $img : _pz('page_cover_img', ZIB_TEMPLATE_DIRECTORY_URI . '/img/user_t.jpg');
    ?>
    <div <?php echo $attr; ?> class="page-cover zib-widget">
        <img class="lazyload fit-cover" <?php echo zib_is_lazy('lazy_cover', true) ? 'src="' . $src . '" data-src="' . $img . '"' : 'src="' . $img . '"'; ?>>
        <div class="absolute <?php echo $center ? 'page-mask' : 'linear-mask'; ?>"></div>
        <div class="list-inline box-body <?php echo $center ? 'abs-center text-center' : 'page-cover-con'; ?>">
            <div class="<?php echo $center ? 'title-h-center' : 'title-h-left'; ?>">
                <b><?php echo $title; ?></b>
            </div>
            <div class="em09 page-desc"><?php echo $desc; ?></div>
        </div>
        <?php echo $more; ?>
    </div>
<?php }

/**
 * @description: 页面AJAX菜单
 * @param {*}
 * @return {*}
 */
function zib_ajax_option_menu($page = 'home', $class = 'ajax-option ajax-replace', $link_class = 'ajax-next', $attr = 'win-ajax-replace="filter"')
{
    if (!empty($_GET['nofilter'])) {
        return;
    }

    $page_args = array();
    if ($page == 'home') {
        $page_args['home'] = array(
            'cat'            => false,
            'cat_option'     => false,
            'topics'         => false,
            'topics_option'  => false,
            'tag'            => false,
            'tag_option'     => false,
            'orderby'        => _pz('home_list1_orderby_s'),
            'orderby_option' => _pz('home_list1_orderby_option'),
        );
    } else {
        $page_args[$page] = array(
            'cat'            => _pz('ajax_list_' . $page . '_cat'),
            'cat_option'     => _pz('ajax_list_option_' . $page . '_cat'),
            'topics'         => _pz('ajax_list_' . $page . '_topics'),
            'topics_option'  => _pz('ajax_list_option_' . $page . '_topics'),
            'tag'            => _pz('ajax_list_' . $page . '_tag'),
            'tag_option'     => _pz('ajax_list_option_' . $page . '_tag'),
            'orderby'        => _pz($page . '_orderby_s'),
            'orderby_option' => _pz($page . '_orderby_option'),
        );
    }

    $con = '';
    if ($page_args[$page]['cat']) {
        $con .= zib_get_option_terms_but($page_args[$page]['cat_option'], $link_class, '分类');
    }
    if ($page_args[$page]['topics']) {
        $con .= zib_get_option_terms_but($page_args[$page]['topics_option'], $link_class, '专题');
    }
    if ($page_args[$page]['tag']) {
        $con .= zib_get_option_terms_but($page_args[$page]['tag_option'], $link_class, '标签');
    }
    if ($page_args[$page]['orderby']) {
        $con .= zib_get_option_orderby_but($page_args[$page]['orderby_option'], $link_class);
    }

    if (!$con) {
        return;
    }

    $html = '<div class="' . $class . '" ' . $attr . '>' . $con . '</div>';
    $html .= '<div></div>'; //空白内容，解决css奇数偶数行
    echo $html;
}

function zib_get_option_list_orderby()
{
    $args = array(
        'modified'      => '更新',
        'date'          => '发布',
        'views'         => '浏览',
        'like'          => '点赞',
        'comment_count' => '评论',
        'favorite'      => '收藏',
        'rand'          => '随机',
    );
    return $args;
}
//排序方式
function zib_get_option_orderby_but($option = array(), $link_class = 'ajax-next')
{
    $defaults = array(
        'lists'    => array(),
        'dropdown' => false,
    );

    $option = wp_parse_args((array) $option, $defaults);
    if (!$option['lists'] && !$option['dropdown']) {
        return '';
    }

    $html     = '';
    $all_args = zib_get_option_list_orderby();

    $dropdown_but = '';
    $but          = '';
    $uri          = home_url(add_query_arg(array()));
    if ($uri) {
        $uri = preg_replace('/page\/([\d]*)/', '', $uri);
    }

    foreach ($option['lists'] as $key) {
        $_class = $link_class;
        if (isset($_GET['orderby']) && $_GET['orderby'] == $key) {
            $_class = $link_class . ' focus-color';
        }
        $href = add_query_arg(array('orderby' => $key, 'paged' => false), $uri);
        $but .= '<a ajax-replace="true" class="' . $_class . '" href="' . $href . '">' . $all_args[$key] . '</a>';
    }
    if ($option['dropdown']) {
        foreach ($all_args as $key => $value) {
            $_class = $link_class;
            if (isset($_GET['orderby']) && $_GET['orderby'] == $key) {
                $_class = $link_class . ' focus-color';
            }
            $href = add_query_arg(array('orderby' => $key, 'paged' => false), $uri);
            $dropdown_but .= '<li><a ajax-replace="true" class="' . $_class . '" href="' . $href . '">' . $value . '</a></li>';
        }
    }

    if (!$but && !$dropdown_but) {
        return '';
    }

    $is_dropdown = ($option['dropdown'] && $dropdown_but) ? true : false;
    $d_but       = $is_dropdown ? '<a href="javascript:;" data-toggle="dropdown"><span name="cat">排序</span><i class="fa fa-fw fa-sort opacity5" aria-hidden="true"></i></a>' : '排序';

    $html .= '<div class="flex ac">';
    $html .= '<div class="option-dropdown splitters-this-r dropdown flex0">';
    $html .= $d_but;
    $html .= $is_dropdown ? '<ul class="dropdown-menu">' . $dropdown_but . '</ul>' : '';
    $html .= '</div>';
    $html .= '<ul class="list-inline scroll-x mini-scrollbar option-items">' . $but . '</ul>';
    $html .= '</div>';

    return $html;
}

function zib_get_option_terms_but($option = array(), $link_class = 'ajax-next', $text = '分类')
{
    $defaults = array(
        'lists'          => array(),
        'dropdown'       => false,
        'dropdown_lists' => array(),
    );

    $option = wp_parse_args((array) $option, $defaults);
    if (!$option['lists'] && (!$option['dropdown'] || !$option['dropdown_lists'])) {
        return '';
    }

    $html         = '';
    $dropdown_but = '';
    $but          = '';
    $this_id      = get_queried_object_id();
    $this_id_s[]  = $this_id;

    $_object = get_queried_object();

    if (!empty($_object->parent)) {
        $this_id_s[] = $_object->parent;
        $this_id_s   = array_merge($this_id_s, get_ancestors($_object->parent, $_object->taxonomy, 'taxonomy'));
    }

    $child_cat  = '';
    $child_name = '';
    if ($option['lists']) {
        $lists = get_terms(array(
            'include' => $option['lists'],
            'orderby' => 'include',
        ));
        foreach ($lists as $term) {
            $_class = $link_class;
            $name   = zib_str_cut($term->name, 0, 8, '...');
            $href   = get_term_link($term);

            if (in_array($term->term_id, $this_id_s)) {
                $_class       = $link_class . ' focus-color';
                $children_ibj = _get_term_hierarchy($_object->taxonomy);
                if (!empty($children_ibj[$term->term_id])) {
                    $child_cat  = $children_ibj[$term->term_id];
                    $child_name = array(
                        'category' => '子分类',
                        'topics'   => '子专题',
                        'post_tag' => '子标签',
                    )[$_object->taxonomy];
                }
            }
            $but .= '<a ajax-replace="true" class="' . $_class . '" href="' . $href . '">' . $name . '</a>';
        }
    }
    if ($option['dropdown'] || $option['dropdown_lists']) {
        $lists = get_terms(array(
            'include' => $option['dropdown_lists'],
            'orderby' => 'include',
        ));
        foreach ($lists as $term) {
            $_class = $link_class;
            if ($this_id == $term->term_id) {
                $_class = $link_class . ' focus-color';
            }
            $name = zib_str_cut($term->name, 0, 8, '...');
            $href = get_term_link($term);
            $dropdown_but .= '<li><a ajax-replace="true" class="' . $_class . '" href="' . $href . '">' . $name . '</a></li>';
        }
    }
    if (!$but && !$dropdown_but) {
        return '';
    }

    $is_dropdown = ($option['dropdown'] && $dropdown_but) ? true : false;
    $d_but       = $is_dropdown ? '<a href="javascript:;" data-toggle="dropdown"><span name="cat">' . $text . '</span><i class="fa fa-fw fa-sort opacity5" aria-hidden="true"></i></a>' : $text;

    $html .= '<div class="flex ac">';
    $html .= '<div class="option-dropdown splitters-this-r dropdown flex0">';
    $html .= $d_but;
    $html .= $is_dropdown ? '<ul class="dropdown-menu">' . $dropdown_but . '</ul>' : '';
    $html .= '</div>';
    $html .= '<ul class="list-inline scroll-x mini-scrollbar option-items">' . $but . '</ul>';
    $html .= '</div>';

    if ($child_cat) {
        $html .= zib_get_option_terms_but(array('lists' => $child_cat), $link_class, $child_name);
    }
    return $html;
}

/**
 * @description: 根据分类或专题的内容以及文章的聚合模块
 * @param {*} $args
 * @param {*} $echo
 * @return {*}
 */
function zib_term_aggregation($args = array(), $echo = false)
{
    $defaults = array(
        'term_id'      => '',
        'class'        => '',
        'target_blank' => '',
        'taxonomy'     => '',
        'orderby'      => 'date',
        'count'        => 6,
    );
    $args = wp_parse_args((array) $args, $defaults);

    if (!$args['term_id']) {
        return;
    }

    $term = get_term($args['term_id'], $args['taxonomy']);
    if (!$term) {
        return '';
    }

    $default_img = '';
    if ($term->taxonomy == 'category') {
        $default_img = _pz('cat_default_cover');
        $icon        = '<i class="fa fa-folder-open-o mr6" aria-hidden="true"></i>';
        $but_name    = '分类';
    } elseif ($term->taxonomy == 'topics') {
        $default_img = _pz('topics_default_cover');
        $icon        = '<i class="fa fa-cube mr6" aria-hidden="true"></i>';
        $but_name    = '专题';
    }
    $img   = zib_get_taxonomy_img_url($term->term_id, null, $default_img);
    $count = zib_get_cat_postcount($term->term_id, $term->taxonomy);
    $href  = get_term_link($term);

    $views_count = get_term_posts_meta_count($term->term_id, 'views');
    $more        = '<badge class="img-badge px12">' . zib_get_svg('hot') . ' ' . $views_count . '</badge>';

    $img_graphic = array(
        'type'         => '',
        'class'        => '',
        'img'          => $img,
        'alt'          => $but_name . '-' . $term->name,
        'link'         => array(
            'url'    => $href,
            'target' => (!empty($args['target_blank']) ? '_blank' : ''),
        ),
        'lazy'         => true,
        'height_scale' => 70,
        'mask_opacity' => 0,
        'more'         => $more,
    );
    $img_html = zib_graphic_card($img_graphic);
    $img_html = '<div class="term-img flex0 em09-sm">' . $img_html . '</div>';

    $target_blank = !empty($args['target_blank']) ? ' target="_blank"' : '';
    $name         = '<a class="em14 key-color"' . $target_blank . ' href="' . $href . '">' . $term->name . '</a>';

    $description = $term->description;
    if (!$description && is_super_admin()) {
        $description = '请在Wordress后台-文章-文章' . $but_name . '中添加描述！' . zib_get_term_admin_edit('立即编辑', $term);
    }
    $description = '<div class="text-ellipsis-2 muted-color">' . $description . '</div>';

    $meta = '<sapn class="px12 muted-3-color"><i class="fa fa-file-text-o fa-fw"></i>' . $count . '篇文章</sapn>';
    $meta .= '<a class="but p2-10 px12 c-blue hide-m-s"' . $target_blank . ' href="' . $href . '"><i class="fa fa-angle-right"></i>更多文章</a>';
    $meta = '<div class="term-meta flex jsb ac">' . $meta . '</div>';

    //准备文章
    $posts_args = array(
        'showposts'           => $args['count'],
        'ignore_sticky_posts' => 1,
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'order'               => 'DESC',
        'tax_query'           => array(
            array(
                'taxonomy' => $term->taxonomy,
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ),
        ),
    );

    //文章排序
    $orderby = $args['orderby'];
    if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
        $posts_args['orderby'] = $orderby;
    } else {
        $posts_args['orderby']    = 'meta_value_num';
        $posts_args['meta_query'] = array(
            array(
                'key'   => $orderby,
                'order' => 'DESC',
            ),
        );
    }

    $posts_html = '';
    $new_query  = new WP_Query($posts_args);
    //  echo json_encode($new_query);
    while ($new_query->have_posts()) {
        $new_query->the_post();
        $title = get_the_title() . get_the_subtitle(false);
        $title = '<div class="text-ellipsis"><a class="icon-circle mln3" ' . $target_blank . ' href="' . get_permalink() . '">' . $title . '</a></div>';
        $_meta = '';
        if ($orderby == 'views') {
            $_meta = get_post_view_count();
        } elseif ($orderby == 'favorite') {
            $_meta = get_post_favorite_count();
        } elseif ($orderby == 'like') {
            $_meta = get_post_like_count();
        } elseif ($orderby == 'comment_count') {
            $_meta = get_post_comment_count();
        } elseif ($orderby == 'date') {
            $_meta = '<i class="fa fa-clock-o mr3" aria-hidden="true"></i>' . zib_get_time_ago(get_the_time('U'));
        }
        if (!$_meta) {
            $_meta = '<i class="fa fa-clock-o mr3" aria-hidden="true"></i>' . zib_get_time_ago(get_the_modified_time('U'));
        }

        $posts_meta = '<div class="em09 muted-3-color flex0 ml10">' . $_meta . '</div>';
        $posts_html .= '<div class="mt10 flex jsb ac">' . $title . $posts_meta . '</div>';
    }
    wp_reset_query();
    wp_reset_postdata();

    $term_html = '<div class="zib-widget term-aggregation">';
    $term_html .= '<div class="mb20 hover-zoom-img flex px12-sm px12-m-s">';
    $term_html .= $img_html;
    $term_html .= '<div class="term-title ml10 flex xx flex1 jsb">';
    $term_html .= $name;
    $term_html .= $description;
    $term_html .= $meta;
    $term_html .= '</div>';
    $term_html .= '</div>';
    $term_html .= $posts_html;
    $term_html .= '</div>';

    $html = '';
    $html .= $term_html;
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}
