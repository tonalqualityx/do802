<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
 */

/**
 * Theme setup and custom theme supports.
 */
require get_template_directory() . '/inc/setup.php';

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Load functions to secure your WP install.
 */
require get_template_directory() . '/inc/security.php';

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/pagination.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/custom-comments.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom WordPress nav walker.
 */
require get_template_directory() . '/inc/bootstrap-wp-navwalker.php';

/**
 * Load WooCommerce functions.
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Load Editor functions.
 */
require get_template_directory() . '/inc/editor.php';

/**
  * Add REST API support to an already registered post type.
  */
  add_action( 'init', 'my_custom_post_type_rest_support', 25 );
  function my_custom_post_type_rest_support() {
    global $wp_post_types;
  
    //be sure to set this to the name of your post type!
    $post_type_name = 'deals';
    if( isset( $wp_post_types[ $post_type_name ] ) ) {
        $wp_post_types[$post_type_name]->show_in_rest = true;
        $wp_post_types[$post_type_name]->rest_base = $post_type_name;
        $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
    }
  
  }

// Create widget for login page
function indelible_login_widgets_init() {

	register_sidebar( array(
		'name'          => 'Login for Businesses',
		'id'            => 'dash_login',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );
    register_sidebar( array(
        'name'          => 'Adrotate Widget',
        'id'            => 'adrotate_widget',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ) );

}
add_action( 'widgets_init', 'indelible_login_widgets_init' );

/**
 * Add the meta fields to REST API responses for posts read and write
 * Read and write a post meta fields in post responses
 */
function mg_register_meta_api() {
    //Meta Fields that should be added to the API 
    $meta_fields = array(
    	'acf',
        'subtitle',
        'deal_type',
        'deal_details',
        'to_claim',
        'associated_business',
        'use_logo'

    );
    //Iterate through all fields and add register each of them to the API
    foreach ($meta_fields as $field) {
        register_rest_field( 'ring',
            $field,
            array(
                'get_callback'    => array( $this, 'mg_fw_get_meta'),
                'update_callback' => array( $this, 'mg_fw_update_meta'),
                'schema'          => null,
            )
        );
    }
}
add_action( 'rest_api_init', 'mg_register_meta_api' );

function wpse_20160526_rest_query_vars( $valid_vars ) {
    $valid_vars = array_merge( $valid_vars, array(  'meta_query'  ) ); // Omit meta_key, meta_value if you don't need them
    return $valid_vars;
}

add_filter( 'rest_query_vars', 'wpse_20160526_rest_query_vars', PHP_INT_MAX, 1 );


// 3) Parse Custom Args

function wpse_20160526_rest_product_query( $args, $request ) {

    if ( isset( $args[ 'meta_query' ] ) ) {

        $relation = 'AND';
        if( isset($args['meta_query']['relation']) && in_array($args['meta_query']['relation'], array('AND', 'OR'))) {
            $relation = sanitize_text_field( $args['meta_query']['relation'] );
        }
        $meta_query = array(
            'relation' => $relation
        );

        foreach ( $args['meta_query'] as $inx => $query_req ) {
        /*
            Array (

                [key] => test
                [value] => testing
                [compare] => =
            )
        */
            $query = array();

            if( is_numeric($inx)) {

                if( isset($query_req['key'])) {
                    $query['key'] = sanitize_text_field($query_req['key']);
                }
                if( isset($query_req['value'])) {
                    $query['value'] = sanitize_text_field($query_req['value']);
                }
                if( isset($query_req['type'])) {
                    $query['type'] = sanitize_text_field($query_req['type']);
                }
                if( isset($query_req['compare']) && in_array($query_req['compare'], array('=', '!=', '>','>=','<','<=','LIKE','NOT LIKE','IN','NOT IN','BETWEEN','NOT BETWEEN', 'NOT EXISTS')) ) {
                    $query['compare'] = sanitize_text_field($query_req['compare']);
                }
            }

            if( ! empty($query) ) $meta_query[] = $query;
        }

        // replace with sanitized query args
        $args['meta_query'] = $meta_query;
    }

    return $args;
}
add_action( 'rest_product_query', 'wpse_20160526_rest_product_query', 10, 2 );

/**
 * Handler for getting custom field data.
 *
 * @since 0.1.0
 * 
 * @param array $object The object from the response
 * @param string $field_name Name of field
 *
 * @return mixed
 */
function mg_get_meta( $object, $field_name ) {
    return get_post_meta( $object[ 'id' ], $field_name );
}

/**
 * Handler for updating custom field data.
 *
 * @since 0.1.0
 * @link  http://manual.unyson.io/en/latest/helpers/php.html#database
 * @param mixed $value The value of the field
 * @param object $object The object from the response
 * @param string $field_name Name of field
 *
 * @return bool|int
 */
function mg_update_meta( $value, $object, $field_name ) {
    if ( ! $value || ! is_string( $value ) ) {
        return;
    }

    return update_post_meta( $object->ID, $field_name, maybe_serialize( strip_tags( $value ) ) );

}
//Lets mod the form for submitting deals so that only the appropriate businesses are available

function indelible_dropdown_with_posts($form){
    
    foreach($form['fields'] as &$field){
        
        if($field['inputType'] != 'select' || strpos($field['cssClass'], 'business-select') === false)
            continue;
        $theUser = get_current_user_id();
        $args = array(
        	'numberposts' => -1,
        	'post_status' => 'publish',
        	'post_type'	=> 'businesses',
        	'orderby'	=> 'title',
        	'order'		=> 'ASC',	
        );
       	if(!current_user_can('administrator')){
       		$args['meta_query'] = array(
       			array(
       				'key' => 'business_managers',
					'value' => $theUser,
					'compare' => 'LIKE',
				)
   			);
       	}
        $posts = get_posts($args);
        
        // update 'Select a Post' to whatever you'd like the instructive option to be
        // 'a:1:{i:0;s:2:"' . . '";}'
        
        foreach($posts as $post){
            $choices[] = array('text' => $post->post_title, 'value' =>  $post->ID);
        }
        
        $field['choices'] = $choices;
        
    }
    
    return $form;
} add_filter('gform_pre_render', 'indelible_dropdown_with_posts');

//Add users to the list!
function acf_load_students_field( $field ) {
    // reset choices
    $field['choices'] = array();
    
    
    // get the list of users
    $choices = get_users(array('orderby' => 'user_nicename', 'order' => 'ASC', 'role' => 'business_manager') );
    // $choices = array('test1', 'test2');
    
    // explode the value so that each line is a new array piece
    // $choices = explode("\n", $choices);

    
    // remove any unwanted white space
    // $choices = array_map('trim', $choices);

    
    // loop through array and add to field 'choices'
    if( is_array($choices) ) {
        
        foreach( $choices as $choice ) {
            
            $field['choices'][ $choice->ID ] = $choice->display_name;
            
        }
        
    }
    
    // $field = array('test1', 'test2');

    // return the field
    return $field;
    
}

add_filter('acf/load_field/name=business_managers', 'acf_load_students_field');

//The following provided by David Smith to setup scheduling fields on the front end. Thanks David!
/**
 * Gravity Wiz // Gravity Forms // Schedule a Post by Date Field
 *
 * Schedule your Gravity Form generated posts to be published at a future date, specified by the user via GF Date and Time fields.
 *
 * @version	  1.0
 * @author    David Smith <david@gravitywiz.com>
 * @license   GPL-2.0+
 * @link      http://gravitywiz.com/...
 */

// CHANGE: "546" to the ID of your form
add_filter( 'gform_post_data_4', 'gw_schedule_post_by_date_field', 10, 3 );
function gw_schedule_post_by_date_field( $post_data, $form, $entry ) {

    $date = $entry['14']; // CHANGE: "7" to the ID of your Date field
    $time = $entry['32']; // CHANGE: "8" to the ID of your Time field

    ### don't touch the magic below this line ###

    if( empty( $date ) ) {
        return $post_data;
    }

    if( $time ) {
        list( $hour, $min, $am_pm ) = array_pad( preg_split( '/[: ]/', $time ), 3, false );
        if( strtolower( $am_pm ) == 'pm' ) {
            $hour += 12;
        }
    } else {
        $hour = $min = '00';
    }

    $schedule_date = date( 'Y-m-d H:i:s', strtotime( sprintf( '%s %s:%s:00', $date, $hour, $min ) ) );

    $post_data['post_status']   = 'future';
    $post_data['post_date']     = $schedule_date;
    $post_data['post_date_gmt'] = get_gmt_from_date( $schedule_date );
    $post_data['edit_date']     = true;

    return $post_data;
}

add_action( 'gform_after_submission_4', 'set_post_content', 10, 2 );
function set_post_content( $entry, $form ) {
	$post = get_post($entry['post_id']);
	update_field('_acf_post_id', $post, $post);
	update_field('_acf_changed', 1, $post);
	update_field('the_deal_subtitle', get_field('the_deal_subtitle', $post), $post);
	update_field('deal_type', get_field('deal_type', $post), $post);
	update_field('deal_end_date', get_field('deal_end_date', $post), $post);
	update_field('deal_image', get_field('deal_image', $post), $post);
	add_action( 'rest_api_init', array( __CLASS__, 'create_rest_routes' ), 10 );
}	

//Reposition Featured Image
add_action('do_meta_boxes', 'wpse33063_move_meta_box');
function wpse33063_move_meta_box(){
    remove_meta_box( 'postimagediv', 'deals', 'side' );
    add_meta_box('postimagediv', __('Deal Image (optional)'), 'post_thumbnail_meta_box', 'deals', 'normal', 'high');
}
//Breakdown the Expiration Date
function expiration($end_date){
	$date_now = date_create(current_time('Y-m-d h:i:s'));
	$now_compare = current_time('YmdHi');
    $end_date = date_create($end_date);
    $expires = $end_date->format('m/d/Y');
    $expires_compare = $end_date->format('YmdHi');
    $expires_time = $end_date->format('h:i a');
    $interval = date_diff($date_now, $end_date);
    $breakdown = explode( ',', $interval->format('%m,%d,%h'));
    if($breakdown[0] < 1 && $breakdown[1] < 1 && $breakdown[2] > 0) {
    	$today = true;
    }
    else {
    	$today = false;
    }
    if($now_compare < $expires_compare) {
    	$expired = false;
    }
    else {
	    $expired = true;
    }
    $response = array(
    	'expiration date' => $expires,
    	'expiration time' => $expires_time,
    	'expired' => $expired,
    	'expires today' => $today,
    	'breakdown' => $breakdown,
    	'date now' => $date_now,
    	'now_compare' => $now_compare,
    	'expires compare' => $expires_compare,
    );
    return $response;
}
function expiry_calc() {
	$unit = get_theme_mod('hide_deal_term', 'days');
	$quantity = get_theme_mod('hide_deal_calc', 2);
	if($unit == 'days'){
		$quantity = $quantity*24;
	}
	return date('Y-m-d h:i:s', time() - 60 * 60 * $quantity);
}
//Compare dates & return timeline for being awesome...
function date_mathing($end_date) {
	$date_info = expiration($end_date);
    $date_now = date_create(current_time('Y-m-d H:i:s'), timezone_open('America/New_York'));
    $show_date = date_create(current_time('Y-m-d H:i:s'), timezone_open('America/New_York'));
    $end_date = date_create($end_date, timezone_open('America/New_York'));
    $show_value = get_theme_mod('show_flag_count', 24);
    $show_terms = get_theme_mod('show_flag_terms', 'hours');
    $show_date = date_add(date_create(current_time('Y-m-d H:i:s'), timezone_open('America/New_York')), date_interval_create_from_date_string("$show_value $show_terms"));
    $the_diff = '';
    if($date_info['expired']) {
        return "<div class='missed'>You missed it!</div><div class='expires'>Expired $expires</div>";
    }
    elseif($show_date >= $end_date) {
	    $interval = date_diff($date_now, $end_date);
	    $breakdown = explode( ',', $interval->format('%m,%d,%h,%i'));
	    $s = '';
	    $the_diff = "<div class='stopwatch'><span class='bold'>";
	    if($breakdown[0] > 0) {
	        if($breakdown[0] > 1){$s = 's';}
	        $the_diff .= "{$breakdown[0]}</span> month$s ";
	    }
	    elseif($breakdown[1] > 0) {
	        if($breakdown[1] > 1){$s = 's';}
	        $the_diff .= "{$breakdown[1]}</span> day$s ";
	    }
	    elseif($breakdown[2] > 0) {
	        if($breakdown[2] > 1){$s = 's';}
	        $the_diff .= "{$breakdown[2]}</span> hour$s ";
	    }
	    elseif($breakdown[3] > 0) {
	    	if($breakdown[3] > 1) {$s = 's';}
	    	$the_diff .= "{$breakdown[3]}</span> minute$s ";
	    }
	    $the_diff .= "left!</div>";
	}
    $expires = $end_date->format('m/d/Y h:i a');
    return "$the_diff<div class='expires'>Expires $expires</div>";
}

//Now let's get rid of the admin bar so we don't end up with any funny business
add_filter('show_admin_bar', '__return_false');

//Let's build some ajax calls!
add_action( 'wp_ajax_nopriv_ajax_pagination', 'my_ajax_pagination' );
add_action( 'wp_ajax_ajax_pagination', 'my_ajax_pagination' );

function my_ajax_pagination() {
    // $query_vars = json_decode( stripslashes( $_POST['query_vars'] ), true );
    $page_num = array_filter(explode(',',htmlspecialchars($_POST['page'])));
    $cat = htmlspecialchars($_POST['category']);
    $date_now = expiry_calc();
    $args = array(
    'post_type' => 'deals',
    'meta_query' => array( 
      array(
        'key' => 'deal_end_date',
        'value' => $date_now,
        'compare' => '>=',
        'type' => 'DATE',
      ),
    ),
    // 'posts_per_page' => 20,
    'post__not_in' => $page_num,
    'post_status' => 'publish',
    );
    if($cat > 0){
        $args['cat'] = $cat;
    }
    $deals = new WP_Query($args); 
    $GLOBALS['wp_query'] = $deals;
    if($deals->have_posts()) : while($deals->have_posts()) : $deals->the_post();
	    $dealID = get_the_ID();
	    $biz = get_field('associated_business');
	    $bizID = $biz[0]->ID; 
	    $biz_display = get_field('business_display', $bizID);
	    $biz_logo = get_field('business_logo', $bizID);
	    $deal_cats = get_the_category();
	    $deal_details = get_field('deal_details');
	    $premiere_biz = get_field('premiere_business', $bizID); 
	    $biz_page = get_field('business_page', $bizID); 
	    $biz_url = get_the_permalink($bizID);
	    $biz_use_logo = get_field('use_logo'); ?>
	    <div id="deal-<?php echo $dealID; ?>" class="deal " data-dealID="<?php echo $dealID; ?>" data-category='<?php echo $deal_cats[0]->term_id; ?>'>
	      <div class="table">
	      <div class="deal-id" hidden><?php echo $dealID; ?></div>
	      <div class="deal-last-updated" hidden></div>
	      <div class="deal-business" <?php if($biz_display == "color") {echo "style='background:" . get_field('business_color', $bizID) . ";'";} ?> ><?php if($biz_display == 'logo'){echo "<img src='{$biz_logo['url']}'>";} else { echo get_the_title($bizID);} ?></div>
	      <div class="deal-contents">
	        <h2 class="deal-title"><?php echo get_the_title(); ?></h2>
	        <h3 class="deal-subtext"><?php the_field('the_deal_subtitle'); ?></h3>
	        <?php echo date_mathing(get_field('deal_end_date')); ?>
	        <div class="deal-extras">
	          <div class="modal-trigger details-link" data-category="<?php echo $deal_cats[0]->term_id; ?>" data-details="<?php echo htmlspecialchars(htmlspecialchars($deal_details)); ?>" data-title="<?php echo the_title(); ?>" data-business="<?php echo get_the_title($bizID);?>" data-subtitle="<?php echo htmlspecialchars(htmlspecialchars(get_field('the_deal_subtitle'))); ?>" data-claim="<?php echo htmlspecialchars(htmlspecialchars(get_field('to_claim'))); ?>" data-dealID="<?php echo $dealID; ?>" data-deal-restrictions="<?php echo htmlspecialchars(htmlspecialchars(get_field('deal_restrictions'))); ?>" data-permalink="<?php echo get_the_permalink(); ?>" data-business-address="<?php echo htmlspecialchars(str_replace(array(' '), '%20', preg_replace('/\r|\n|\.|\,/', '', get_field('business_address', $bizID)))); ?>" <?php if($biz_page || $premiere_biz){echo " data-business-page='{$biz_url}'";} if($biz_use_logo){echo " data-business-logo='{$biz_logo["url"]}'";} ?> >(Details)</div>
	        </div>
	      </div>
	      </div>
	    </div>
	    <?php endwhile; 
	    if (  $deals->max_num_pages > 1 ){ ?>
	    <span id="more">Load More</span>
    <?php }
    endif; 
    wp_reset_postdata();
    die();
}

//AJAX TO PRODUCE BUSINESS LOGO


function ajax_logo(){
    $bizID = htmlspecialchars($_POST['business']);
    $logo = get_field('business_logo', $bizID);
    return $logo['url'];
}

//function to create human readable phone number
function phone($phone) {
    $area = substr($phone, 0, 3);
    $exch = substr($phone, 3, 3);
    $num = substr($phone, 6, 4);
    echo "<a href='tel:1{$phone}'>{$area}-{$exch}-{$num}</a>";
}

//Let's modify the crap out of the notifications
//More info here - https://documentation.onesignal.com/docs/web-push-wordpress-faq#section-customizing-wordpress-plugin-behavior
add_filter('onesignal_send_notification', 'onesignal_send_notification_filter', 10, 4);

function onesignal_send_notification_filter($fields, $new_status, $old_status, $post) {
    $business = get_field("associated_business", $post->ID);
    $business_title = get_the_title($business[0]->ID);
    $business_logo = get_field("business_logo", $business[0]->ID);
    $fields['headings'] = array("en" => "$business_title Do802 Deal");
    if(get_the_post_thumbnail($post->ID) == '' || get_the_post_thumbnail($post->ID) == NULL){
        $fields['chrome_web_image'] = $business_logo['url'];
    }
    if(!$business_logo['url']){
        $fields['chrome_web_icon'] = "https://do802.com/wp-content/themes/do802/images/do802logo.png";
    }
    else {
        $fields['chrome_web_icon'] = $business_logo['url'];
    }
    // $fields['contents'] = array("en" => $post);
    return $fields;
}

//Hide posts from other users
function alter_the_edit_screen_query( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can( 'activate_plugins' ) )  {
add_action( 'views_edit-post', 'remove_items_from_edit' );
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}

add_filter('parse_query', 'alter_the_edit_screen_query' );

function remove_items_from_edit( $views ) {
    unset($views['all']);
    unset($views['publish']);
    unset($views['trash']);
    unset($views['draft']);
    unset($views['pending']);
    return $views;
}

if(isset($_GET['edit_deal'])){
    add_filter( 'gform_field_value', 'modify_deals', 10, 3 );
    function modify_deals( $value, $field, $name ) {
        $cur_deal = htmlspecialchars($_GET['edit_deal']);
        $end_date = get_field('deal_end_date', $cur_deal);
        $end_date = new DateTime($end_date);
        $values = array(
            // 'assoc_business' => get_the_field('associated_business', $cur_deal),
            'deal_title'    => get_the_title($cur_deal),
            'hidden_title'  => get_the_title($cur_deal),
            'subtitle'      => get_field('the_deal_subtitle', $cur_deal),
            'start_date'    => get_the_date('m/d/Y', $cur_deal),
            'start_time'    => get_the_time('', $cur_deal),
            'end_date'      => $end_date->format('m/d/Y'),
            'end_time'      => $end_date->format('g:i A'),
            'end_date_calc' => get_field('deal_end_date', $cur_deal),
            'details'       => html_entity_decode(strip_tags(get_field('deal_details', $cur_deal))),
            'restrictions'  => html_entity_decode(strip_tags(get_field('deal_restrictions', $cur_deal))),
            'claim'         => get_field('to_claim', $cur_deal),
            'notification'  => 0,

        );
        return isset( $values[ $name ] ) ? $values[ $name ] : $value;
    }
    add_filter("gform_submit_button_1", "add_my_value", 10, 2);
    function add_my_value($button, $form) {
        $dom = new DOMDocument();
        $dom->loadHTML($button);
        $input = $dom->getElementsByTagName('input')->item(0);
        if ($input->hasAttribute('onclick')) {
            $input->setAttribute("onclick","");
        } else {
            $input->setAttribute("onclick","");
        }
        return $dom->saveHtml();
    }
    // add_filter('gform_validation', 'custom_validation');
    // function custom_validation($validation_result){
    //     return false;
    // }
}

add_filter( 'acf/rest_api/key', function( $key, $request, $type ) {
    return 'acf_fields';
}, 10, 3 );

add_action( 'wp_ajax_nopriv_use_logo_mod', 'use_logo_mod' );
add_action( 'wp_ajax_use_logo_mod', 'use_logo_mod' );
function use_logo_mod(){
    $field_key = "field_59fbe88259cca";
    $value = array($_POST['useLogo']);
    update_field($field_key, $value, $_POST['edit_deal']);
    echo $field_key . " " . $value . " " . $_POST['edit_deal'];
}

//Get posts the current user is assigned as a business manager on
//If user is an admin will get all deals.
//Only shows deals that are active on the front end
add_action( 'wp_ajax_nopriv_get_editable_posts', 'get_editable_posts' );
add_action( 'wp_ajax_get_editable_posts', 'get_editable_posts' );
function get_editable_posts($userID, $biz = null) {
    if(isset($_POST['userID'])){
        $userID = $_POST['userID'];
        $biz = $_POST['biz'];
    }
    $date_now = expiry_calc();
    if($biz == null){
        $author_query = array(
          'posts_per_page' => '-1',
          'post_type' => 'deals', 
          'post_status' => array('publish', 'draft', 'future'),
          'meta_query' => array(
            array(
              'key' => 'deal_end_date',
              'value' => $date_now,
              'compare' => '>=',
              'type' => 'DATE',
            ),
          ),
        );
        editable_loop($author_query);
    }
    else {
        foreach ($biz as $value) {
            $author_query = array(
              'posts_per_page' => '-1',
              'post_type' => 'deals', 
              'post_status' => array('publish', 'draft', 'future'),
              'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'deal_end_date',
                    'value' => $date_now,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'associated_business',
                    'value' => $value,
                    'compare' => 'LIKE',
                ),
              ),
            );
            editable_loop($author_query);
        }
    }
    if(isset($_POST['userID'])){
        die();
    }
}

function editable_loop($args){
    global $post;
    $author_posts = new WP_Query($args);
    if($author_posts->have_posts()){
        while($author_posts->have_posts()) : $author_posts->the_post();
        ?>
          <div class="col-md-4" style="margin-bottom: 20px;">
            <h4><?php the_title(); ?></h4>
              <a href="/add-a-deal?edit_deal=<?php echo $post->ID; ?>" style="margin-right: 30%;">Edit</a>
              <a class="text-red" onclick="return confirm('Are you SURE you want to delete this deal?')" href="<?php echo get_delete_post_link( $post->ID ) ?>">Delete</a>
          </div>
        <?php           
        endwhile;
    } else {
        echo "<div class='col-md-12 col-md-12'><h3>No deals under this merchant.</h3></div>";
    }
    wp_reset_postdata();
}