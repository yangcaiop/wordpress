<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2021-07-25 14:09:00
 */
if (is_tax('topics')) {
	get_template_part('template/category-topics');
	return;
}
get_header();
$pagedtext = '';
if ($paged && $paged > 1) {
	$pagedtext = ' <small>第' . $paged . '页</small>';
}
?>
<main role="main" class="container">
	<div class="content-wrap">
		<div class="content-layout">
			<div class="main-bg text-center box-body radius8 main-shadow theme-box">
				<h4 class="title-h-center">
					<?php
					if (is_day()) echo the_time('Y年m月j日');
					elseif (is_month()) echo the_time('Y年m月');
					elseif (is_year()) echo the_time('Y年');
					?>的文章<small class="ml10"><?php echo $pagedtext ?></small>
				</h4>
			</div>
			<?php
			echo '<div class="posts-row ajaxpager">';
			zib_posts_list();
			zib_paging();
			echo '</div>';
			?>
		</div>
	</div>
	<?php get_sidebar(); ?>
</main>

<?php get_footer(); ?>