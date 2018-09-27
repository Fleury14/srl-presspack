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
    $race_flags = array(
        'J' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'Allowance of items/commands found in the Japanese version of FFIV. 0: None. 1: Items only. 2: Items and commands.'
        ),
        'K' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            'count' => 0,
            'description' => 'Locations of key items. 0: Vanilla locations. 1: Vanilla locations but shuffled. 2: Possible locations adds Lunar Bosses and Summon Bosses. 3: Possible locations include all trapped chests. 4: Same as K3 only you may be forced to go to the moon to get underground access.'
        ),
        'C' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            'count' => 0,
            'description' => 'Handling of character locations. 0: Vanilla locations. 1: All characters shuffled, with Edge/FuSoYa weighted to be in slightly tougher locations, with every character available somewhere. 2: Same as C1 with no weight. 3: Only 5 characters available.'
        ),
        'P' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'Handles the location of the Pass, which now acts as a free warp to Zeromus. 0: Found in a shop. 1: Mixed in with key items. 2: Placed in 3 random non-lunar chests.'
        ),
        'T' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            'count' => 0,
            'description' => 'Content of treasure chests. 0: Vanilla. 1: Shuffled with location bias. 2: Randomized, with location bias. 3: Randomized with no bias. 4: Completely randomized, may contain anything. 5: All non-trapped chests are empty. (NOTE: In C2-C3, trapped chests pull from a stronger item pool than untrapped)'
        ),
        'S' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            'count' => 0,
            'description' => 'Content of Shops. 0: Vanilla 1: Shuffled with location bias. 2: Randomized with location bias. 3: Randomized with no bias, excluding highest-level items. 4: Shops may contain anything. 5: Shops only contain cabins.'
        ),
        'B' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'How bosses are handled. 0: Vanilla locations. 1: Bosses are shuffled retaining the stats of the location it is at. Some bosses will not block underground access. 2: Any boss can be anywhere.'
        ),
        'F' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'FuSoYa. 0: He starts at level 50 with all spells and HP. 1: Starts with 900HP and some spells, gets all his spells and HP by completing Mt. Ordeals. 2: Starts with 500HP and random low-level spells. Gains 100HP and 3 random spells after defeating any boss.'
        ),
        'N' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'No Free Lunch 0: No changes 1: None of the free character locations are available. Edward does not give you a key item in Toroia. Rydias mom gives you a key item in Mist Village after defeating the Mist Dragon. 2: All bosses have the boss bit. Alternate win conditions on D.Knight, Karate, K/Q Eblan and WaterHag are removed.' 
        ),
        'E' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            'count' => 0,
            'description' => "Random encounters: 0: Vanilla. 1: Reduces encounter rate. 60% of trapdoors are disabled. Individual Behemoth encounters in Bahamut's lair have a 50% chance of being disabled. 2: Same as E1, but all aforementioned trapdoors and Behemoths are disabled. 3: Encounters (and trapdoor fights) are toggleable from the in-game Custom menu. 4: All random encounters are disabled."
        ),
        '$' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            'count' => 0,
            'description' => 'Money modifier. 0: No changes. 1: Chests with weak items contain GP instead. Chests with weak and moderate items now contain GP instead. 3: All items in shops are free.'
        ),
        'X' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => "Experience modifier. 0: No changes. 1: XP is not divided amongst party members; each surviving character recieves the full amount. 2: Same as X1, in addition after collecting 10 key items, XP is gained is doubled. In full parties, characters with relatively low levels will recieve XP bonuses in order to 'slingshot' them up to the partys level quickly."
        ),
        'Y' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'Turbo. 0: Hold Y to dash. 1: In addition to Y0, battle speed and battle message speed default to 1. 2: Same as Y1 except dashing is automatic, hold Y to walk.'
        ),
        'G' => array(
            0 => 0,
            1 => 0,
            'count' => 0,
            'description' => "'Glitchless' 0: All glitches enabled. 1: Disabled item duplication, MP underflow, and warping from the Dwarf Castle into the crystal room after the boss fights no longer gets you the Sealed cave reward."
        ),
        'W' => array(
            0 => 0,
            1 => 0,
            2 => 0,
            'count' => 0,
            'description' => 'Wyvern Behavior. 0: Vanilla. 1: Opening MegaNuke is disabled. 2: Opening MegaNuke is replaced with a different random command.'
        ),
        'Z' => array(
            0 => 0,
            1 => 0,
            'count' => 0,
            'description' => 'Zeromus sprite: 0: Uses Vanilla true-form sprite. Is also akin to commiting heresy. 1: Replaces the Zeromus true-form sprite with a random sprite. Does not effect the boss behavior at all.'
        )
        
    );

    function parseRaceFlags($goal) {
        global $race_flags;
        if ( strpos($goal, 'J') !== false && strpos($goal, 'J2') === false ) { $race_flags['J'][1]++; $race_flags['J']['count']++; }
        if ( strpos($goal, 'J2') !== false ) { $race_flags['J'][2]++; $race_flags['J']['count']++; }
        if ( strpos($goal, 'J') === false ) { $race_flags['J'][0]++; $race_flags['J']['count']++; }
        if ( strpos($goal, 'K') !== false && strpos($goal, 'K2') === false && strpos($goal, 'K3') === false && strpos($goal, 'K4') === false ) { $race_flags['K'][1]++; $race_flags['K']['count']++; }
        if ( strpos($goal, 'K2') !== false ) { $race_flags['K'][2]++; $race_flags['K']['count']++; }
        if ( strpos($goal, 'K3') !== false ) { $race_flags['K'][3]++; $race_flags['K']['count']++; }
        if ( strpos($goal, 'K4') !== false ) { $race_flags['K'][4]++; $race_flags['K']['count']++; }
        if ( strpos($goal, 'K') === false ) { $race_flags['K'][0]++; $race_flags['K']['count']++; }
        if ( strpos($goal, 'C') !== false && strpos($goal, 'C2') === false && strpos($goal, 'C3') === false ) { $race_flags['C'][1]++; $race_flags['C']['count']++; }
        if ( strpos($goal, 'C2') !== false ) { $race_flags['C'][2]++; $race_flags['C']['count']++; }
        if ( strpos($goal, 'C3') !== false ) { $race_flags['C'][3]++; $race_flags['C']['count']++; }
        if ( strpos($goal, 'C') === false ) { $race_flags['C'][0]++; $race_flags['C']['count']++; }
        if ( strpos($goal, 'P') !== false && strpos($goal, 'P2') === false ) { $race_flags['P'][1]++; $race_flags['P']['count']++; }
        if ( strpos($goal, 'P2') !== false ) { $race_flags['P'][2]++; $race_flags['P']['count']++; }
        if ( strpos($goal, 'P') === false ) { $race_flags['P'][0]++; $race_flags['P']['count']++; }
        if ( strpos($goal, 'T') !== false && strpos($goal, 'T2') === false && strpos($goal, 'T3') === false && strpos($goal, 'T4') === false && strpos($goal, 'T5') === false ) { $race_flags['T'][1]++; $race_flags['T']['count']++; }
        if ( strpos($goal, 'T2') !== false ) { $race_flags['T'][2]++; $race_flags['T']['count']++; }
        if ( strpos($goal, 'T3') !== false ) { $race_flags['T'][3]++; $race_flags['T']['count']++; }
        if ( strpos($goal, 'T4') !== false ) { $race_flags['T'][4]++; $race_flags['T']['count']++; }
        if ( strpos($goal, 'T5') !== false ) { $race_flags['T'][5]++; $race_flags['T']['count']++; }
        if ( strpos($goal, 'S') !== false && strpos($goal, 'S2') === false && strpos($goal, 'S3') === false && strpos($goal, 'S4') === false && strpos($goal, 'S5') === false ) { $race_flags['S'][1]++; $race_flags['S']['count']++; }
        if ( strpos($goal, 'S2') !== false ) { $race_flags['S'][2]++; $race_flags['S']['count']++; }
        if ( strpos($goal, 'S3') !== false ) { $race_flags['S'][3]++; $race_flags['S']['count']++; }
        if ( strpos($goal, 'S4') !== false ) { $race_flags['S'][4]++; $race_flags['S']['count']++; }
        if ( strpos($goal, 'S5') !== false ) { $race_flags['S'][5]++; $race_flags['S']['count']++; }
        if ( strpos($goal, 'B') !== false && strpos($goal, 'B2') === false ) { $race_flags['B'][1]++; $race_flags['B']['count']++; }
        if ( strpos($goal, 'B2') !== false ) { $race_flags['B'][2]++; $race_flags['B']['count']++; }
        if ( strpos($goal, 'B') === false ) { $race_flags['B'][0]++; $race_flags['B']['count']++; }
        if ( strpos($goal, 'F') !== false && strpos($goal, 'F2') === false ) { $race_flags['F'][1]++; $race_flags['F']['count']++; }
        if ( strpos($goal, 'F2') !== false ) { $race_flags['F'][2]++; $race_flags['F']['count']++; }
        if ( strpos($goal, 'F') === false ) { $race_flags['F'][0]++; $race_flags['F']['count']++; }
        if ( strpos($goal, 'N') !== false && strpos($goal, 'N2') === false ) { $race_flags['N'][1]++; $race_flags['N']['count']++; }
        if ( strpos($goal, 'N2') !== false ) { $race_flags['N'][2]++; $race_flags['N']['count']++; }
        if ( strpos($goal, 'N') === false ) { $race_flags['N'][0]++; $race_flags['N']['count']++; }
        if ( strpos($goal, 'E') !== false && strpos($goal, 'E2') === false && strpos($goal, 'E3') === false && strpos($goal, 'E4') === false ) { $race_flags['E'][1]++; $race_flags['E']['count']++; }
        if ( strpos($goal, 'E2') !== false ) { $race_flags['E'][2]++; $race_flags['E']['count']++; }
        if ( strpos($goal, 'E3') !== false ) { $race_flags['E'][3]++; $race_flags['E']['count']++; }
        if ( strpos($goal, 'E4') !== false ) { $race_flags['E'][4]++; $race_flags['E']['count']++; }
        if ( strpos($goal, 'E') === false ) { $race_flags['E'][0]++; $race_flags['E']['count']++; }
        if ( strpos($goal, '$') !== false && strpos($goal, '$2') === false && strpos($goal, '$3') === false ) { $race_flags['$'][1]++; $race_flags['$']['count']++; }
        if ( strpos($goal, '$2') !== false ) { $race_flags['$'][2]++; $race_flags['$']['count']++; }
        if ( strpos($goal, '$3') !== false ) { $race_flags['$'][3]++; $race_flags['$']['count']++; }
        if ( strpos($goal, '$') === false ) { $race_flags['$'][0]++; $race_flags['$']['count']++; }
        if ( strpos($goal, 'X') !== false && strpos($goal, 'X2') === false ) { $race_flags['X'][1]++; $race_flags['X']['count']++; }
        if ( strpos($goal, 'X2') !== false ) { $race_flags['X'][2]++; $race_flags['X']['count']++; }
        if ( strpos($goal, 'X') === false ) { $race_flags['X'][0]++; $race_flags['X']['count']++; }
        if ( strpos($goal, 'Y') !== false && strpos($goal, 'Y2') === false ) { $race_flags['Y'][1]++; $race_flags['Y']['count']++; }
        if ( strpos($goal, 'Y2') !== false ) { $race_flags['Y'][2]++; $race_flags['Y']['count']++; }
        if ( strpos($goal, 'Y') === false ) { $race_flags['Y'][0]++; $race_flags['Y']['count']++; }
        if ( strpos($goal, 'G') !== false && strpos($goal, 'G2') === false ) { $race_flags['G'][1]++; $race_flags['G']['count']++; }
        if ( strpos($goal, 'G') === false ) { $race_flags['G'][0]++; $race_flags['G']['count']++; }
        if ( strpos($goal, 'W') !== false && strpos($goal, 'W2') === false ) { $race_flags['W'][1]++; $race_flags['W']['count']++; }
        if ( strpos($goal, 'W2') !== false ) { $race_flags['W'][2]++; $race_flags['W']['count']++; }
        if ( strpos($goal, 'W') === false ) { $race_flags['W'][0]++; $race_flags['W']['count']++; }
        if ( strpos($goal, 'Z') !== false && strpos($goal, 'Z2') === false ) { $race_flags['Z'][1]++; $race_flags['Z']['count']++; }
        if ( strpos($goal, 'Z') === false ) { $race_flags['Z'][0]++; $race_flags['Z']['count']++; }
    }

    foreach ($past_races->pastraces as $race) {
        if ( strpos($race->goal, 'J2KC2T4S3BF2NE3$X2Y2GWZ') !== false ) { array_push($league_qual_races, $race); }
        if ( strpos($race->goal, 'JK2PCT3S2BF2NE3X2Y2GZ') !== false || strpos($race->goal, 'HTTZZ League Match') !== false ) { array_push($league_ro32_races, $race); }
        if ( strpos($race->goal, 'JK2PC3T3S2BF2NE3X2Y2GZ') !== false ) { array_push($league_ro16_races, $race); }
        if ( strpos($race->goal, 'Community Race') !== false ) { array_push($community_races, $race); }
        $flags_pos = strpos($race->goal, '?flags=');
        $seed_pos = strpos($race->goal, '&amp;seed=');
        if ($flags_pos !== false ) {
            $goal_flags = substr($race->goal, $flags_pos + 7, $seed_pos - $flags_pos - 7);
        }
        
        if ($goal_flags) { 
            // var_dump($goal_flags);
            parseRaceFlags($goal_flags);
        }
        
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
                    <div class="row mt-5">
                        <div class="col-xs-12 w-100">
                            <h2 class="text-center text-uppercase press-start">Flag Stats</h2>
                        </div>
                    </div>
                </div>
                <table class="table table-dark table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="audiowide" scope="row">Flag</th>
                            <th class="audiowide" scope="row">0</th>
                            <th class="audiowide" scope="row">1</th>
                            <th class="audiowide" scope="row">2</th>
                            <th class="audiowide" scope="row">3</th>
                            <th class="audiowide" scope="row">4</th>
                            <th class="audiowide" scope="row">5</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($race_flags as $flag=>$value): ?>
                        <tr>
                            <th class="audiowide flag-stats-flag" data-toggle="tooltip" data-placement="top" data-delay="{'show': 5, 'hide': 5000}" title="<?php echo $value['description']; ?>"><?php echo $flag ?></th>
                            <?php for ($number = 0; $number < 6; $number++): 
                                if ($value[$number] !== null):
                                ?>
                                <td class="press-start"><?php echo floor($value[$number] / $value['count'] * 1000) / 10 ?>%</td>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <h2 class="text-center text-uppercase press-start mt-5">Race start time heatmap</h2>
                <p class="text-center">Last 100 races</p>
                <div id="heatmap-canvas" class="mb-5"></div>
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