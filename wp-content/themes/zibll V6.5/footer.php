<?php
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-28 00:05:06
 * @LastEditTime: 2022-04-16 18:04:57
 */
?>
<footer class="footer">

	<?php if (function_exists('dynamic_sidebar')) {
    dynamic_sidebar('all_footer');
}?>
	<div class="container-fluid container-footer">
		<?php do_action('zib_footer_conter');?>
	</div>
</footer>
<?php
wp_footer();
?>
</body>
</html>