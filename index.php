<?php get_header(); ?>
	<div id="book_title">
		<h1><?php bloginfo('name'); ?></h1>
		<div class="description"><?php bloginfo('description'); ?></div>
	</div>
	<div id="bookmark" style="display:none;"></div>

<?php
if(get_option("book_wp_front_blurb")!=""){
	echo "<div id='front_blurb'>";
	echo nl2br(stripslashes(get_option("book_wp_front_blurb")));
	echo "</div>";
}
?>
<div id='table_of_contents_header'>Table of Contents</div>
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
