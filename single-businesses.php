<?php
/**
 * The template for displaying all single posts.
 *
 * @package understrap
 */

get_header();
$premiere = get_field('premiere_business');
$biz_page = get_field('business_page');
if(!$premiere && !$biz_page){
	wp_redirect(home_url());
}
$container   = get_theme_mod( 'understrap_container_type' );
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<div class="wrapper" id="single-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post();?>
					<div class="row pad-bottom">
						<div class="col-xs-12 col-md-8">
							<div class="row margin-bottom">
									<?php $toggleDisplay = get_field('business_display');
									if($toggleDisplay == 'color') {
										$md = 12;
									}
									elseif($toggleDisplay == 'logo'){
										$business_logo = get_field('business_logo');
										$md = 8;
								
										echo "<div class='col-xs-12 col-md-4' style='align-items: center; justify-content: center; align-self: center;'><img src='{$business_logo["url"]}' alt='{$business_logo[
										"alt"]}'></div>";
									} ?>
								<div id="single-business-info" class="col-xs-12 col-md-<?php echo $md; ?>">
									<h1><?php the_title(); ?></h1>
									<?php if(get_field('business_phone')){ phone(get_field('business_phone')); } ?><br />
									<?php if(get_field('business_website')){ ?>
										<a href="//<?php the_field('business_website'); ?>" target="_blank"><?php the_field('business_website'); ?></a><br />
									<?php } ?>
									<?php if(get_field('business_address')) {?>
										<a href="http://maps.google.com/maps?q=<?php the_field('business_address'); ?>" target="_blank" class="address"><?php the_field('business_address');?></a><br />
									<?php } ?>

								</div>
							</div>
							<?php the_field('business_description'); ?>
								
							
						</div>
						<div class="col-xs-12 col-md-4">
							<h3>Current Deals</h3>
							<?php $date_now = date('Y-m-d h:i:s', time());
							$args = array(
								'post_type' => 'deals',
								'meta_query' => array(
									array(
										'key' => 'associated_business', // name of custom field
										'value' => '"' . get_the_ID() . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
										'compare' => 'LIKE'
									),
									array(
										'key' => 'deal_end_date',
										'value' => $date_now,
										'compare' => '>='
									)
								)
							);
							$deals = get_posts($args);

						?>
						<?php if( $deals ){ ?>
							<ul class="nostyle no-pad">
							<?php foreach( $deals as $deal ){ 
								$end_date = date('m/d/y g:i a', strtotime(get_field('deal_end_date', $deal->ID)));
							?>
								<li>
									<div class="deal">
										<div class="row">
											<div class="col-xs-12 col-md-12">
												<h5><a href="<?php the_permalink($deal->ID); ?>"><?php echo get_the_title( $deal->ID ); ?></a></h5>
												<p><?php the_field('the_deal_subtitle', $deal->ID); ?>
												<p>Ends <?php echo $end_date; ?></p>
											</div>
										</div>
									</div>
								</li>
							<?php } ?>
							</ul>
						<?php } ?>
						</div>
					</div>

				<?php endwhile; // end of the loop. ?>
				<div class="col-xs-12">
					<h3>Past Deals</h3>
				</div>
				<div class="row" style="justify-content: space-around;">
					<?php //Let's retool the args for expired deals
					$args['meta_query'][1]['compare'] = '<';
					$args['posts_per_page'] = 3;
					$expired = get_posts($args);
					if($expired){
						foreach($expired as $deal){ 
							$end_date = date('m/d/y g:i a', strtotime(get_field('deal_end_date', $deal->ID))); ?>
							<div class="col-xs-12 col-md-4 bg-white container-padding tri-list" style="">
								<div class="col-xs-12 col-md-12">
									<h5><?php echo get_the_title($deal->ID); ?></h5>
									<p>Ended <?php echo $end_date; ?></p>
								</div>
							</div>
						<?php }
					} ?>
				</div>
			</main><!-- #main -->

		</div><!-- #primary -->

	</div><!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>

