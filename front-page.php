<?php
$wp_url = 'https://do802.com';
//Let's hit the API to get the categories & businesses.
//We'll cache this stuff since it isn't as likely to change as often
// Start with the categories
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $wp_url . '/wp-json/wp/v2/categories?hide_empty=true');
$cat_json = curl_exec($ch);
curl_close($ch);
$categories = json_decode($cat_json, true);
// Now get the businesses
$bus = curl_init();
curl_setopt($bus, CURLOPT_RETURNTRANSFER, true);
curl_setopt($bus, CURLOPT_URL, $wp_url . '/wp-json/wp/v2/businesses?filter[meta_query][key]=premiere_business&filter[meta_query][value]=premiere');
$business_json = curl_exec($bus);
curl_close($bus);
$businesses = json_decode($business_json, true);
$fields = array(
  'filter[meta_query]' => array(
    'relation' => 'AND',
    array(
      'key' => 'premiere_business'
    )
  )
);

get_header();
 ?>


  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
  <!-- <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script> -->
  <script>
  // if ('serviceWorker' in navigator) {
  //   navigator.serviceWorker
  //            .register('https://do802.com/OneSignalSDKWorker.js')
  //            .then(function() { console.log('Service Worker Registered'); });
  // }
  </script>
<!--   <script src="https://do802.com/OneSignalSDKWorker.js"></script> -->

  <!-- <script src="<?php echo get_stylesheet_directory_uri(); ?>/scripts/moment.min.js"></script> -->








  <!-- End Google Tag Manager (noscript) -->

  <div id="categories">
    <img src="http://do802.com/wp-content/themes/do802/images/icons/Do802.com_logo-egg-1024.png" alt="Do802 Logo" id="branding">
    <select>
      <option class="all" value="all">Show All</option>
      <?php foreach($categories as $category) {
        if($category['id'] != 1 && $category['count'] > 0){
          var_dump($category);
          echo "<option class='cat' data-id='{$category['id']}' data-name='{$category['name']}'>{$category['name']}</option>";
        }
      } ?>
      <option class="merchants mobile-only" value="merchants">Premiere Merchants</option>
    </select>
    <p class="closer">&times;</p>
  </div>

  <main class="main">
    <div class="content-containter">
      <div id="deals" class="active">
        <div id="details">
          <div class="details-do802 print-only"><img src="https://do802.com/wp-content/themes/do802/images/do802logo.png" /></div>
          <div class="details-business"></div>
          <div class="details-title"></div>
          <div class="details-subtitle"></div>
          <div class="details-expiry"></div>
          <div class="details-expired"></div>
          <div class="details-remaining"></div>
          <div class="details-details"></div>
          <div class="details-claim"></div>
          <div class="details-image"></div>
          <div class="details-restrictions"></div>
          <div class="details-print"></div>
          <span class="close">X</span>
        </div>
        <div id="deals-container">
          <h2 id="filter">All Deals</h2>
          <p id="more">Loding deals...</p>
        </div>
        <div id="businesses">
          <h2>Premiere Merchants</h2>
            <?php
            $args = array(
              'post_type' => 'businesses',
              'meta_query' => array(
                array(
                  'key' => 'premiere_business',
                  'value' => 'true',
                  'compare' => 'LIKE',
                ),
              ),
              'orderby' => 'rand',
              'posts_per_page' => 5,
            );
            $businesses = new WP_Query($args);
            $tall = [];
            $wide = [];
            $flex = [];
            if($businesses->have_posts()) :
                while($businesses->have_posts()) :
                    $businesses->the_post();
                    $business_logo = get_field('business_logo');
                    $logo_file = get_attached_file($business_logo["ID"]);
                    $business_display = get_field('business_display');
                    $business_color = get_field('business_color');
                    $business_link = get_the_permalink();
                    if($business_display == "logo") {
                      $dims = getimagesize($logo_file);
                      $ratio = $dims[1]/$dims[0];
                      if($ratio > 0.5){
                        $tall[] = array(
                          'url'   =>  $business_logo["url"],
                          'link'  =>  $business_link,
                          'title' => get_the_title(),
                        );
                      } else {
                        $wide[] = array(
                          'url'   =>  $business_logo["url"],
                          'link'  =>  $business_link,
                          'title' => get_the_title(),
                        );
                      }
                    } else {
                      $flex[] = array(
                        'color'   =>  $business_color,
                        'link'  =>  $business_link,
                        'title' => get_the_title(),
                      );
                    } ?>
                <?php endwhile;
            endif; ?>
            <div class="premiere-businesses">
                <?php
                $tall_count = count($tall);
                $wide_count = count($wide);
                $flex_count = count($flex);
                $total = $tall_count + $wide_count + $flex_count;
                $ti = 0;
                $wi = 0;
                $fi = 0;
                $bizarray = [];
                foreach($tall as $t){
                  if($ti % 2 == 0){
                    $the_string = "<div class='biz-row'>";
                  }
                  $the_string .= "<div class='biz'><a href='{$t["link"]}'><img src='{$t["url"]}' alt='{$t["title"]} Logo'></a></div>";
                  if($ti % 2 != 0){
                    $the_string .= "</div>";//Close out the row
                    $bizarray[] = $the_string;
                    $the_string = '';
                  }
                  $ti++;
                }
                foreach($flex as $f){
                    $the_string .= "<div class='biz flex-biz' style='background:{$f["color"]};'><a href='{$t["link"]}'>{$f['title']}</a></div>";
                    if($ti % 2 != 0){
                        $the_string .= "</div>"; //Close out biz-row
                        $bizarray[] = $the_string;
                        $the_string = '';
                        $ti++;
                    } else {
                        $bizarray[] = $the_string;
                    }
                    $the_string = '';
                }
                foreach($wide as $w){
                    $the_string = "<div class='biz'><a href='{$w["link"]}'><img src='{$w["url"]}' alt='{$w["title"]} Logo'></a></div>";
                    $bizarray[] = $the_string;
                    $the_string = '';
                }
                shuffle($bizarray);
                foreach ($bizarray as $b) {
                  echo $b;
                } ?>
            </div>

          <?php dynamic_sidebar('adrotate_widget'); ?>
          <div class="desktop-only block vt-standard" >
            <p>A division of</p>
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vs-logo.png">
          </div>
        </div>
      </div>
    </div>
    <div class="modal-screen"></div>
  </main>
  <?php $build_string = '';
  foreach($no_posts as $no_post){
    $build_string .= $no_post . ',';
  } ?>
  <input type="text" class="hide" name="dealCount" id="dealCount" value="<?php echo $build_string; ?>">
  <input type="number" class="hide" name="category" id="selectedCategory" value="0">
<?php get_footer(); ?>
