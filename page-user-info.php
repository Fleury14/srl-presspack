<?php get_header();

$current_user = wp_get_current_user();

// get user info based on display name
$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://api.speedrunslive.com/stat?player=' . $current_user->display_name . '&game=ff4hacks&page=1',
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json"
		)  
	));
	$info = curl_exec($curl);
	curl_close($curl);
	$overall_stats = json_decode($info);

?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>

			<h1><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
				<?php the_content(); ?>

				<p>User name: <?php echo $current_user->user_login; ?></p>
				<p>Email: <?php echo $current_user->user_email; ?></p>
				<p>Display Name: <?php echo $current_user->display_name; ?></p>
				
				<?php if ($overall_stats->errorCode == 404): ?>
				<p>There is no SRL data for your nickname.</p>
				<?php endif; ?>
				<?php if ($overall_stats->stats->rank !== null): ?>
				<p>Rank: <?php echo $overall_stats->stats->rank; ?></p>
				<?php endif; ?>
				<?php if ($overall_stats->player->channel !==  null && $overall_stats->player->api == 'twitch'): ?>
				<a href="http://twitch.tv/<?php echo $overall_stats->player->channel; ?>" target="_blank"><button class="twitch-button mt-2 mb-2">Twitch Channel</button></a>
				<a href="/statpage/?player=<?php echo $current_user->display_name ?>"><button class="btn btn-primary">View My Stats</button></a>
				<?php endif; ?>
				<form method="POST" action="\submission-complete">
					<label for="nick">New Nickname</label>
					<input name="nick" type="text">
					<button type="submit">Change Name</button>
				</form>
				
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
<?php get_footer(); ?>