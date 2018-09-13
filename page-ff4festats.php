<?php get_header();
    $player_name = $_GET['player'] ? $_GET['player'] : 'Fleury14';
    // Grab all race data
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://api.speedrunslive.com/pastraces?game=ff4hacks&season=0&page=1&pageSize=5000',
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        )  
    ));
    $info = curl_exec($curl);
    curl_close($curl);
    $past_races = json_decode($info);

    // get general game info
    $general_curl = curl_init();
    curl_setopt_array($general_curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://api.speedrunslive.com/stat?game=ff4hacks&season=0',
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        )
    ));
    $general_info = curl_exec($general_curl);
    curl_close($general_curl);
    $ff4_info = json_decode($general_info);

    
    // throw all league flag races into its respective array
    $league_qual_races = array();
    $league_ro32_races = array();
    $league_ro16_races = array();
    $community_races = array();
    $total_races = $past_races->count;

    foreach ($past_races->pastraces as $race) {
        if ( strpos($race->goal, 'J2KC2T4S3BF2NE3$X2Y2GWZ') !== false ) { array_push($league_qual_races, $race); }
        if ( strpos($race->goal, 'JK2PCT3S2BF2NE3X2Y2GZ') !== false || strpos($race->goal, 'HTTZZ League Match') !== false ) { array_push($league_ro32_races, $race); }
        if ( strpos($race->goal, 'JK2PC3T3S2BF2NE3X2Y2GZ') !== false ) { array_push($league_ro16_races, $race); }
        if ( strpos($race->goal, 'Community Race') !== false ) { array_push($community_races, $race); }
    }

    function winningTimeCmp($race1, $race2) {
        $race1Time = null;
        $race2Time = null;
        foreach ($race1->results as $result) {
            if ($result->place === 1) { $race1Time = $result->time; }
        }
        foreach ($race2->results as $result) {
            if ($result->place === 1) { $race2Time = $result->time; }
        }
        if ($race1Time === -1) { $race1Time = 99999; }
        if ($race2Time === -1) { $race2Time = 99999; }
        return $race1Time - $race2Time;
    }

    function dateCmp($race1, $race2) {
        return $race1->date - $race2->date;
    }
    usort($league_qual_races, "winningTimeCmp");
    usort($league_ro32_races, "winningTimeCmp");
    usort($league_ro16_races, "winningTimeCmp");
    usort($community_races, "dateCmp");
    $recent_community_race = $community_races[count($community_races) - 1];
    zScore($recent_community_race);
    // var_dump($league_qual_races);

?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>

			<h1 class="text-center"><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <p class="opening-stats">Popularity on SRL: <span class="audiowide">#<?php echo $ff4_info->game->popularityrank; ?><span></p>
                            <p class="opening-stats">Largest Race: <span class="audiowide"><?php echo $ff4_info->stats->largestRaceSize; ?></span></p>
                        </div>
                        <div class="col-md-6 text-center">
                            <p class="opening-stats">Total # of races: <span class="audiowide"><?php echo $ff4_info->stats->totalRaces; ?></span></p>
                            <p class="opening-stats">Total # of racers: <span class="audiowide"><?php echo $ff4_info->stats->totalPlayers; ?></span></p>
                        </div>
                    </div>
                </div>
                <h2>Community Races</h2>
                <p>Total Community Races: <?php echo count($community_races); ?></p>
                <p>Most recent race</p>
                <div class="container community-container">
                    <div class="row">
                        <div class="col-md-12 community-header d-flex justify-content-center align-items-center p-2">
                            <p class="m-0">Goal: <?php echo $recent_community_race->goal; ?></p>
                        </div>
                    </div>
                    <div class="row p-3">
                        <?php for($i = 0; $i < $recent_community_race->numentrants; $i++): ?>
                        <div class="col-md-4 community-results">
                            <p class="m-0 p-2"><?php echo $recent_community_race->results[$i]->player ?>: <?php
                                $hours = floor($recent_community_race->results[$i]->time / 3600);
                                $minutes = floor($recent_community_race->results[$i]->time / 60 % 60);
                                $seconds = floor($recent_community_race->results[$i]->time % 60);
                                echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
                                <p class="z-score m-0 p-0">Rank: <?php echo $i + 1; ?> Z-S: <?php echo number_format($recent_community_race->results[$i]->zScore, 3, '.', ','); ?></p>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
            
                <h2 class="text-center mt-5">Fastest Winning times</h2>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3 mini-table">
                            <p class="mini-table-header text-center">League Qualifiers</p>
                            <?php
                            for ($i = 0; $i < 10; $i++):
                            ?>
                            <div class="mini-table-row d-flex justify-content-between">
                                <p><?php echo ($league_qual_races[$i]->results[0]->player); ?></p>
                                <p><?php
                                $hours = floor($league_qual_races[$i]->results[0]->time / 3600);
                                $minutes = floor($league_qual_races[$i]->results[0]->time / 60 % 60);
                                $seconds = floor($league_qual_races[$i]->results[0]->time % 60);
                                echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <div class="col-sm-3 mini-table">
                            <p class="mini-table-header text-center">League Ro.32</p>
                            <?php
                            for ($i = 0; $i < 10; $i++):
                            ?>
                            <div class="mini-table-row d-flex justify-content-between">
                                <p><?php echo ($league_ro32_races[$i]->results[0]->player); ?></p>
                                <p><?php
                                $hours = floor($league_ro32_races[$i]->results[0]->time / 3600);
                                $minutes = floor($league_ro32_races[$i]->results[0]->time / 60 % 60);
                                $seconds = floor($league_ro32_races[$i]->results[0]->time % 60);
                                echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <div class="col-sm-3 mini-table">
                            <p class="mini-table-header text-center">League Ro.16</p>
                            <?php
                            for ($i = 0; $i < 10; $i++):
                            ?>
                            <div class="mini-table-row d-flex justify-content-between">
                                <p><?php echo ($league_ro16_races[$i]->results[0]->player); ?></p>
                                <p><?php
                                $hours = floor($league_ro16_races[$i]->results[0]->time / 3600);
                                $minutes = floor($league_ro16_races[$i]->results[0]->time / 60 % 60);
                                $seconds = floor($league_ro16_races[$i]->results[0]->time % 60);
                                echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
	</main>
<?php get_footer(); ?>