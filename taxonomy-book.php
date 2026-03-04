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

$book_term = get_queried_object();
$cover_id  = $book_term ? get_term_meta( $book_term->term_id, 'book_cover', true ) : 0;
$book_desc = $book_term ? get_term_meta( $book_term->term_id, 'book_description', true ) : '';
$pub_year  = $book_term ? (int) get_term_meta( $book_term->term_id, 'publication_year', true ) : 0;
$chapters  = $book_term ? bookwp_get_pro_chapter_posts( $book_term->term_id ) : array();
?>

<main id="main-content" class="site-main" role="main" tabindex="-1">
	<div class="content-area-wide">

		<header class="archive-header book-archive-header">
			<h1>
				<?php
				if ( $book_term ) {
					echo esc_html( $book_term->name );
				} else {
					esc_html_e( 'Book', 'book-wp' );
				}
				?>
			</h1>

			<?php if ( $pub_year ) : ?>
				<p class="book-meta">
					<?php
					printf(
						/* translators: %d: publication year */
						esc_html__( 'Published %d', 'book-wp' ),
						intval( $pub_year )
					);
					?>
				</p>
			<?php endif; ?>

			<?php if ( $cover_id ) : ?>
				<div class="book-cover">
					<?php
					echo wp_get_attachment_image(
						$cover_id,
						'medium',
						false,
						array(
							// alt from attachment; empty if decorative.
							'alt' => esc_attr( get_post_meta( $cover_id, '_wp_attachment_image_alt', true ) ),
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( $book_desc ) : ?>
				<div class="book-description-full">
					<?php echo wp_kses_post( $book_desc ); ?>
				</div>
			<?php endif; ?>
		</header><!-- .archive-header -->

		<?php if ( ! empty( $chapters ) ) : ?>
			<nav class="toc-container" aria-label="<?php esc_attr_e( 'Table of Contents', 'book-wp' ); ?>">
				<h2 class="toc-heading" id="toc-heading"><?php esc_html_e( 'Table of Contents', 'book-wp' ); ?></h2>
				<ol class="toc-list" aria-labelledby="toc-heading">
					<?php foreach ( $chapters as $chapter ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $chapter->ID ) ); ?>">
								<?php echo esc_html( get_the_title( $chapter->ID ) ); ?>
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
