<?php
/**
 * Dealadd partial template
 *
 * @package Do802
 */
$user = get_current_user_id();
?>
<div id="notice"></div>
<div id="details">
  <div class="details-business"></div>
  <div class="details-title"></div>
  <div class="details-subtitle"></div>
  <div class="details-expired"></div>
  <div class="details-remaining"></div>
  <div class="details-details"></div>
  <div class="details-claim"></div>
  <div class="details-image"><img src="http://via.placeholder.com/800x300/7bbf4a/fff?text=Your+image+here"></div>
  <div class="details-restrictions"></div>
  <span class="close">X</span>
</div>
<div class="container">
	<div class="row" style="padding-top: 30px;">
		<div class="col-xs-12 col-md-4">
      <a href="<?php echo wp_logout_url("/add-a-deal"); ?>">Logout</a>
      <?php 
      $is_editing = false;
      $is_trashed = false;
      if(isset($_GET['trashed'])){
        $is_trashed = true;
        echo "<p class='text-red'>You have deleted your deal</p>";
      }
      if(isset($_GET['edit_deal'])){
          $is_editing = true;?>
        <p class="red-text">You are currently editing a live deal!<br /><a href="/add-a-deal/">Add a new deal instead.</a></p>
      <?php } elseif(isset($_GET['update']) && !$is_trashed){ ?>
        <p class="confirmation">Your deal has been submitted!</p>
      <?php } else { ?>
        <p>Need to modify an existing deal? <a href="#modify">Click here.</a></p>
      <?php } ?>
			<h3><?php echo ($is_editing ? "Edit Your " : "Add a New "); ?> Deal!</h3>
			<?php the_content(); ?>
		</div>
		<div class="col-xs-12 col-md-8">
		<h3>Preview</h3>
			<div id="deal-demo" class="deal sticky " data-dealid="271" data-category="4" style="margin-top:20px;">
        <div class="table">
          <div class="deal-id" hidden="">271</div>
          <div class="deal-last-updated" hidden=""></div>
          <div class="deal-business"><img src="http://via.placeholder.com/300x300/7bbf4a/fff?text=Your+logo+here"></div>
        	<div class="deal-contents">
          	<h2 class="deal-title">Free Dinner for 2</h2>
         		<h3 class="deal-subtext">When you buy $50 or more in gift certificates.</h3>
          	<div class="stopwatch"><span class="bold">Preview</span></div><div class="expires"><span class="expiry">Expires 11/05/1955</span>  <span class="time">5:00 PM</span></div>                <div class="deal-extras">
            	<div class="modal-trigger details-link" data-category="4" data-details="&amp;lt;p&amp;gt;Beef commodo kevin dolore ground round pork belly. Landjaeger commodo nisi nulla short loin minim est brisket bresaola. Esse sunt kielbasa frankfurter fatback. Shoulder doner biltong boudin qui filet mignon cow aute ham hock meatloaf hamburger shank ball tip. Sint shank kielbasa, chicken sunt magna kevin pig exercitation.&amp;lt;/p&amp;gt;" data-title="Free Subscription for 6 Months" data-business="Vermont Standard Inc." data-subtitle="Get half a year for free when you sign up for a year!" data-claim="Call us! 555-555-5555" data-dealid="271" data-deal-restrictions="&amp;lt;p&amp;gt;Ribeye sed brisket, eiusmod strip steak elit landjaeger sunt qui tri-tip doner sausage boudin est hamburger. Biltong cupidatat ham, irure filet mignon pork porchetta pork belly nostrud kevin reprehenderit et hamburger. Swine laborum id porchetta est ut exercitation aute ribeye doner reprehenderit nulla elit dolor. Duis laboris venison bacon, deserunt anim boudin kevin commodo doner frankfurter landjaeger nisi eiusmod aliquip. Leberkas eiusmod ball tip venison ham, chuck alcatra quis sunt kielbasa enim. Fatback salami laborum elit aliqua.&amp;lt;/p&amp;gt;" data-permalink="https://pwa.becomeindelible.com/wp/deals/free-subscription-for-6-months/" data-business-address="" data-business-page="https://pwa.becomeindelible.com/wp/businesses/vermont-standard-inc/">(Details)</div>
          </div>
        </div>
      </div>
    </div>
	</div>
</div>
  <div class="row" id="modify">
    <div class="col-xs-12 col-md-12">
		<h3 style="margin-bottom:1em;">Your Current Deals
				<select id="editable-businesses" name="editable-business" style="margin-bottom: 30px;">
				    <?php global $current_user;
				    wp_get_current_user();
					global $post;
					$business_query = array(
						'posts_per_page' => '-1',
						'post_type' => 'businesses',
					);
					$business_posts = new WP_Query($business_query);
					while($business_posts->have_posts()) : $business_posts->the_post();
						if(current_user_can('administrator')) {
							echo "<option value='{$post->ID}'>" . get_the_title() . "</option>";
							if(!isset($biz)){
								$biz[] = $post->ID;
							}
						} else {
					  	$managers = get_field('business_managers');
					  	if(in_array((string)$current_user->ID, $managers)){
					  		echo "<option value='{$post->ID}'>" . get_the_title() . "</option>";
					  		if(!isset($biz)){	
					  			$biz[] = $post->ID;
					  		}
					  	}
					}
					endwhile;
					wp_reset_postdata(); ?>
				</select>
			</h3>
    	<div class="row padding-top" id="editable-deals" data-user="<?php echo $current_user->ID;?>">
	    	<?php get_editable_posts($current_user->ID, $biz); ?>
	    </div>
      </div>       
    </div>
  </div>
</div>
</div>