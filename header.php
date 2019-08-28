<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <link rel="manifest" href="<?php echo home_url(); ?>/manifest.json">
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <!-- Add to home screen for Safari on iOS -->
  <link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/Do802.com_logo-egg-152.png">
  <meta name="msapplication-TileImage" content="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/Do802.com_logo-egg-144.png">
  <meta name="msapplication-TileColor" content="#2F3BA2">
  <?php wp_head(); ?>
  <script>
    navigator.serviceWorker.register('/OneSignalSDKWorker.js');
    window.OneSignal = window.OneSignal || [];

      OneSignal.push( function() {
        OneSignal.SERVICE_WORKER_UPDATER_PATH = "OneSignalSDKUpdaterWorker.js";
        OneSignal.SERVICE_WORKER_PATH = "OneSignalSDKWorker.js";
        OneSignal.SERVICE_WORKER_PARAM = { scope: '/' };

        OneSignal.setDefaultNotificationUrl("https://do802.com/");
        var oneSignal_options = {};
        window._oneSignalInitOptions = oneSignal_options;

        oneSignal_options['wordpress'] = true;
        oneSignal_options['appId'] = "e4839f01-0849-49c7-9a9a-59eeda0cbe0d";
        oneSignal_options['autoRegister'] = true;
        oneSignal_options['welcomeNotification'] = { };
        oneSignal_options['welcomeNotification']['title'] = "Do 802";
        oneSignal_options['welcomeNotification']['message'] = "Thanks for subscribing!";
        oneSignal_options['path'] = "https://do802.com/";
        oneSignal_options['safari_web_id'] = "web.onesignal.auto.48e79a0f-c6ba-48bf-ae98-32501b011889";
        oneSignal_options['promptOptions'] = { };
        oneSignal_options['notifyButton'] = { };
        oneSignal_options['notifyButton']['enable'] = true;
        oneSignal_options['notifyButton']['position'] = 'bottom-right';
        oneSignal_options['notifyButton']['theme'] = 'default';
        oneSignal_options['notifyButton']['size'] = 'medium';
        oneSignal_options['notifyButton']['prenotify'] = true;
        oneSignal_options['notifyButton']['showCredit'] = false;
              OneSignal.init(window._oneSignalInitOptions);
                    });

      function documentInitOneSignal() {
        var oneSignal_elements = document.getElementsByClassName("OneSignal-prompt");

        var oneSignalLinkClickHandler = function(event) { OneSignal.push(['registerForPushNotifications']); event.preventDefault(); };        for(var i = 0; i < oneSignal_elements.length; i++)
          oneSignal_elements[i].addEventListener('click', oneSignalLinkClickHandler, false);
      }

      if (document.readyState === 'complete') {
           documentInitOneSignal();
      }
      else {
           window.addEventListener("load", function(event){
               documentInitOneSignal();
          });
      }
  </script>
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-KFNLLD4');</script>
  <!-- End Google Tag Manager -->
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-108066679-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-108066679-1');
  </script>
  <!-- Google Tag Manager (noscript) -->
</head>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KFNLLD4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<body <?php body_class(); ?>>

<div class="hfeed site" id="page">
<?php if(!is_page('calendar')){
  $button_image = get_stylesheet_directory_uri() . '/images/LiveDeals_Black_Vert.png';
  $header_image = get_stylesheet_directory_uri() . '/images/LiveDeals_White_Line.png';
  $button_url = home_url();
  $button_text = "Click for Deals!";
  $header_class = 'deals-header';
}
if(is_front_page()){
  // $header_image = get_stylesheet_directory_uri() . '/images/LiveDeals_White_Line.png';
  $button_image = get_stylesheet_directory_uri() . '/images/do802calendar.png';
  $button_url = home_url() . '/calendar';
  $button_text = "Click for Events!";
  $header_class = 'deals-header';
} 
elseif(is_page('calendar')) {
  $header_image = get_stylesheet_directory_uri() . '/images/This_Week_White.png';
  $button_image = get_stylesheet_directory_uri() . '/images/LiveDeals_Black_Vert.png';
  $header_class = "calendar-header";
  $button_url = home_url();
  $button_text = "Click for Deals!";
} ?>
  <!-- ******************* The Navbar Area ******************* -->
  <header class="header">
    <h1 class="header__title" hidden>Do802</h1>
    <div class="container flex">
      <a href="<?php echo home_url(); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/do802logo.png" id="logo" class="<?php echo $header_class; ?>"></a>
      <div class="navToggle desktop-only" id="flexburger">
        <a href="#" id="navToggle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hamburger.png"></a>
      </div>
      <div id="live-logo"><img src="<?php echo $header_image; ?>"></div>
      <div class="header-btn" id="cal-btn-container">
        <a href="<?php echo $button_url; ?>" class='header-btn-link'><img src="<?php echo $button_image; ?>" id="calendar-button" ></a>
        <p class="header-btn-label"><?php echo $button_text; ?></p>
      </div>
  </div>
  </header>
  <?php if(is_front_page() || is_page('calendar')){ ?>
    <div class="white wrapper <?php if(is_front_page()){ echo 'mobile-only';} ?>">
      <?php if(is_front_page()){?>
        <div class="subhead deals-subhead active clearfix">
          <a href="#" id="navToggle" class="navToggle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hamburger.png"></a>
          <div id="vs-standard">
            <p>A division of</p>
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vs-logo.png">
          </div>
        </div>
      <?php } elseif(is_page('calendar')) {?>
        <div class="subhead calendar-subhead">
          <ul>
            <li id="dayCal"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/calendar.png" ></li>
            <li><button data-target="mustDo" href="#mustDo" class="calSwitch">Must DO's</button></li>
            <li><button data-target="allEvents" href="#allEvents" class="calSwitch">All Events</button></li>
            <li><a href="https://events.time.ly/z7kziwq/fes" class="green-button" target="_blank">Submit Event</a></li>
          </ul>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
