<?php get_header();
	$player_name = $_GET['player'] ? $_GET['player'] : 'Fleury14';
	// FF4 target profile stat curl
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://api.speedrunslive.com/stat?player=' . $player_name . '&game=ff4hacks&page=1',
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json"
		)  
	));
	$info = curl_exec($curl);
	curl_close($curl);
	$overall_stats = json_decode($info);
	
	$history_curl = curl_init();
	curl_setopt_array($history_curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://api.speedrunslive.com/pastraces?player=' . $player_name . '&game=ff4hacks&page=1&pageSize=500',
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json"
		)  
	));
	$history = curl_exec($history_curl);
	curl_close($history_curl);
	$race_history = json_decode($history);

	// get ranking list to allow for SRL rating to be displayed in w/l table
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
	// var_dump($player_list);
	function getPlayersRating($player) {
		$rating = 0;
		global $player_list;
		global $total_players;

        foreach ($player_list as $leader) {
            if (strtolower($leader->name) === strtolower($player)) {
                $rating = floor($leader->trueskill);
            }
		}
		return $rating;
    }
	
	// go through races and increment last week, two weeks and 30 days if necessary
	// throw all league flag races into its respective array
	$league_qual_races = array();
	$league_ro32_races = array();
	$league_ro16_races = array();
	$all_my_races = array();
	$past_7_days = $past_14_days = $past_30_days = 0;
 
	foreach ($race_history->pastraces as $race) {
		 if ( strpos($race->goal, 'J2KC2T4S3BF2NE3$X2Y2GWZ') !== false ) { array_push($league_qual_races, $race); }
		 if ( strpos($race->goal, 'JK2PCT3S2BF2NE3X2Y2GZ') !== false ) { array_push($league_ro32_races, $race); }
		 if ( strpos($race->goal, 'JK2PC3T3S2BF2NE3X2Y2GZ') !== false ) { array_push($league_ro16_races, $race); }
		 if ( time() - $race->date < 604800 ) { $past_7_days++; } // 604800 = 7 days
		 if ( time() - $race->date < 1209600 ) { $past_14_days++; } // 1029600 = 14 days
		 if ( time() - $race->date < 2592000 ) { $past_30_days++; } // 2592000 = 30 days
		 zScore($race);
		 array_push($all_my_races, $race);
	 }
 
	 

	 function dateCmp($race1, $race2) {
		 return $race1->date - $race2->date;
	 }

	 function getPercentOfTotal( $stat ) {
		global $overall_stats;
		$percent = round( $stat / $overall_stats->stats->totalRaces * 1000 ) / 10;
		return $percent;
	}

	function findMyTime($race) {
		global $player_name;
		$time = null;
		if ($race !== null) {
			foreach ($race->results as $result) {
				if (strtolower($result->player) == strtolower($player_name) && $result->time !== -1) { 
					$time = $result->time;
				}
			}
		} 
		return $time;
	}

	// sort races by time and then grab the best time
	 usort($league_qual_races, "timeCmp");
	 usort($league_ro32_races, "timeCmp");
	 usort($league_ro16_races, "timeCmp");

	// go through all three arrays, pull users time and compare with best time, replace is necessary
	$league_qual_best = 99999;

	foreach ($league_qual_races as $race) {
		$myTime = findMyTime($race);
		if ($myTime < $league_qual_best && $myTime !== null ) {
			$league_qual_best = $myTime;
		}
	}

	$league_ro32_best = 99999;

	foreach ($league_ro32_races as $race) {
		$myTime = findMyTime($race);
		if ($myTime < $league_ro32_best && $myTime !== null ) {
			$league_ro32_best = $myTime;
		}
	}

	$league_ro16_best = 99999;

	foreach ($league_ro16_races as $race) {
		$myTime = findMyTime($race);
		if ($myTime < $league_ro16_best && $myTime !== null ) {
			$league_ro16_best = $myTime;
		}
	}

	// sort races by date then put the top 10 in an array. Also while grabbing each race, get a time sum for averaging

	usort($league_qual_races, "dateCmp");
	usort($league_ro32_races, "dateCmp");
	usort($league_ro16_races, "dateCmp");
	usort($all_my_races, "dateCmp");

	$league_qual_last10 = array();
	$league_ro32_last10 = array();
	$league_ro16_last10 = array();
	$last_10_overall = array();
	$league_qual_last10avg = $league_ro32_last10avg = $league_ro16_last10avg = null;
	$league_qual_last10sum = $league_ro32_last10sum = $league_ro16_last10sum = 0;
	
	for ($i = 0; $i < 10; $i++) {
		array_push($league_qual_last10, $league_qual_races[$i]);
		$league_qual_last10sum += findMyTime($league_qual_races[$i]);
		array_push($league_ro32_last10, $league_ro32_races[$i]);
		$league_ro32_last10sum += findMyTime($league_ro32_races[$i]);
		if (findMyTime($league_ro16_races[$i]) !== null ) {
			array_push($league_ro16_last10, $league_ro16_races[$i]);
			$league_ro16_last10sum += findMyTime($league_ro16_races[$i]);
		}
		
	}

	$league_qual_last10 = array_filter($league_qual_last10);
	$league_ro32_last10 = array_filter($league_ro32_last10);
	$league_ro16_last10 = array_filter($league_ro16_last10);

	$league_qual_last10avg = $league_qual_last10sum / count($league_qual_last10);
	$league_ro32_last10avg = $league_ro32_last10sum / count($league_ro32_last10);
	$league_ro16_last10avg = $league_ro16_last10sum / count($league_ro16_last10);

	$opponents = array();

	function totalGamesCmp($player1, $player2) {
		return ($player2["wins"] + $player2["losses"]) - ($player1["wins"] + $player1["losses"]);
	}

	foreach ($all_my_races as $race) {
		$myTime = findMyTime($race);
		foreach ($race->results as $result) {
			if (strtolower($result->player) === strtolower($player_name)) {
				continue;
			} else if ($result->time === -1 && $myTime === null) {
				continue;
			} else {
				if (array_key_exists($result->player, $opponents) == false) {
					$opponents[$result->player] = array(
						"wins" => 0,
						"losses" => 0
					);
					if (($result->time > $myTime || $result->time === -1) && $myTime !== null) {
						$opponents[$result->player]["wins"]++;
					} else {
						// var_dump($opponents[$result->player]);
						$opponents[$result->player]["losses"]++;
					}
				} else {
					if (($result->time > $myTime || $result->time === -1) && $myTime !== null) {
						$opponents[$result->player]["wins"]++;
					} else {
						$opponents[$result->player]["losses"]++;
					}
				}
			}
		}
	}
	uasort($opponents, "totalGamesCmp");

?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>
			<!-- <div class="banner"></div> -->

			<?php if (!is_null($overall_stats)): ?>
			<div class="sub-banner">
				<h1 class="audiowide"><?php echo $player_name ?></h1>
				<p class="audiowide">Rank: #<?php echo $overall_stats->stats->rank; ?></p>
			</div>
			
			<div class="race-summary">
				<div class="container">
					<div class="row time-rows">
						<div class="col-md-6 text-center">
							<p>Date of first race</p>
							<h2><?php echo date("F jS, Y", $overall_stats->stats->firstRaceDate); ?></h2>
						</div>
						<div class="col-md-6 text-center">
							<p>Total time played</p>
							
							<?php 
								
								$timebuffer = $overall_stats->stats->totalTimePlayed;
								// echo "Timebuffer:" . $timebuffer;

								if ($timebuffer > 86400) {
									$days_played = floor( $timebuffer / 86400 );
									$timebuffer = $timebuffer % 86400;
								}

								if ($timebuffer > 3600) {
									$hours_played = floor( $timebuffer / 3600);
									$timebuffer = $timebuffer % 3600;
								}

								if ($timebuffer > 60) {
									$minutes_played = floor( $timebuffer / 60);
									$timebuffer = $timebuffer % 60;
								}

								$seconds_played = $timebuffer;

								if ($days_played > 0) { 
									?><h3><?php echo $days_played . ' days ';?></h3><?php }
								if ($hours_played > 0) { 
									?><h3><?php echo $hours_played . ' hours ';?></h3><?php }
								if ($minutes_played > 0) {
									?><h3><?php echo $minutes_played . ' minutes ';?></h3><?php }
							
							?>
							
						</div>
					</div>
					<div class="row summary-margin">
						<div class="col-md-3 summary-left">
							<p>Total Races:</p>
							<h2><?php echo $overall_stats->stats->totalRaces; ?></h2>
						</div>
						<div class="col-md-3 summary-mid">
							<div class="summary-row">
								<p>1st place finishes:</p>
								<p><?php echo $overall_stats->stats->totalFirstPlace; ?></p>
							</div>
							<div class="summary-row">
								<p>2nd place finishes:</p>
								<p><?php echo $overall_stats->stats->totalSecondPlace; ?></p>
							</div>
							<div class="summary-row">
								<p>3rd place finishes:</p>
								<p><?php echo $overall_stats->stats->totalThirdPlace; ?></p>
							</div>
							<div class="summary-row">
								<p>Forfeits:</p>
								<p><?php echo $overall_stats->stats->totalQuits; ?></p>
							</div>
						</div>
						<div class="col-md-6 summary-right">
							<div class="summary-row">
								<div class="progress">
									<div class="progress-bar gold-bg" role="progressbar" style="width: <?php echo getPercentOfTotal($overall_stats->stats->totalFirstPlace); ?>%;" aria-valuenow="<?php echo getPercentOfTotal($overall_stats->stats->totalFirstPlace); ?>"><?php echo getPercentOfTotal($overall_stats->stats->totalFirstPlace); ?>%</div>
								</div>
							</div>
							<div class="summary-row">
								<div class="progress">
									<div class="progress-bar silver-bg" role="progressbar" style="width: <?php echo getPercentOfTotal($overall_stats->stats->totalSecondPlace); ?>%;" aria-valuenow="<?php echo getPercentOfTotal($overall_stats->stats->totalSecondPlace); ?>"><?php echo getPercentOfTotal($overall_stats->stats->totalSecondPlace); ?>%</div>
								</div>
							</div>
							<div class="summary-row">
								<div class="progress">
									<div class="progress-bar bronze-bg" role="progressbar" style="width: <?php echo getPercentOfTotal($overall_stats->stats->totalThirdPlace); ?>%;" aria-valuenow="<?php echo getPercentOfTotal($overall_stats->stats->totalThirdPlace); ?>"><?php echo getPercentOfTotal($overall_stats->stats->totalThirdPlace); ?>%</div>
								</div>
							</div>
							<div class="summary-row">
								<div class="progress">
									<div class="progress-bar maroon-bg" role="progressbar" style="width: <?php echo getPercentOfTotal($overall_stats->stats->totalQuits); ?>%;" aria-valuenow="<?php echo getPercentOfTotal($overall_stats->stats->totalQuits); ?>"><?php echo getPercentOfTotal($overall_stats->stats->totalQuits); ?>%</div>
								</div>
							</div>
						</div>
					</div>
					<!--- Begin # of race in x days section -->
					<div class="row mb-5">
						<div class="col-md-4">
							<div class="p-3 x-days d-flex flex-column text-center">
								<p>Races in last 7 days</p>
								<h2><?php echo $past_7_days; ?></h2>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-3 x-days d-flex flex-column text-center">
								<p>Races in last 14 days</p>
								<h2><?php echo $past_14_days; ?></h2>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-3 x-days d-flex flex-column text-center">
								<p>Races in last 30 days</p>
								<h2><?php echo $past_30_days; ?></h2>
							</div>
						</div>
						
					</div>
					<!--- End # of races in x days section -->
					<!-- begin win-loss section -->
					<h2 class="text-center mt-5">Win Loss record against other racers (Scrollable)</h2>

					<div class="row win-loss-row">
						<div class="col-sm-12">
						</div>
						<?php foreach($opponents as $opponent=>$value):?>
						<div class="col-sm-2 win-loss" style="background-color: rgb(0,0,<?php echo $value["wins"] / ($value["wins"] + $value["losses"]) * 200; ?> );">
							<p class="audiowide"><?php echo $opponent; ?><span class="ml-1 badge badge-primary"><?php echo getPlayersRating($opponent); ?></span></p>
							<p class="press-start"><?php echo $value["wins"]; ?>-<?php echo $value["losses"]; ?></p>
						</div>
						<?php endforeach; ?>
					</div>
					<!-- End win-loss section -->
					<!--- Begin Flag type section -->
					<div class="row">
						<div class="col-sm-4 mini-table mr-0">
							<p class="text-center mini-table-header">League Qualifiers</p>
							<div class="d-flex justify-content-between">
								<p>Best Time:</p> 
								<p><?php
									$hours = floor($league_qual_best / 3600);
									$minutes = floor($league_qual_best / 60 % 60);
									$seconds = floor($league_qual_best % 60);
									echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
							</div>
							<div class="d-flex justify-content-between">
								<p>Last 10 Avg:</p> 
								<p><?php
									$hours = floor($league_qual_last10avg / 3600);
									$minutes = floor($league_qual_last10avg / 60 % 60);
									$seconds = floor($league_qual_last10avg % 60);
									echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
							</div>
						</div>
						<div class="col-sm-4 mini-table mr-0">
							<p class="text-center mini-table-header">League Ro.32</p>
							<div class="d-flex justify-content-between">
								<p>Best Time:</p> 
								<p><?php
									$hours = floor($league_ro32_best / 3600);
									$minutes = floor($league_ro32_best / 60 % 60);
									$seconds = floor($league_ro32_best % 60);
									echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
							</div>
							<div class="d-flex justify-content-between">
								<p>Last 10 Avg:</p> 
								<p><?php
									$hours = floor($league_ro32_last10avg / 3600);
									$minutes = floor($league_ro32_last10avg / 60 % 60);
									$seconds = floor($league_ro32_last10avg % 60);
									echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
							</div>
						</div>
						<div class="col-sm-4 mini-table mr-0">
							<p class="text-center mini-table-header">League Ro.16</p>
							<div class="d-flex justify-content-between">
								<p>Best Time:</p> 
								<p><?php
									$hours = floor($league_ro16_best / 3600);
									$minutes = floor($league_ro16_best / 60 % 60);
									$seconds = floor($league_ro16_best % 60);
									echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
							</div>
							<div class="d-flex justify-content-between">
								<p>Last 10 Avg:</p> 
								<p><?php
									$hours = floor($league_ro16_last10avg / 3600);
									$minutes = floor($league_ro16_last10avg / 60 % 60);
									$seconds = floor($league_ro16_last10avg % 60);
									echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);?></p>
							</div>
						</div>
					</div><!-- End flag type section -->
					<div class="row">
						<div class="col-sm-12">
							<h2 class="text-center w-100 mt-5 mb-5">Change over last 20 races</h2>
						</div>
						<div class="col-sm-12">
							<canvas id="ratingOverTime" width="1000" height="400"></canvas>
						</div>
						<div class="col-sm-12">
							<h2 class="text-center w-100 mt-5">Z-score over last 20 races</h2>
							<h6 class="text-center">(Lower is better)</h6>
						</div>
						<div class="col-sm-12">
							<canvas id="zScoreOverTime" width="1000" height="400"></canvas>
						</div>
					</div> <!-- end charting row -->
					<div class="row">
						<div class="col-sm-12">
							<h2 class="text-center">Recent races</h2>
						</div>
						<div class="col-sm-12 d-flex flex-wrap justify-content-center">
							<?php for ($i = 0; $i < count($all_my_races) && $i < 20; $i++): ?>
							<div class="race-tag" data-index="race-<?php echo $i; ?>">
								<span><?php echo $i + 1; ?></span>
							</div>
							<?php endfor; ?>
							<div id="race-data" class="w-100"></div>
						</div>
					</div>
				</div>
			</div>
		<?php endif;
		if (is_null($overall_stats)):
		?>
			<h1 class="text-center">Player <?php echo $_GET['player']; ?> not found...</h1>
		<?php endif; ?>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
				<?php the_content(); ?>
				
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<?php get_footer(); ?>