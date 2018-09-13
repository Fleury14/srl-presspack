<?php get_header(); ?>
<div class="jumbotron">
  <h1 class="display-4 text-center">FF4 FE Race Stats</h1>
</div>
<!-- <p>Photo by Donald Giannatti on Unsplash</p> -->
<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <?php if (have_posts()): while (have_posts()) : the_post() ?>
      <article>
        <h3> <?php the_title(); ?></h3>
        <p class="sub-text text-uppercase">Created by <?php the_author(); ?> on <?php the_date(); ?></p>
        <p> <?php the_content(); ?> </p>
      </article>
      
      <?php endwhile;
      endif; ?>
    </div>
    <div class="col-sm-6 featured">
      <figure class="figure">
        <div class="laptop-image"></div>
        <h3>Check out our featured thing</h3>
        <figcaption class="figure-caption">"This thing is awesome! How is this even a thing...." -- Some dude</figcaption>
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
