<?php
/**
 * lmcn template for displaying the footer
 *
 * @package WordPress
 * @subpackage lmcn
 * @since lmcn 1.0
 */
?>

				<ul class="footer-widgets"><?php
					if ( function_exists( 'dynamic_sidebar' ) ) :
						dynamic_sidebar( 'footer-sidebar' );
					endif; ?>
				</ul>

			</div>
		<?php wp_footer(); ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<?php
			if ( is_home() ):
		?>
			<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.cycle.all.min.js";
		<?php 
			endif;
		?>
		<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.cookie.js";
		<?php if ( is_home() && !get_option('ss_disable') ) : ?>
        <script type="text/javascript">
            (function(jQuery) {
            	alert()
                jQuery(function() {
                    jQuery('#slideshow').cycle({
                        fx:     'scrollHorz',
                        timeout: <?php echo (get_option('ss_timeout')) ? get_option('ss_timeout') : '7000' ?>,
                        next:   '#rarr',
                        prev:   '#larr'
                    });
                })
            })(jQuery);
        </script>
        <?php endif; ?>
	</body>
</html>