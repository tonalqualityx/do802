<?php
/**
 * The template for displaying all single posts.
 *
 * @package understrap
 */

get_header();
$container   = get_theme_mod( 'understrap_container_type' );
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<div class="wrapper" id="single-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post();
					$business = get_field('associated_business');

				?>
					<div id="details" style="position: relative; left: unset;  max-width: unset; transform: unset; top:unset; max-height: unset;">
					<div class="details-business"><h1><?php echo get_the_title($business[0]->ID); ?></h1></div>
					<div class="details-title"><h2><?php the_title(); ?></h2></div>
					<div class="details-subtitle"><?php the_field('the_deal_subtitle'); ?></div>
					<div class="details-expired"><?php echo date_mathing(get_field('deal_end_date')); ?></div>
					<div class="details-remaining"></div>
					<div class="details-details"><p><?php the_field('deal_details'); ?></p></div>
					<div class="details-claim"><p><span class="bold">To Redeem:</span> <?php the_field('to_claim'); ?></p></div>
					<div class="details-image"><?php the_post_thumbnail(); ?></div>
					<div class="details-restrictions"><p><span class="text-red bold">RESTRICTIONS:</span></p><?php the_field('deal_restrictions'); ?></div>
					<div class="details-print"><p><a href='javascript:window.print()''>Print</a></div>

				<?php endwhile; // end of the loop. ?>
			</main><!-- #main -->

		</div><!-- #primary -->

	</div><!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>

