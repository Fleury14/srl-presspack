<?php get_header();

date_default_timezone_set('America/Los_Angeles');

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://api.speedrunslive.com/races',
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
    )  
));
$info = curl_exec($curl);
curl_close($curl);
$race_info = json_decode($info);
$ff4races = array();
foreach( $race_info->races as $race ) {
    if ($race->game->abbrev == 'ff4hacks') { array_push($ff4races, $race); }
}
// var_dump($ff4races);
?>

	<main role="main" aria-label="Content">
		<!-- section -->
		<section>


		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

			<!-- article -->
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
                <div class="container">

                <?php 
                if (count($ff4races) === 0): ?>
                <h2 class="text-center">There are no FF4: FE races going on at this time.</h2>
                <?php endif;
                foreach ($ff4races as $race):
                ?>
                    <div class="row row-eq-height full-race-row">
                        <div class="col-md-3 race-status">
                            <h2 class="audiowide"><?php echo $race->statetext; ?></h2>
                            <p><?php echo 'Started at ' . date('h:m:sa T', $race->time) ?></p>
                        </div>
                        <div class="col-md-9 race-info text-center">
                            <h2 class="text-center audiowide"><?php echo $race->numentrants ?> participant<?php if ($race->numentrants > 1) { echo 's'; } ?></h2>
                            <div class="container-fluid">
                                <div class="row">
                                    
                                    <div class="col-md-4 order-2">
                                        <div class="racer-list">
                                            <?php
                                            $totalSkill = 0;
                                            // var_dump($race->entrants); 
                                            foreach ($race->entrants as $racer => $value) { ?>
                                            <div class="racer-container">
                                                <a href="?page_id=12&player=<?php echo $racer ?>"><span class="racer"><?php echo $racer; ?></span></a>
                                                <span class="badge badge-primary"><?php echo $value->trueskill; ?></span>
                                            </div>
                                            <?php
                                                $totalSkill += $value->trueskill;
                                            }
                                            $averageSkill = $race->numentrants > 0 ? round($totalSkill / $race->numentrants) : 0;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-8 order-1">
                                        <div class="d-flex justify-content-center align-items-center no-child-margin mb-3">
                                            <p class="mr-3 press-start">Average Skill Rating: </p><span class="h2 audiowide"><?php echo $averageSkill ?></span>
                                        </div>
                                        
                                        <?php
                                        $flagsPos = strpos($race->goal, 'flags=');
                                        $endFlagPos = strpos($race->goal, '&amp');
                                        $flags = substr($race->goal, $flagsPos + 6, $endFlagPos - $flagsPos - 6);
                                        ?>
                                        <p class="current-race-flags">Flags: <?php echo $flags;
                                            if ($flags == 'J2KC2T4S3BF2NE3$X2Y2GWZ') { echo ' (League Qualifier)'; }
                                            if ($flags == 'JK2PCT3S2BF2NE3X2Y2GZ') { echo ' (League Ro.32)'; }
                                            if ($flags == 'JK2PC3T3S2BF2NE3X2Y2GZ') { echo ' (League Playoffs)'; }
                                        ?></p>
                                        <div class="d-flex flex-wrap">
                                        <?php
                                            if (strpos($flags, 'V') !== false && strpos($flags, 'V2') === false){
                                                ?><span class="badge badge-info mr-3 mb-3">Forge The Crystal</span><?php
                                            }
                                            if (strpos($flags, 'V2') !== false) {
                                                ?><span class="badge badge-info mr-3 mb-3">Giant%</span><?php
                                            }
                                            if (strpos($flags, 'J') === false){
                                                ?><span class="badge badge-danger mr-3 mb-3">No J-Items or Cmds</span><?php
                                            }
                                            if (strpos($flags, 'J') !== false && strpos($flags, 'J2') === false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">J-Items Only</span><?php
                                            }
                                            if (strpos($flags, 'J2') !== false) {
                                                ?><span class="badge badge-success mr-3 mb-3">J Items & Cmds</span><?php
                                            }
                                            if (strpos($flags, 'K2') !== false || strpos($flags, 'K3') !== false || strpos($flags, 'K4') !== false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Key Items at Summon/Lunar Bosses</span><?php
                                            }
                                            if (strpos($flags, 'K3') !== false || strpos($flags, 'K4') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">Key Items in Trapped Chests</span><?php
                                            }
                                            if (strpos($flags, 'K4') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">Possible Moon before Underground</span><?php
                                            }
                                            if (strpos($flags, 'P') === false) {
                                                ?><span class="badge badge-success mr-3 mb-3">Pass in a shop</span><?php
                                            }
                                            if (strpos($flags, 'P') !== false && strpos($flags, 'P2') === false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Pass mixed with key items</span><?php
                                            }
                                            if (strpos($flags, 'P2') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">Pass in 3 random non-moon chests</span><?php
                                            }
                                            if (strpos($flags, 'C3') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">Only 5 characters available</span><?php
                                            }
                                            if (strpos($flags, 'W') === false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">WHY BURN?</span><?php
                                            }
                                            if (strpos($flags, 'W') !== false && strpos($flags, 'W2') === false) {
                                                ?><span class="badge badge-success mr-3 mb-3">Whyburn disabled</span><?php
                                            }
                                            if (strpos($flags, 'W2') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">Whyburn replaced</span><?php
                                            }
                                            if (strpos($flags, 'G') === false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Major Glitches Allowed</span><?php
                                            } else {
                                                ?><span class="badge badge-success mr-3 mb-3">Major Glitches Disabled</span><?php
                                            }
                                            if (strpos($flags, 'N') === false) {
                                                ?><span class="badge badge-success mr-3 mb-3">Free Lunch</span><?php
                                            }
                                            if (strpos($flags, 'N') !== false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Rydia's Mom / Mist Dragon in play</span><span class="badge badge-warning mr-3 mb-3">No Free Lunch Recruitments</span><?php
                                            }
                                            if (strpos($flags, 'N2') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">No Free Bosses</span><?php
                                            }
                                            if (strpos($flags, 'S') !== false && strpos($flags, 'S2') === false && strpos($flags, 'S3') === false && strpos($flags, 'S4') === false && strpos($flags, 'S5') === false ) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Shop shuffled with bias</span><?php
                                            }
                                            if (strpos($flags, 'S2') !== false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Shops randomized with bias</span><?php
                                            }
                                            if (strpos($flags, 'S3') !== false) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Shops randomized without location bias</span><?php
                                            }
                                            if (strpos($flags, 'S4') !== false) {
                                                ?><span class="badge badge-success mr-3 mb-3">Shops contain anything</span><?php
                                            }
                                            if (strpos($flags, 'S5') !== false) {
                                                ?><span class="badge badge-danger mr-3 mb-3">CabinFest 2018</span><?php
                                            }
                                            if (strpos($flags, 'T') !== false && strpos($flags, 'T2') === false && strpos($flags, 'T3') === false && strpos($flags, 'T4') === false && strpos($flags, 'T5') === false ) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Untrapped chests shuffled with bias</span><span class="badge badge-warning mr-3 mb-3">Trapped chests shuffles with Summons/Lunar Bosses</span><?php
                                            }
                                            if (strpos($flags, 'T2') !== false  ) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Untrapped chests randomized with bias</span><?php
                                            }
                                            if (strpos($flags, 'T3') !== false  ) {
                                                ?><span class="badge badge-warning mr-3 mb-3">Untrapped chests randomized without location bias</span><?php
                                            }
                                            if (strpos($flags, 'T4') !== false  ) {
                                                ?><span class="badge badge-success mr-3 mb-3">Untrapped chests contain anything</span><?php
                                            }
                                            if (strpos($flags, 'T5') !== false  ) {
                                                ?><span class="badge badge-danger mr-3 mb-3">Untrapped chests empty</span><?php
                                            }
                                        ?>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            
                            
                            <button class="btn btn-primary mt-3 mb-4 toggleResults" >Show Results</button>
                            <div class="results-container hide-results">
                                <table class="table-sm table-striped w-100 ">
                                    <thead>
                                        <tr>
                                            <th scope="col">Rank</th>
                                            <th scope="col">Racer</th>
                                            <th scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php for ($place = 1; $place <= $race->numentrants; $place++ ): ?>
                                        <tr>
                                            <th scope="row" class="press-start"><?php echo $place; ?></th>
                                            <td class="audiowide">
                                                <?php foreach ($race->entrants as $racer => $value) {
                                                    if ($value->place == $place) { echo $value->displayname; }
                                                }  ?>
                                            </td>
                                            <td class="press-start">
                                                <?php foreach ($race->entrants as $racer => $value) {
                                                    if ($value->place == $place) { 
                                                        $hours = floor($value->time / 3600);
                                                        $minutes = floor($value->time / 60 % 60);
                                                        $seconds = floor($value->time % 60);
                                                        echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                                    }
                                                }  ?>
                                            </td>
                                        </tr>
                                    <?php endfor;?>
                                    </tbody>
                                </table>
                                
                            </div>
                            
                        </div>
                    </div>
                    
                <?php endforeach;
                ?>
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
        <!-- <script src="src/routes/current.js"></script> -->
	</main>
<?php get_footer(); ?>