<?php 
//This is the calendar page. It uses custom fields to populate the calendars from the scripts served by timely
get_header(); ?>
<main class="main">
    <div class="content-containter">
    <?php while ( have_posts() ) : the_post(); ?>
    	<div id="calendar">
    	<div class="mustDo calendar"><?php the_field('slider'); ?></div>
        <div id="mustDo" class="calendar mustDo">
        	<?php the_field('must_do'); ?>
        </div>
        <h2 class="hide calendar allEvents">All Events</h2>
        <div id="allEvents" class="hide calendar allEvents">
         	<?php the_field('all_events'); ?>
        </div>
      </div> 
    <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>