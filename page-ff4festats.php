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

    function placeCmp($result1, $result2) {
        return $result1->place - $result2->place;
    }

    usort($league_qual_races, "winningTimeCmp");
    usort($league_ro32_races, "winningTimeCmp");
    usort($league_ro16_races, "winningTimeCmp");
    usort($community_races, "dateCmp");
    $recent_community_race = $community_races[count($community_races) - 1];
    zScore($recent_community_race);
    // var_dump($league_qual_races);
    usort($recent_community_race->results, "placeCmp");

?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>

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
                <h2 class="press-start text-uppercase text-center mt-5">Community Races</h2>
                <p class="text-center"><?php echo count($community_races); ?> Community Races on record</p>
                <p class="press-start text-center text-uppercase">Recent race summary</p>
                <div class="container community-container">
                    <div class="row">
                        <div class="col-md-12 community-header d-flex justify-content-center align-items-center p-2">
                            <p class="m-0">Goal: <?php echo $recent_community_race->goal; ?></p>
                        </div>
                    </div>
                    <table class="table-sm table-striped w-100 ">
                        <thead>
                            <tr>
                                <th scope="col">Rank</th>
                                <th scope="col">Racer</th>
                                <th scope="col">Time</th>
                                <th scope="col">Z-Score</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php for ($place = 1; $place <= $recent_community_race->numentrants; $place++ ): ?>
                            <tr>
                                <th scope="row" class="press-start"><?php echo $place; ?></th>
                                <td class="audiowide">
                                
                                    <?php echo $recent_community_race->results[$place - 1]->player;  ?>
                                </td>
                                <td class="press-start<?php echo $recent_community_race->results[$place - 1]->time === -1 ? ' negative-change' : ''; ?>">
                                    <?php
                                        if ($recent_community_race->results[$place - 1]->time !== -1) { 
                                            $hours = floor($recent_community_race->results[$place - 1]->time/ 3600);
                                            $minutes = floor($recent_community_race->results[$place - 1]->time / 60 % 60);
                                            $seconds = floor($recent_community_race->results[$place - 1]->time % 60);
                                            echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                        } else { echo 'Forfeit'; }
                                      ?>
                                </td>
                                <td class="press-start<?php
                                    if ($recent_community_race->results[$place - 1]->zScore < 0) { echo ' positive-change'; }
                                    else if ($recent_community_race->results[$place - 1] > 0) {echo ' negative-change'; }
                                ?>"><?php echo number_format($recent_community_race->results[$place - 1]->zScore, 3); ?></td>
                            </tr>
                        <?php endfor;?>
                        </tbody>
                    </table>
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