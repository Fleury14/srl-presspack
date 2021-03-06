<?php get_header();

date_default_timezone_set('America/New_York');

// Grab all race data
$history_curl = curl_init();
curl_setopt_array($history_curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://api.speedrunslive.com/pastraces?game=ff4hacks&season=0&page=1&pageSize=10',
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
    )  
));
$info = curl_exec($history_curl);
curl_close($history_curl);
$past_races = json_decode($info);
$recent_start = 0;
$recent_end = 4;

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
?>
<div class="jumbotron mb-0">
  <h1 class="display-4 text-center">FF4 FE Race Stats</h1>
</div>
<div class="current-races-banner d-flex justify-content-center align-items-center">
  <a href="currentraces"><h2 class="text-center audiowide">There <? echo count($ff4races) !== 1 ? 'are' : 'is' ?> <?php echo count($ff4races); ?> race<? echo count($ff4races) !== 1 ? 's' : '' ?> currently being run.</h2></a>
</div>
<div class="container-fluid ticker-row mb-5">
  <div class="row">
    <div class="col-sm-1 col-xs-hidden d-flex justify-content-center align-items-center ticker-col"></div>
    <?php for ($i = $recent_start; $i <= $recent_end; $i++): ?>
      <div class="col-sm-2 recent-cell d-flex justify-content-center flex-column p-1">
        <p class="mb-0"><u class="recent-cell-header"><?php echo date('n/d g:ia T ', $past_races->pastraces[$i]->date); ?></u></p>
        <p class="mb-0"><strong class="gold-text">1st:</strong> <?php echo $past_races->pastraces[$i]->results[0]->player; ?></p>
        <p class="mb-0"><strong class="silver-text">2nd:</strong> <?php echo $past_races->pastraces[$i]->results[1]->player; ?></p>
        <p class="mb-0"><strong class="bronze-text">3rd:</strong> <?php echo $past_races->pastraces[$i]->results[2]->player; ?></p>
      </div>
    <?php endfor; ?>
  </div>
</div>
<!-- <p>Photo by Donald Giannatti on Unsplash</p> -->
<div class="container">
  <div class="row">
    <div class="col-sm-8">
      <?php if (have_posts()): while (have_posts()) : the_post() ?>
      <article>
        <a href="<?php the_permalink(); ?>"><h3> <?php the_title(); ?></h3></a>
        <p class="sub-text text-uppercase">Created by <?php the_author(); ?> on <?php the_date(); ?></p>
        <p> <?php the_content(); ?> </p>
      </article>
      
      <?php endwhile;
      endif; ?>
    </div>
    <div class="col-sm-4 featured">
      <figure class="figure">
        <div class="laptop-image"></div>
        <h2 class="press-start text-uppercase">Links</h2>
        <a target="_blank" href="http://ff4fe.com">FF4 Free Enterprise Site</a>
        <a target="_blank" href="http://speedrunslive.com">SpeedRunsLive</a>
        <a target="_blank" href="https://discord.gg/AVeUqkb">FF4: FE Discord</a>
      </figure>
      
    </div>
  </div>
</div>

<?php
$pages = get_pages();
// var_dump($pages); ?>


<div class="accordion" id="accordionExample">
  <?php for ($i = 0; $i < count($pages); $i++): ?>
  <div class="card-header blue-bg" id="heading<?php echo $i; ?>">
      <h5 class="mb-0 position-relative">
        <button class="btn btn-link <?php if ($i === 0) { echo 'collapsed '; } ?>accordion-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $i; ?>" aria-expanded="true" aria-controls="collapse<?php echo $i; ?>">
          <?php echo $pages[$i]->post_title; ?>
        </button>
        <a href="/?page_id=<?php echo $pages[$i]->ID; ?>"><button class="btn-info link-button">Visit Page</button></a>
      </h5>
    </div>

    <div id="collapse-<?php echo $i; ?>" class="collapse" aria-labelledby="heading<?php echo $i; ?>" data-parent="#accordionExample">
      <div class="card-body">
        <?php echo $pages[$i]->post_content ?>
      </div>
    </div>
  </div>
  <?php endfor; ?>
  
</div>


<?php get_footer(); ?>
