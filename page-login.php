<?php get_header(); ?>
<h1>I'm the login page!!</h1>
<div class="login-form">
<?php
$args = array(
    'redirect' => home_url(),
    'id_username' => 'user',
    'id_password' => 'pass',
);
wp_login_form( $args ); 
?>
<a href="<?php echo wp_registration_url(); ?>">
    <h4 class="text-center">REGISTER DAWG</h4>
</a>
</div>

<?php get_footer(); ?>
