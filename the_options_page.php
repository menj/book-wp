<?php
if (isset($_POST["update_settings"])) {
    // Do the saving
	update_option("book_wp_font",$_POST['book_wp_font']);
	update_option("book_wp_color",$_POST['book_wp_color']);
	update_option("book_wp_comments",$_POST['book_wp_comments']);
	update_option("book_wp_front_blurb",stripslashes($_POST['book_wp_front_blurb']));
	update_option("book_wp_chapter_blurb",stripslashes($_POST['book_wp_chapter_blurb']));
	update_option("book_wp_copyright",stripslashes($_POST['book_wp_copyright']));

	echo '<div id="message" class="updated">Settings saved</div>';
}
?>

 <div class="wrap">
	<?php screen_icon('themes'); ?> <h2>Book WP Options</h2>
	<form method="POST" action="">
		<table class="form-table">
			
		</table>
		<p>
			<input type="hidden" name="update_settings" value="Y" />
			<input type="submit" value="Save settings" class="button-primary"/>
		</p>
	</form>
</div>
