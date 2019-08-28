<?php
/**
 * Template Name: Submit Deals
 *
 * @package Do802
 */
get_header();

if(is_user_logged_in()) {
	while ( have_posts() ) : the_post();
		get_template_part( 'loop-templates/content', 'dealadd' );
	endwhile;
} else { ?>
	<div class="container">
		<div class="col-xs-12 col-md-6 center">
			<h2>Login</h2>
			<p>You'll need to login to add a new deal. If you don't have an account, contact us to set one up!</p>
			<?php dynamic_sidebar('dash_login'); ?>
		</div>
	</div>
<?php }

get_footer();