<?php get_header(); ?>
	<section id="book_title">
		<h1><?php bloginfo('name'); ?></h1>
		<section class="description"><?php bloginfo('description'); ?></section>
	</section>
	<section id="bookmark" style="display:none;"></section>

<?php
if(get_option("book_wp_front_blurb")!=""){
	echo "<section id='front_blurb'>";
	echo nl2br(stripslashes(get_option("book_wp_front_blurb")));
	echo "</section>";
}
?>
<section id='table_of_contents_header'>Table of Contents</section>
<ul id='table_of_contents'>
<?php
$debut = 0;
$myposts = get_posts('numberposts=-1&offset=$debut&orderby=data&order=ASC');
foreach($myposts as $post) :
?>
	<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
<?php endforeach; ?>
</ul>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
