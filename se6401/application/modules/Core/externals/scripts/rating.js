
en4.core.runonce.add(function() {
  
  var rating_over = window.rating_over = function(rating) {
    if(rated == 1 ) {
      scriptJquery('#rating_text').html(en4.core.language.translate('you already rated'));
      //set_rating();
    } else if( viewer == 0 ) {
      scriptJquery('#rating_text').html(en4.core.language.translate('please login to rate'));
    } else {
      scriptJquery('#rating_text').html(en4.core.language.translate('click to rate'));
      for(var x=1; x<=5; x++) {
        if(x <= rating) {
          scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big ' + ratingIcon);
        } else {
          scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled ' + ratingIcon);
        }
      }
    }
  }
  
  var rating_out = window.rating_out = function() {
    if (new_text != ''){
      scriptJquery('#rating_text').html(new_text);
    }
    else{
      scriptJquery('#rating_text').html(rating_text);
    }
    if (pre_rate != 0){
      set_rating();
    }
    else {
      for(var x=1; x<=5; x++) {
        scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled ' + ratingIcon);
      }
    }
  }
  
  var set_rating = window.set_rating = function() {
    var rating = pre_rate;
    if (new_text != ''){
      scriptJquery('#rating_text').html(new_text);
    }
    else{
      scriptJquery('#rating_text').html(rating_text);
    }
    for(var x=1; x<=parseInt(rating); x++) {
      scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big ' + ratingIcon);
    }
    
    for(var x=parseInt(rating)+1; x<=5; x++) {
      scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled ' + ratingIcon);
    }
    
    var remainder = Math.round(rating)-rating;
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      scriptJquery('#rate_'+last).attr('class', 'rating_star_big_generic rating_star_big_half ' + ratingIcon);
    }
  }
  
  var rate = window.rate = function(rating) {
    scriptJquery('#rating_text').html(en4.core.language.translate('Thanks for rating!'));
    for(var x=1; x<=5; x++) {
      scriptJquery('#rate_'+x).attr('onclick', '');
    }
    rated = 1;
    total_votes = total_votes+1;
    pre_rate = (pre_rate+rating)/total_votes;
    set_rating();
    
    if(modulename == 'music') {
      var URL = en4.core.baseUrl + modulename + '/rate/';
    } else {
      var URL = en4.core.baseUrl + modulename + '/index/rate';
    }

    (scriptJquery.ajax({
      format: 'json',
      url : URL,
      data : {
        format : 'json',
        rating : rating,
        resource_id: resource_id,
        resource_type: resource_type,
      },
      success : function(responseJSON, responseText) {
        scriptJquery('#rating_text').html(responseJSON[0].total+" ratings");
        new_text = responseJSON[0].total+" ratings";
      }
    }));
  }
  set_rating();
});
