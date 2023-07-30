<?php
if (isset($_POST["update_settings"])) {
    // Do the saving
	update_option("book_wp_font",$_POST['book_wp_font']);
	update_option("book_wp_color",$_POST['book_wp_color']);
	update_option("book_wp_comments",$_POST['book_wp_comments']);
	update_option("book_wp_front_blurb",stripslashes($_POST['book_wp_front_blurb']));
	update_option("book_wp_chapter_blurb",stripslashes($_POST['book_wp_chapter_blurb']));
	update_option("book_wp_analytics",$_POST['book_wp_analytics']);
	update_option("book_wp_copyright",stripslashes($_POST['book_wp_copyright']));

	echo '<div id="message" class="updated">Settings saved</div>';
}
?>

 <div class="wrap">
	<?php screen_icon('themes'); ?> <h2>Book WP Options</h2>
	<form method="POST" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_font">
						Book Font
					</label>
				</th>
				<td>
					<select name='book_wp_font'>
						<option value='georgia'<?php echo "", ( get_option('book_wp_font')=='georgia' ? ' selected' : ''); ?>>Georgia</option>
						<option value='vollkorn'<?php echo "", ( get_option('book_wp_font')=='vollkorn' ? ' selected' : ''); ?>>Vollkorn</option>
						<option value='arvo'<?php echo "", ( get_option('book_wp_font')=='arvo' ? ' selected' : ''); ?>>Arvo</option>
						<option value='maven'<?php echo "", ( get_option('book_wp_font')=='maven' ? ' selected' : ''); ?>>Maven Pro</option>
						<option value='ubuntu'<?php echo "", ( get_option('book_wp_font')=='ubuntu' ? ' selected' : ''); ?>>Ubuntu</option>
					</select>
					<ul>
						<li><em>Georgia is a standard websafe font.</em></li>
						<li><em>The others are google-hosted fonts and will increase the loading time of your site by a little bit, but are also a little fancier.</em></li>
					</ul>
					
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_color">
						Color Scheme
					</label>
				</th>
				<td>
					<select name='book_wp_color'>
						<option value='b_n_w'<?php echo "", ( get_option('book_wp_color')=='b_n_w' ? ' selected' : ''); ?>>Black and White</option>
						<option value='c_n_b'<?php echo "", ( get_option('book_wp_color')=='c_n_b' ? ' selected' : ''); ?>>Cream and Brown</option>
					</select>					
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_comments">
						Comment Section
					</label>
				</th>
				<td>
					<select name='book_wp_comments'>
						<option value='show'<?php echo "", ( get_option('book_wp_comments')=='show' ? ' selected' : ''); ?>>show</option>
						<option value='hide'<?php echo "", ( get_option('book_wp_comments')=='hide' ? ' selected' : ''); ?>>hide</option>
					</select>					
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_front_blurb">Front Page Blurb</label>
				</th>
				<td>
					<textarea name='book_wp_front_blurb' rows='6' cols='50'><?php echo get_option('book_wp_front_blurb');?></textarea>
					<p><em>This text will be inserted onto the table of contents page before the actual table of contents.</em></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_chapter_blurb">Chapter Blurb</label>
				</th>
				<td>
					<textarea name='book_wp_chapter_blurb' rows='6' cols='50'><?php echo get_option('book_wp_chapter_blurb');?></textarea>
					<p><em>This text will go above the chapter title on each post (chapter)</em></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_copyright">Copyright</label>
				</th>
				<td>
					<input type='text' name='book_wp_copyright' size='40' value='<?php echo get_option('book_wp_copyright');?>'>
					<p><em>The copyright message that goes at the very bottom of every page. If you want to insert a copyright symbol, type in &amp;copy;</em></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="book_wp_analytics">Google Analytics Account ID</label>
				</th>
				<td>
					<input type='text' name='book_wp_analytics' size='25' value="<?php echo get_option('book_wp_analytics');?>"/>
					<p><em>This is usually in the format</em> UA-XXXXX-Y</p>
					<p><em>If you don't want this theme to insert the google analytics code, just leave this blank.</em></p>
				</td>
			</tr>
		</table>
		<p>
			<input type="hidden" name="update_settings" value="Y" />
			<input type="submit" value="Save settings" class="button-primary"/>
		</p>
	</form>
</div>