<?php 
//This is the calendar page. It uses custom fields to populate the calendars from the scripts served by timely
get_header(); ?>
<main class="main">
    <div class="content-containter">
    <?php while ( have_posts() ) : the_post(); ?>
    	<?php the_content(); ?>
    <?php endwhile; ?>
    </div>
    <div class="content-containter">
    	<h3>Your Existing Deals</h3>
    	
    </div>
</main>
<?php get_footer(); ?>
