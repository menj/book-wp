		<footer id="footer" class="source-org vcard copyright">
			<small><?php
				if (get_option("book_wp_copyright")!=""){
					echo get_option("book_wp_copyright")." <br/> ";
				}
			?><a href='http://www.storyhack.com/book-wp'>Book WP Theme</a> is by <a href='http://www.bryceabeattie.com'>Bryce Beattie</a></small>
		</footer>

	</div>

	<?php wp_footer(); ?>


<!-- here comes the javascript -->

<!-- jQuery is called via the Wordpress-friendly way via functions.php -->

<!-- this is where we put our custom functions -->
<script src="<?php bloginfo('template_directory'); ?>/_/js/jquery.cookie.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/_/js/functions.js"></script>

<?php

if(get_option("book_wp_analytics")!= ""){
?>
<!-- Asynchronous google analytics; this is the official snippet.
	 Replace UA-XXXXXX-XX with your site's ID and uncomment to enable.
	 -->
<script>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo get_option("book_wp_analytics");?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php
} ?>
	
</body>

</html>
