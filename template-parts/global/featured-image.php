<?php
/**
 * Featured image template part.
 *
 * Shown on single posts and chapters. Alt text comes from the Media Library;
 * when none is set the post title is used, so the image is never unlabelled.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! has_post_thumbnail() || post_password_required() ) {
	return;
}

$bookwp_thumb_id = get_post_thumbnail_id();
$bookwp_alt      = trim( (string) get_post_meta( $bookwp_thumb_id, '_wp_attachment_image_alt', true ) );
?>
<figure class="entry-featured-image">
	<?php
	the_post_thumbnail(
		'large',
		array(
			'alt'           => $bookwp_alt ? $bookwp_alt : the_title_attribute( array( 'echo' => false ) ),
			'loading'       => false,
			'fetchpriority' => 'high',
		)
	);
	?>
</figure>
