<?php
/**
 * Footer template.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	<footer id="footer" class="site-footer" role="contentinfo">
		<div class="site-footer-inner">
			<?php
			$copyright = bookwp_get_option( 'copyright' );
			if ( $copyright ) :
				?>
				<p class="footer-copyright"><?php echo esc_html( $copyright ); ?></p>
				<?php
			endif;
			?>
			<p class="footer-credit">
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: WordPress link */
						__( 'Powered by %s', 'book-wp' ),
						'<a href="' . esc_url( 'https://wordpress.org/' ) . '" rel="noopener noreferrer">WordPress</a>'
					),
					array(
						'a' => array(
							'href' => array(),
							'rel'  => array(),
						),
					)
				);
				?>
			</p>
		</div><!-- .site-footer-inner -->
	</footer><!-- #footer -->

</div><!-- #page-wrap -->

<?php wp_footer(); ?>
</body>
</html>
