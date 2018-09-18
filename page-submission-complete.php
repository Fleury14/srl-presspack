<?php get_header();
$current_user = wp_get_current_user();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['nick'])) {
	?><p>Changing name to.... <?php echo $_POST['nick']; ?></p><?php
	wp_update_user( array(
		'ID' => $current_user->ID,
		'display_name' => $_POST['nick']
	) );
}
?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>

			<h1><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
				<?php the_content(); ?>

				<?php edit_post_link(); ?>

			</article>
			<!-- /article -->

		<?php endwhile; ?>

		<?php else: ?>

			<!-- article -->
			<article>

				<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>

			</article>
			<!-- /article -->

		<?php endif; ?>

		</section>
		<!-- /section -->
	</main>
    <script>
    setTimeout(() => {window.location.replace('/user-info')}, 2000);
    </script>
<?php get_footer(); ?>