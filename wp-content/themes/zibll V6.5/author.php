<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2021-11-02 12:44:51
 */
get_header();
?>

<main>
	<div class="container">
		<?php
		zib_author_header();
		if (function_exists('dynamic_sidebar')) {
			echo '<div class="fluid-widget">';
			dynamic_sidebar('all_top_fluid');
			dynamic_sidebar('author_top_fluid');
			echo '</div>';
		}
		zib_author_content();
		?>
	</div>
	<?php if (function_exists('dynamic_sidebar')) {
		echo '<div class="container fluid-widget">';
		dynamic_sidebar('author_bottom_fluid');
		dynamic_sidebar('all_bottom_fluid');
		echo '</div>';
	}
	?>
</main>
<?php get_footer(); ?>