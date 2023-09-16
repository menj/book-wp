<?php get_header();

 ?>	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<script type='text/javascript'>
				var page_title = "<?php the_title(); ?>";
			</script>	
			<h1 class="entry-title chapter_title"><?php the_title(); ?></h1>

			<section class="entry-content">
				
				<?php the_content(); ?>

				<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
				
				

			</section>
			<?php next_post_link('<br/><br/><section id="next_chapter">Next Chapter: %link</section>'); ?>

			<?php edit_post_link('Edit this entry','<section style="text-align:center">','</section>'); ?>
			
		</article>

	<?php 
	if(get_option("book_wp_comments")=="show"){
		comments_template(); 
	}
	
	?>

	<?php endwhile; endif; ?>
<script type='text/javascript'>
	page_type = 'single';
</script>	
<?php get_sidebar(); ?>

<?php get_footer(); ?>