<?php
/*
 * @Author: 刀 客 源 码 网
 * @Url: www.dkewl.com
 * @Date: 2020-12-17 21:45:28
 * @LastEditTime: 2022-04-12 17:01:20
 */

get_header();

?>

<?php if (function_exists('dynamic_sidebar')) {
	echo '<div class="container fluid-widget">';
	dynamic_sidebar('all_top_fluid');
	dynamic_sidebar('home_top_fluid');
	echo '</div>';
}
?>
<main role="main" class="container">
	<?php
	$paged = zib_get_the_paged();
	?>
	<div class="content-wrap">
		<div class="content-layout">
			<?php
			if (function_exists('dynamic_sidebar')) {
				dynamic_sidebar('home_top_content');
			}
			$index_tab_nav = zib_index_tab_html();
			if ($index_tab_nav) {
			?>
				<div class="home-tab-content">
					<?php echo $index_tab_nav; ?>
					<div class="tab-content">
						<div class="posts-row ajaxpager tab-pane fade in active" id="index-tab-main">
							<?php
							zib_ajax_option_menu('home');
							zib_posts_list();
							zib_paging();
							?>
						</div>
						<?php if (1 == $paged) {
							echo zib_index_tab('content');
						}
						?>
					</div>
				</div>
			<?php } ?>
			<?php if (function_exists('dynamic_sidebar')) {
				dynamic_sidebar('home_bottom_content');
			}
			?>
		</div>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php if (function_exists('dynamic_sidebar')) {
	echo '<div class="container fluid-widget">';
	dynamic_sidebar('home_bottom_fluid');
	dynamic_sidebar('all_bottom_fluid');
	echo '</div>';
}
?>
<?php get_footer();