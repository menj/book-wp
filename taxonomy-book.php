<?php
/**
 * Book taxonomy archive template (Pro Mode).
 * Lists chapters for a specific book term.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bookwp_book_term = get_queried_object();
$bookwp_cover_id  = $bookwp_book_term ? get_term_meta( $bookwp_book_term->term_id, 'book_cover', true ) : 0;
$bookwp_book_desc = $bookwp_book_term ? get_term_meta( $bookwp_book_term->term_id, 'book_description', true ) : '';
$bookwp_pub_year  = $bookwp_book_term ? (int) get_term_meta( $bookwp_book_term->term_id, 'publication_year', true ) : 0;
$bookwp_chapters  = $bookwp_book_term ? bookwp_get_pro_chapter_posts( $bookwp_book_term->term_id ) : array();
?>

<main id="main-content" class="site-main" role="main" tabindex="-1">
	<div class="content-area-wide">

		<header class="archive-header book-archive-header">
			<h1>
				<?php
				if ( $bookwp_book_term ) {
					echo esc_html( $bookwp_book_term->name );
				} else {
					esc_html_e( 'Book', 'book-wp' );
				}
				?>
			</h1>

			<?php if ( $bookwp_pub_year ) : ?>
				<p class="book-meta">
					<?php
					printf(
						/* translators: %d: publication year */
						esc_html__( 'Published %d', 'book-wp' ),
						intval( $bookwp_pub_year )
					);
					?>
				</p>
			<?php endif; ?>

			<?php if ( $bookwp_cover_id ) : ?>
				<div class="book-cover">
					<?php
					// Alt from the Media Library, falling back to a description of the cover.
					$bookwp_cover_alt = trim( (string) get_post_meta( $bookwp_cover_id, '_wp_attachment_image_alt', true ) );
					if ( '' === $bookwp_cover_alt ) {
						/* translators: %s: book title */
						$bookwp_cover_alt = sprintf( __( 'Cover of %s', 'book-wp' ), $bookwp_book_term->name );
					}
					echo wp_get_attachment_image(
						$bookwp_cover_id,
						'medium',
						false,
						array(
							'alt' => $bookwp_cover_alt,
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( $bookwp_book_desc ) : ?>
				<div class="book-description-full">
					<?php echo wp_kses_post( $bookwp_book_desc ); ?>
				</div>
			<?php endif; ?>
		</header><!-- .archive-header -->

		<?php if ( ! empty( $bookwp_chapters ) ) : ?>
			<nav class="toc-container" aria-label="<?php esc_attr_e( 'Table of Contents', 'book-wp' ); ?>">
				<h2 class="toc-heading" id="toc-heading"><?php esc_html_e( 'Table of Contents', 'book-wp' ); ?></h2>
				<ol class="toc-list" aria-labelledby="toc-heading">
					<?php foreach ( $bookwp_chapters as $bookwp_chapter ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $bookwp_chapter->ID ) ); ?>">
								<?php echo esc_html( get_the_title( $bookwp_chapter->ID ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php else : ?>
			<p class="no-results-message"><?php esc_html_e( 'No chapters found for this book.', 'book-wp' ); ?></p>
		<?php endif; ?>

	</div><!-- .content-area-wide -->
</main><!-- #main-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
