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
<div class="container">
  <div class="row">
    <?php foreach ($pages as $page): ?>

    <div class="col-md-4 col-sm-6">
      <div class="card page-card mb-5" style="width: 18rem;">
        <div class="card-body">
          <h5 class="card-title"> <?php echo $page->post_title; ?> </h5>
          <p class="sub-text"> <?php echo $page->post_date; ?> by <?php echo $page->post_author ?> </p>
          <div class="card-text"> <?php $content = strlen($page->post_content) > 200 ? substr($page->post_content, 0, 200) . '...' :  $page->post_content; echo $content;  ?> </div>
          <a href="/?page_id=<?php echo $page->ID ?>" class="btn btn-primary">Visit Page</a>
        </div>
      </div>
    </div>
<?php endforeach; ?>
  </div>
</div>



<?php get_footer(); ?>
