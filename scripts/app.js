//Here is the masterpiece
(function($) {
  'use strict';

  var app = {
    isLoading: true,
    visibleCards: {},
    visibleDeals: {},
    selectedCities: [],
    spinner: document.querySelector('.loader'),
    cardTemplate: document.querySelector('.cardTemplate'),
    dealTemplate: document.querySelector('.dealTemplate'),
    calTemplate: document.querySelector('.calTemplate'),
    container: document.querySelector('.main'),
    addDialog: document.querySelector('.dialog-container'),
    daysOfWeek: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    rootURL: 'https://do802.com',
    themeURL: 'https://do802.com/wp-content/themes/do802'
  };

  // Get the site categories and parse them into an array that works better for our purposes!

  const categoriesParsed = [];
  var categoriesRequest = new XMLHttpRequest();
  categoriesRequest.onreadystatechange = function() {
    if(categoriesRequest.readyState === XMLHttpRequest.DONE) {
      if(categoriesRequest.status == 200) {
        var categoriesList = JSON.parse(categoriesRequest.response);
        for(var i = 0, len = categoriesList.length; i<len; i++){
          categoriesParsed[categoriesList[i].id] = {
            catName: categoriesList[i].name,
          };
        }

      }
    }
  }

  categoriesRequest.open('GET', app.rootURL + '/wp-json/wp/v2/categories');
  categoriesRequest.send();

  categoriesRequest.getTheCats = function(data) {
    for (var i = 0, len = categoriesList.length; i < len; i++) {
      newKey = categoriesList[i].id;
      categoriesParsed[newKey].catName = categoriesList[i].name;
      categoriesParsed[newKey].catSlug = categoriesList[i].slug;
      categoriesParsed[newKey].catCount = categoriesList[i].count;
    }
    return categoriesParsed;
    console.log(categoriesParsed);
  }

  // var cleanCats = app.getTheCats(categoriesList);
  /*****************************************************************************
   *
   * Event listeners for UI elements
   *
   ****************************************************************************/



  /*****************************************************************************
   *
   * Methods to update/refresh the UI
   *
   ****************************************************************************/

   app.updateDealCard = function(data) {
    for (var i = 0, len = data.length; i < len; i++) {
      someFn(arr[i]);
    }
    var dataLastUpdated = new Date(data.modified);
   }

  // Updates a weather card with the latest weather forecast. If the card
  // doesn't already exist, it's cloned from the template.
  app.updateForecastCard = function(data) {
    var dataLastUpdated = new Date(data.created);
    var sunrise = data.channel.astronomy.sunrise;
    var sunset = data.channel.astronomy.sunset;
    var current = data.channel.item.condition;
    var humidity = data.channel.atmosphere.humidity;
    var wind = data.channel.wind;

    var card = app.visibleCards[data.key];
    if (!card) {
      card = app.cardTemplate.cloneNode(true);
      card.classList.remove('cardTemplate');
      card.querySelector('.location').textContent = data.label;
      card.removeAttribute('hidden');
      app.container.appendChild(card);
      app.visibleCards[data.key] = card;
    }

    // Verifies the data provide is newer than what's already visible
    // on the card, if it's not bail, if it is, continue and update the
    // time saved in the card
    var cardLastUpdatedElem = card.querySelector('.card-last-updated');
    var cardLastUpdated = cardLastUpdatedElem.textContent;
    if (cardLastUpdated) {
      cardLastUpdated = new Date(cardLastUpdated);
      // Bail if the card has more recent data then the data
      if (dataLastUpdated.getTime() < cardLastUpdated.getTime()) {
        return;
      }
    }
    cardLastUpdatedElem.textContent = data.created;

    card.querySelector('.description').textContent = current.text;
    card.querySelector('.date').textContent = current.date;
    card.querySelector('.current .icon').classList.add(app.getIconClass(current.code));
    card.querySelector('.current .temperature .value').textContent =
      Math.round(current.temp);
    card.querySelector('.current .sunrise').textContent = sunrise;
    card.querySelector('.current .sunset').textContent = sunset;
    card.querySelector('.current .humidity').textContent =
      Math.round(humidity) + '%';
    card.querySelector('.current .wind .value').textContent =
      Math.round(wind.speed);
    card.querySelector('.current .wind .direction').textContent = wind.direction;
    var nextDays = card.querySelectorAll('.future .oneday');
    var today = new Date();
    today = today.getDay();
    for (var i = 0; i < 7; i++) {
      var nextDay = nextDays[i];
      var daily = data.channel.item.forecast[i];
      if (daily && nextDay) {
        nextDay.querySelector('.date').textContent =
          app.daysOfWeek[(i + today) % 7];
        nextDay.querySelector('.icon').classList.add(app.getIconClass(daily.code));
        nextDay.querySelector('.temp-high .value').textContent =
          Math.round(daily.high);
        nextDay.querySelector('.temp-low .value').textContent =
          Math.round(daily.low);
      }
    }
    if (app.isLoading) {
      // app.spinner.setAttribute('hidden', true);
      app.container.removeAttribute('hidden');
      app.isLoading = false;
    }
  };


  /*****************************************************************************
   *
   * Methods for dealing with the model
   *
   ****************************************************************************/

  var offset = 0;
  app.getDeals = function(offset) {
    var url = app.rootURL + '/wp-json/wp/v2/deals?offset=' + offset;
    if('caches' in window) {
      caches.match(url).then(function(response) {
        if(response) {
          response.json().then(function updateFromCache(json) {
            // var results = json.query.results;

          })
        }
      })
    }
   //Fetch latest deals
    var dealRequest = new XMLHttpRequest();
    var theDeal = '';
    dealRequest.onreadystatechange = function() {
      if(dealRequest.readyState === XMLHttpRequest.DONE) {
        if(dealRequest.status === 200) {
          var dealResponse = JSON.parse(dealRequest.response);
            console.log(dealResponse);
          var deal = {};
          for (var i = 0, len = dealResponse.length; i < len; i++) {
            offset ++;
            deal[i] = app.visibleDeals[dealResponse[i].id];
            $('.dealTemplate').clone(true).insertBefore('.dealTemplate').removeAttr('hidden').removeClass('dealTemplate').attr({'id': 'deal-' + offset, "data-dealID": dealResponse[i].id});
            $('#deal-' + offset + ' .deal-business').html(dealResponse[i].acf.associated_business[0]['post_title']).css({'background': "#074b7e", "color": "white"});
            $('#deal-' + offset + ' .deal-title').html(dealResponse[i].title['rendered']);
            $('#deal-' + offset + ' .deal-subtext').html(dealResponse[i].acf.the_deal_subtitle);

            // Function for finding days between two dates
            // function showDays(dealEnd){
            //     var now = moment();
            //      var days = moment(dealEnd);

            //      // Round down.
            //      console.log(days);
            //    if (now.isAfter(days)){
            //      $('#deal-'+ offset +' .stopwatch').addClass('inactive');
            //      console.log(days);
            //      return "You Missed It";
            //   } else if (now.isSame(days)){
            //       return "Ending Today!";
            //     } else {
            //       return days.fromNow(true) + " Left";
            //    }
            //  }
            //  End that day finder function

            // $('#deal-'+ offset + ' .stopwatch').html(showDays(dealResponse[i].acf.deal_end_date));
            var theCategory = dealResponse[i].categories[0];
            $('#deal-' + offset + ' .modal-trigger').attr({'data-category': theCategory});
            $('#deal-' + offset + ' .modal-trigger').attr({'data-details': escapeHtml(dealResponse[i].acf.deal_details), 'data-title': dealResponse[i].title['rendered'], 'data-business': dealResponse[i].acf.associated_business[0]['post_title'], 'data-subtitle':
            escapeHtml(dealResponse[i].acf.the_deal_subtitle), 'data-claim': escapeHtml(dealResponse[i].acf.to_claim),
            "data-dealID": dealResponse[i].id});
            // var interval = setInterval(function() {
            //   if(typeof(categoriesParsed) == 'undefined' && typeof(interval) == 'undefined') return;
            //   clearInterval(interval);
            //   console.log('Defined!');
              // curDealCat = deal[i].categories[0];
              // deal[i].querySelector('.deal-business').classList.add(categoriesParsed[curDealCat].name);
            // }, 10);
            //
            //
            // deal[i].setAttribute('data-details', dealResponse[i].acf.deal_details);
            // deal[i].setAttribute('data-claim', dealResponse[i].acf.to_claim);
            // deal[i].querySelector('.deal-subtext').textContent = dealResponse[i].acf.the_deal_subtitle;
             //Grab the first category and use it for coloring and such...
            // deal[i].classList.add("'" + categoriesParsed[curDealCat].slug + "'");
          }
          $('#dealCount').val(offset);
        }
      } else {

      }
    }
    dealRequest.open('GET', url);
    dealRequest.send();
  }

  /*
   * Gets a forecast for a specific city and updates the card with the data.
   * getForecast() first checks if the weather data is in the cache. If so,
   * then it gets that data and populates the card with the cached data.
   * Then, getForecast() goes to the network for fresh data. If the network
   * request goes through, then the card gets updated a second time with the
   * freshest data.
   */
  
   
  


  //make a modal
  $(document).ready(function() {
    //Load the first round of deals
    $.ajax({
              url: ajaxpagination.ajaxurl,
              type: 'post',
              data: {
                  action: 'ajax_pagination',
                  page: null,
                  category: null,
              },
          success: function(result){
              $('#more').remove();
              $('#deals-container').append(result);
              $(".deal").each(function(e) {
                  var curVal = $("#dealCount").val();
                  var addVal = $(this).data('dealid');
                  $("#dealCount").val(curVal + addVal + ',');
                  var newString = curVal + addVal + ',';
              });
          }
          });
    //Gonna make a modal!
    $(document).on('click','.modal-trigger', function() {
      $('#details').addClass('active');
      $('.modal-screen').addClass('active');
      $('.details-title').html("<h3>" + $(this).data('title') + "</h3>");
      var theSubtitle = $(this).data('subtitle');
      $('.details-subtitle').html(decodeEntities(theSubtitle));
      $(".details-expiry").html("Expires " + $(this).data("expiry"));
      var toClaim = $(this).data('claim');
      $('.details-business').html("<h2 class='business-title'>" + $(this).data('business') + "</h2>");
      var remember = $("#" + $(this).closest('.deal').attr('id') + " .stopwatch").clone();
      // console.log($(this).closest('.deal').attr(id));
      $('.details-remaining').html(remember);
      $('.details-claim').html("<p><span class='bold'>To Redeem:</span> " + decodeEntities(toClaim) + "</p>");
      var theDetails = $(this).data('details');
      $('.details-details').html(decodeEntities(theDetails));
      var theRestrictions = $(this).data('deal-restrictions');
      if(theRestrictions != undefined && theRestrictions !='') {
        $('.details-restrictions').html("<p><span class='text-red bold'>RESTRICTIONS:</span></p> " + decodeEntities(theRestrictions));
      }
      else {
        $('.details-restrictions').html("");
      }
      var thisID = $(this).data('dealid');
      var businessAddress = $(this).data('business-address');
      var realAddress = '';
      if(businessAddress != undefined && businessAddress != '') {
        var realAddress = "<a href=http://maps.google.com/maps?q='" + decodeEntities(businessAddress) + "' target='_blank'>Directions</a> | ";
      }
      var businessPage = $(this).data('business-page');
      var businessURL = '';
      if(businessPage != undefined){
        businessURL = " <a href='" + businessPage + "'>More From This Business</a>";
      }
      $('.details-print').html("<p><a href='javascript:window.print()''>Print</a> |" + realAddress +  businessURL + "</p>")
      var businessLogo = $(this).data('business-logo');
      if(businessLogo != undefined){
        $('.details-image').html("<img src='" + businessLogo + "'>");
        console.log("BIZ " + businessLogo);
      } else {
        $.ajax( {
          url: app.rootURL + '/wp-json/wp/v2/deals/' + thisID,
          success: function ( data ) {
            var imgURL = data.better_featured_image.source_url;
            if(imgURL){
              $('.details-image').html("<img src='" + imgURL + "'>");
            }
            else {
              $('.details-image').empty();
            }
            // If the Source is available, use it. Otherwise hide it.

          },
          cache: false
        } );
      }
    });
    $('.close, .modal-screen').click(function() {
      $('#details').removeClass('active');
      $('.modal-screen').removeClass('active');
      $(".details-image").empty();
    });
    $(document).keyup(function(e) {
      if(e.keyCode == 27) {
        $('#details').removeClass('active');
        $('.modal-screen').removeClass('active');
      }
    });
    var today = new Date();
    var dd = today.getDate();
    $('.calendar-subhead #dayCal').addClass('date-' + dd);
    //Toggle Category menu
    $('.navToggle').click(function() {
      $('#categories').addClass('show');
    });
    $('.closer').click(function() {
      $('#categories').removeClass('show');
    });
    $("#categories").on('change', 'select', function() {
        $("#deals-container").removeClass('hide');
        $("#businesses").removeClass("reveal");
        if($(this).find(":selected").val() != "all" && $(this).find(":selected").val() != "merchants"){
          var catNum = $(this).find(":selected").data('id');
          var catName = $(this).find(":selected").data('name');
          $('.deal:not(.dealTemplate)').addClass('hide');
          $('.deal[data-category=' + catNum + ']').removeClass('hide');
          $('#categories').removeClass("show");
          $("#selectedCategory").val(catNum);
          $('#filter').remove();
          $('#deals-container').prepend('<h2 id="filter">Category: ' + catName + '</h2>');
          countDeals();
          var dealCount = $("#dealCount").val();
          event.preventDefault();
          $.ajax({
              url: ajaxpagination.ajaxurl,
              type: 'post',
              data: {
                  action: 'ajax_pagination',
                  page: dealCount,
                  category: $('#selectedCategory').val(),
              },
          success: function(result){
              $('#more').remove();
              $('#deals-container').append(result);
              $(".deal").each(function(e) {
                  var curVal = $("#dealCount").val();
                  var addVal = $(this).data('dealid');
                  $("#dealCount").val(curVal + addVal + ',');
                  var newString = curVal + addVal + ',';
              });
          }
          });
        } else if($(this).find(":selected").val() == "all"){
          $("#deals-container").removeClass('hide');
          $("#businesses").removeClass("reveal");
          $('.deal').removeClass('hide');
          $('#categories').removeClass('show');
          $("#selectedCategory").val(null);
          $("#filter").remove();
          $('#deals-container').prepend('<h2 id="filter" >All Deals</h2>');
          countDeals();
        } else {
        	$("#deals-container").addClass('hide');
        	$("#businesses").addClass('reveal');
        	$('#categories').removeClass('show');
        }
    });
    $("#categories").on('click', 'option.all' ,function() {
    });
    $("#categories").on("click", "option.merchants", function() {
    });
    function countDeals(){
      // var dealCount = $('.deal:not(.hide)').length;
      // $("#dealCount").val(dealCount);
      if($("#more").length == 0){
        $("#deals-container").append("<span id='more'>Load More</span>");
      }
    }
    function refreshNoDeals(){
      $(".deal").each(function(e) {
        var val = $("#countDeals").val();
        var addVal = $(this).data('dealID');
        $("#countDeals").val(val + addVal + ',');
      });
    }
    //GET MORE DEALS!
    $(document).on('click', '#more', function(event) {
      var dealCount = $("#dealCount").val();
      event.preventDefault();
      $.ajax({
        url: ajaxpagination.ajaxurl,
        type: 'post',
        data: {
          action: 'ajax_pagination',
          page: dealCount,
          category: $('#selectedCategory').val(),
        },
        success: function(result){
          $('#more').remove();
          $('#deals-container').append(result);
          $('#dealCount').val('');
          $(".deal").each(function(e) {
            var curVal = $("#dealCount").val();
            var addVal = $(this).data('dealid');
            $("#dealCount").val(curVal + addVal + ',');
            var newString = curVal + addVal + ',';
          });
        }
      });
    });
    //SWITCH THE CALENDAR!
    $(".calSwitch").click(function() {
      var calTarget = $(this).data('target');
      $(".calendar").addClass("hide");
      $("." + calTarget).removeClass("hide");
    });
  });






  function inWindow(element) {
    var elementTop = $(element).offset().top;
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();
    return elementTop < viewportBottom;
  };

  var entityMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
    '/': '&#x2F;',
    '`': '&#x60;',
    '=': '&#x3D;'
  };

  function escapeHtml (string) {
    return String(string).replace(/[&<>"'`=\/]/g, function (s) {
      return entityMap[s];
    });
  }

  function decodeEntities(encodedString) {
      var textArea = document.createElement('textarea');
      textArea.innerHTML = encodedString;
      return textArea.value;
  }

  function timeLeft(endDate, endTime) {
    //Get the current date
    if(endDate != '' && endTime != ''){

      var d = new Date();
      var month = d.getMonth()+1;
      var day = d.getDate();
      var dateNow = d.getFullYear() + '/' +
        (month<10 ? '0' : '') + month + '/' +
        (day<10 ? '0' : '') + day;
      //Compare to end date
      var dateDiff = endDate - dateNow;
      if(dateDiff > 0){
      } else {
        return dateDiff + " days";
      }
      var hours = parseInt($(".Time2").val().split(':')[0], 10) - parseInt($(".Time1").val().split(':')[0], 10);

      // if negative result, add 24 hours
      if(hours < 0) hours = 24 + hours;
    }
  }
  //Update the submission form to create the actual end date time :-)
  $(document).on('change', '#input_4_21, #input_4_33_1, #input_4_33_2, #input_4_33_3',  function() {
  // $(document).on('change', "#input_4_21", function() {
    $(".expiry").html("Expires " + $("#input_4_21").val());
  // });
    var endDate = $('#input_4_21').val().split('/');
    var endTime = $("#input_4_33_1").val();
    var endMin = $("#input_4_33_2").val();
    var endMer = $("#input_4_33_3").val();
    var displayTime = parseInt(endTime) + ":" + endMin + " " + endMer;
    $(".time").html(displayTime);
    if(endMer == 'pm' || endMer == 'PM'){
     var endTime = parseInt(endTime)+12;
    }
    else {
      endTime = endTime;
    }
    var endDateTime = endDate[2] + '-' + endDate[0] + '-' + endDate[1] + ' ' + endTime + ':' + endMin + ':' + '00';
    $('#input_4_4').val(endDateTime);
    console.log(endDateTime);
  });
  //CREATE THE SAMPLE DEAL ON THE SUBMISSION PAGE
  $(document).on('input', "#input_4_29", function() {
    $("#input_4_2").val($(this).val());
    $(".deal-title").text($(this).val());
    $(".details-link").data('title', $(this).val());
  });
  $(document).on('input', "#input_4_9", function() {
    $(".deal-subtext").text($(this).val());
    $(".details-link").data('subtitle', $(this).val());
  });
  $(document).on('input', "#input_4_8", function() {
    $(".details-link").data('business', $(this).find(":selected").text());
  });
  $(document).on('input', "#input_4_6", function() {
    $(".details-link").data('details',escapeHtml($(this).val()));
  });
  $(document).on('input', "#input_4_25", function() {
    $(".details-link").data('deal-restrictions',escapeHtml($(this).val()));
  });
  $(document).on('input', "#input_4_7", function() {
    $(".details-link").data('claim',escapeHtml($(this).val()));
  });
  $(document).on('input', "#input_4_21", function() {
    var expiry = $(this).val();
    if(~expiry.toLowerCase().indexOf("undefined")){
      //Just do nothing
    }
    else {
      //Start modifying the date in the preview
      var dateBreak = expiry.split(/[\s\-]+/g);
      console.log(dateBreak[0]);

    }
  });
  //Ajax call to update the logo on the deal preview
  
  $( document ).ready(function() {

    var $sticky = $('.sticky');
    var $stickyrStopper = $('.sticky-stopper');
    if (!!$sticky.offset() && $("body").width() > 768) { // make sure ".sticky" element exists

      var generalSidebarHeight = $sticky.innerHeight();
      var stickyTop = $sticky.offset().top;
      var stickOffset = 0;
      var stickyStopperPosition = $stickyrStopper.offset().top;
      var stopPoint = stickyStopperPosition - generalSidebarHeight - stickOffset;
      var diff = stopPoint + stickOffset;

      $(window).scroll(function(){ // scroll event
        var windowTop = $(window).scrollTop(); // returns number

        if (stickyTop < windowTop+stickOffset) {
            $sticky.css({ position: 'fixed', top: stickOffset,"max-width" : "730px", width: '65%' });
        } else {
            $sticky.css({position: 'relative', top: 'initial', width: '100%'});
        }
      });

    }
  });
  //Editing a post on the front end
  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }
  if(getParameterByName('edit_deal') != null) {
    var dealID = getParameterByName('edit_deal');
    $.ajax( {
	    url: ajaxpagination.root + 'wp/v2/deals/' + dealID,
	    method: 'GET',
	}).done(function(response) {
	    $("#input_4_8").val(response.acf.associated_business[0].ID);
	    if(response.acf.use_logo[0] == "true"){
	    	$("#choice_4_27_1").trigger("click");
	    }
	    $("#input_4_10 option[value=" + response.categories[0] + "]").attr("selected", "selected");
	});
    function get_featured_img(){
	    $.ajax( {
	        url: ajaxpagination.root + 'wp/v2/deals/' + dealID,
	        method: 'GET',
	    }).done(function(response) {
	       	$("#loader").remove();
	        $("#input_4_8").val(response.acf.associated_business[0].ID);
	        if(response.better_featured_image != 'undefined') {
		    	$("#field_4_11").append("<img src='" + response.better_featured_image.source_url + "' id='featured_thumb' style='margin-top:10px;'>");
		    }
	    });
	    
    }
    get_featured_img();
    //Manage new featured image
    $("#input_4_11").click(function() {
    	console.log("clickered");
    	$(this).blur;
    	$(this).focus;
    });
    $(document).on("change", "#input_4_11", function(){
		$("#featured_thumb").remove();
    	$("#field_4_11").append("<p id='loader'>Loading...</p>");
    	var file = jQuery( '#input_4_11' )[0].files[0];
		var formData = new FormData();
		var newTimestamp = + new Date();
		formData.append( 'file', file );
		formData.append( 'title', $("#input_4_29").val() + "_image" + newTimestamp );
		formData.append( 'caption', '' );

		// Fire the request.
		jQuery.ajax( {
			url: "/wp-json/wp/v2/media",
			method: 'POST',
			processData: false,
			contentType: false,
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', ajaxpagination.nonce );
			},
			data: formData
		} ).success( function ( response ) {
			var featuredID = response.id;
			$.ajax( {
				url: ajaxpagination.root + 'wp/v2/deals/' + dealID,
	            method: 'POST',
	            beforeSend: function ( xhr ) {
	                xhr.setRequestHeader( 'X-WP-Nonce', ajaxpagination.nonce );
	            },
	            data:{
	            	"featured_media": featuredID
	            }
	        }).done( function ( response ) {
	        	get_featured_img();
	        	$("#loader").remove();
	        });
		} ).error( function( response ) {
			$("#field_4_11").append("<p class='text-red'>Something went wrong...</p>");
			console.log( response );

		});
    });
    //Update the deal
    $(document).on( "keypress", ".gform_wrapper", function(e) {
      var code = e.keyCode || e.which;
      if ( code == 13 && ! $( e.target ).is( 'textarea,input[type="submit"],input[type="button"]' ) ) {
          e.preventDefault();
          return false;
      }
    });
    $(document).on("click", "#gform_submit_button_4", function(e) {
        e.preventDefault();
        var dealID = getParameterByName('edit_deal');
        var meridian = $("#input_4_32_3").val();
        var hour = parseInt($("#input_4_32_1").val());
        var minute = String(parseInt($("#input_4_32_2").val()));
        if(meridian == "pm"){
          hour += 12;
        } 
        var gmt_hour = hour + 5;
        if(gmt_hour >= 24){
          gmt_hour -= 24;
        }
        if(hour == 24) {
          hour = "00";
        }
        if(gmt_hour == 24) {
          gmt_hour = "00";
        }
        hour = String(hour);
        gmt_hour = String(gmt_hour);
        if(hour.length == 1) {
          hour = "0" + hour;
        }
        if(gmt_hour.length == 1) {
          gmt_hour = "0" + gmt_hour;
        }
        if(minute.length == 1){
          minute = "0" + minute;
        }
        var startDate = $("#input_4_14").val();
        var dateObject = startDate.split("/");
        var curTime = hour + ":" + minute + ":00";
        var gmtTime = gmt_hour + ":" + minute + ":00";
        var start = dateObject[2] + "-" + dateObject[0] + "-" + dateObject[1] + " ";
        var startTime = start + curTime;
        var gmtStart = start + gmtTime;
        console.log(startTime);
        console.log(gmtStart);
        if($("#choice_4_27_1").is(":checked")) {
          var useLogo = [true]
        }
        else {
          var useLogo = "";
        }
        console.log(useLogo);
        $.ajax( {
            url: ajaxpagination.root + 'wp/v2/deals/' + dealID,
            method: 'POST',
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', ajaxpagination.nonce );
            },
            data:{
                'title' : $("#input_4_29").val(),
                'date' : startTime,
                'date_gmt' : gmtStart,
                'edit_date' : "true",
                'categories' : [$("#input_4_10").val()],
                "acf_fields" : {
                  "associated_business" : $("#input_4_8").val(),
                  "the_deal_subtitle" : $("#input_4_9").val(),
                  "deal_end_date" : $("#input_4_4").val(),
                  "use_logo" : useLogo,
                  "deal_details" : $("#input_4_6").val(),
                  "deal_restrictions" : $("#input_4_25").val(),
                  "to_claim" : $("#input_4_7").val(),
                }
            }
            }).done( function ( response ) {
                $("#notice").html("<h3>Your Deal Has Been Updated</h3>");
                $("#notice").addClass("show");
                setTimeout(function(){ 
                  $("#notice").removeClass("show"); 
                }, 2500);
            });
        });

  }
    //Update editable posts
    $(document).on("change", "#editable-businesses", function() {
      console.log($(this).val());
      var biz = [$(this).val()];
      $.ajax({
        url: ajaxpagination.ajaxurl,
        type: 'POST',
        data: {
            action: 'get_editable_posts',
            userID : $("#editable-deals").data("user"),
            biz : biz,
        },
        success: function(result){
          $("#editable-deals").html(result);
        }
      });

  });
})(jQuery);