<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			<li class="nav-item<?php if(get_query_var('pagename') == '') echo (' active'); ?>">
				<a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
			</li>
			<li class="nav-item<?php if(get_query_var('pagename') == 'statpage') echo (' active'); ?>">
				<a class="nav-link" href="/statpage">Player Stats</a>
			</li>
			<li class="nav-item<?php if(get_query_var('pagename') == 'ff4festats') echo (' active'); ?>">
				<a class="nav-link" href="/ff4festats">Game Stats</a>
			</li>
			<li class="nav-item<?php if(get_query_var('pagename') == 'currentraces') echo (' active'); ?>">
				<a class="nav-link" href="/currentraces">Current Races</a>
			</li>
			<li class="nav-item<?php if(get_query_var('pagename') == 'player-rankings') echo (' active'); ?>">
				<a class="nav-link" href="/player-rankings">Player Rankings</a>
			</li>
			</ul>
			<?php echo get_query_var('pagename'); ?>
			<?php $userinfo = wp_get_current_user(); 
			if($userinfo->display_name == null) {
				?><a href="<?php echo wp_login_url( get_permalink() ); ?>"><button class="btn btn-success">Login</button></a><?php
			} else {
				?><span class="user-name mr-3"><?php echo $userinfo->display_name; ?></span>
				<a href="<?php echo wp_logout_url( get_permalink() ); ?>"><button class="btn-danger rounded">Logout</button></a><?php
			}
			?>
			<span class="navbar-text ml-3">
				<form action="/" class="mb-0">
					<input type="hidden" name="page_id" value="12">
					<input class="header-search" type="text" name="player" placeholder="Player name..">
					<button type="sumbit" class="btn-sm btn-default header-search-button">Search</button>
				</form>
			</span>
		</div>
	</nav>

	