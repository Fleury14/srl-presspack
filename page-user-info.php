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

			<h1 class="text-center"><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
				<div class="info-container">
					<div class="info-row">
						<p><strong>User name:</strong></p>
						<p><?php echo $current_user->user_login; ?></p>
					</div>
					<div class="info-row">
						<p><strong>Email:</strong></p>
						<p><?php echo $current_user->user_email; ?></p>
					</div>
					<div class="info-row">
						<p><strong>Display Name:</strong></p>
						<p><?php echo $current_user->display_name; ?></p>
					</div>
					<?php if (empty($overall_stats)): ?>
					<p class="alert-danger p-2">There is no SRL data for your nickname.</p>
					<?php endif; ?>
					<?php if ($overall_stats->stats->rank !== null): ?>
					<div class="info-row">
						<p><strong>Rank:</strong><span class="audiowide ml-3"></p><p><?php echo $overall_stats->stats->rank; ?></span></p>
					</div>
					<?php endif; ?>
					<div class="mb-3">
						<?php if ($overall_stats->player->channel !==  null && $overall_stats->player->api == 'twitch'): ?>
						<a href="http://twitch.tv/<?php echo $overall_stats->player->channel; ?>" target="_blank"><button class="btn twitch-button mr-3">Twitch Channel</button></a>
						<a href="/statpage/?player=<?php echo $current_user->display_name ?>"><button class="btn btn-primary">View My Stats</button></a>
						<?php endif; ?>
					</div>
					<div class="hr hr-primary"></div>
					<form method="POST" class="mt-4" action="\submission-complete">
						<h3 for="nick">Change Your Nickname?</h3>
						<div class="d-flex mt-3">
							<input name="nick" type="text" class="mr-4">
							<button type="submit" class="btn btn-primary">Change Name</button>
						</div>
						
					</form>
				</div>
				
				
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