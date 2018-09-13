<?php get_header();
    
    // get leaderboard info
    $leaderboard_curl = curl_init();
    curl_setopt_array($leaderboard_curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://api.speedrunslive.com/leaderboard/ff4hacks?season=0',
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        )  
    ));

    function placeCmp($leader1, $leader2) {
        return $leader1->rank - $leader2->rank;
    }

    $leaderboard_info = curl_exec($leaderboard_curl);
    curl_close($leaderboard_curl);
    $leaderboard = json_decode($leaderboard_info);
    $total_players = $leaderboard->leadersCount;
    $player_list = $leaderboard->leaders;
    usort($player_list, "placeCmp");

    // create index tiers
    $bronze_place_end = $total_players;
    $bronze_place_start = $total_players - ( floor($total_players * .45) ); // bronze = bottom 45%
    $silver_place_end = $bronze_place_start - 1;
    $silver_place_start = $total_players - ( floor($total_players * .72) ); // silver = from 45% -> 72%
    $gold_place_end = $silver_place_start - 1;
    $gold_place_start = $total_players - ( floor($total_players * .875) ); // gold = 72% -> 87.5;
    $master_place_end = $gold_place_start - 1;
    $master_place_start = $total_players - ( floor($total_players * .935) ); // master = 87.5% -> 93.5%;
    $gm_place_end = $master_place_start - 1;
    $gm_place_start = $total_players - ( floor($total_players * .97) ); // GM = 93.5% -> 97%;
    $lunarian_place_start = 2;
    $lunarian_place_end = $gm_place_start - 1;

    
    
?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>

			<h1 class="text-center"><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
                <div class="champion text-center">
                    <h2 class="press-start text-uppercase">Champion</h2>
                    <h3><?php echo $player_list[0]->name; ?></h3>
                    <h4><?php echo floor($player_list[0]->trueskill); ?></h4>
                </div>

                <div class="container">
                    <div class="row lunarian">
                        <h2 class="text-center w-100 mb-2 press-start text-uppercase">Lunarians</h2>
                        <?php for ($i = $lunarian_place_start; $i <= $lunarian_place_end; $i++ ): ?>
                        <div class="col-md-4 col-sm-6">
                            <h4 class="text-center"><?php echo $player_list[$i - 1]->name ?></h4>
                            <h5 class="text-center press-start"><?php echo floor($player_list[$i - 1]->trueskill); ?></h5>
                        </div>
                        <?php endfor; ?>
                    </div> <!-- end row -->
                    <div class="row grand-master">
                        <h2 class="text-center w-100 mb-2 press-start text-uppercase">Grand Masters</h2>
                        <?php for ($i = $gm_place_start; $i <= $gm_place_end; $i++ ): ?>
                        <div class="col-md-2 col-sm-4">
                            <h5 class="text-center"><?php echo $player_list[$i - 1]->name ?></h5>
                            <h6 class="text-center press-start"><?php echo floor($player_list[$i - 1]->trueskill); ?></h6>
                        </div>
                        <?php endfor; ?>
                    </div> <!-- end gm row -->
                    <div class="row master">
                        <h2 class="text-center w-100 mb-2 press-start text-uppercase">Masters</h2>
                        <?php for ($i = $master_place_start; $i <= $master_place_end; $i++ ): ?>
                        <div class="col-md-2 col-sm-4">
                            <h6 class="text-center"><?php echo $player_list[$i - 1]->name ?></h6>
                            <p class="text-center press-start"><?php echo floor($player_list[$i - 1]->trueskill); ?></p>
                        </div>
                        <?php endfor; ?>
                    </div> <!-- end master row -->
                    <div class="row gold">
                        <h2 class="text-center w-100 mb-2 press-start text-uppercase">Gold</h2>
                        <?php for ($i = $gold_place_start; $i <= $gold_place_end; $i++ ): ?>
                        <div class="col-md-3 col-sm-4 d-flex align-items-center">
                            <p class="text-center mr-3">#<?php echo $i; ?> <?php echo $player_list[$i - 1]->name ?>:</p>
                            <p class="text-center press-start"><?php echo floor($player_list[$i - 1]->trueskill); ?></p>
                        </div>
                        <?php endfor; ?>
                    </div> <!-- end gold row -->
                    <div class="row silver">
                        <h2 class="text-center w-100 mb-2 press-start text-uppercase">Silver</h2>
                        <?php for ($i = $silver_place_start; $i <= $silver_place_end; $i++ ): ?>
                        <div class="col-md-3 col-sm-4 d-flex align-items-center">
                            <p class="text-center mr-3">#<?php echo $i; ?> <?php echo $player_list[$i - 1]->name ?>:</p>
                            <p class="text-center press-start"><?php echo floor($player_list[$i - 1]->trueskill); ?></p>
                        </div>
                        <?php endfor; ?>
                    </div> <!-- end silver row -->
                    <div class="row bronze">
                        <h2 class="text-center w-100 mb-2 press-start text-uppercase">Bronze</h2>
                        <?php for ($i = $bronze_place_start; $i <= $bronze_place_end; $i++ ): ?>
                        <div class="col-md-3 col-sm-4 d-flex align-items-center">
                            <p class="text-center mr-3">#<?php echo $i; ?> <?php echo $player_list[$i - 1]->name ?>:</p>
                            <p class="text-center press-start"><?php echo floor($player_list[$i - 1]->trueskill); ?></p>
                        </div>
                        <?php endfor; ?>
                    </div> <!-- end bronze row -->
                </div> <!-- end container -->

                

                <?php edit_post_link();
                 
                
                // echo count($league_qual_races);
                // var_dump($league_qual_races[1]);
                ?>

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
	</main>
<?php get_footer(); ?>