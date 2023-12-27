<script>
  jQuery.noConflict();
</script>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var pre_rate = <?php echo $this->item->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var pro_id = <?php echo $this->item->pro_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';

    var rating_over_<?php echo $this->identity;?> = window.rating_over_<?php echo $this->identity;?> = function(rating) {
      if( rated == 1 ) {
        scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate('you already rated');?>");
      } else if( viewer == 0 ) {
        scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate('please login to rate');?>");
      } else {
        scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate('click to rate');?>");
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            scriptJquery('#rate_<?php echo $this->identity;?>_'+x).attr('class', 'rating_star_big_generic rating_star_big');
          } else {
            scriptJquery('#rate_<?php echo $this->identity;?>_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
          }
        }
      }
    }
    
    var rating_out_<?php echo $this->identity;?> = window.rating_out_<?php echo $this->identity;?> = function() {
      if (new_text != ''){
        scriptJquery('#rating_text_<?php echo $this->identity;?>').html(new_text);
      }
      else{
        if(<?php echo $this->rating_count ?> <= 0){
          scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate('0 Rating') ?>");
        }
        else{
          scriptJquery('#rating_text_<?php echo $this->identity;?>').html(" <?php echo $this->translate(array('%s Rating', '%s Ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>");        
        }
      }
      if (pre_rate != 0){
        set_rating();
      }
      else {
        for(var x=1; x<=5; x++) {
          scriptJquery('#rate_<?php echo $this->identity;?>_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }

    var set_rating = window.set_rating = function() {
      var rating = pre_rate;
      if (new_text != ''){
        scriptJquery('#rating_text_<?php echo $this->identity;?>').html(new_text);
      }
      else{
        if(<?php echo $this->rating_count ?> <= 0){
          scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate('0 Rating') ?>");
        }
        else{
          scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate(array('%s Rating', '%s Ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>");
        }
      }
      for(var x=1; x<=parseInt(rating); x++) {
        scriptJquery('#rate_<?php echo $this->identity;?>_'+x).attr('class', 'rating_star_big_generic rating_star_big');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
        scriptJquery('#rate_<?php echo $this->identity;?>_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating)+1;
        scriptJquery('#rate_<?php echo $this->identity;?>_'+last).attr('class', 'rating_star_big_generic rating_star_big_half');
      }
    }

    var rate = window.rate = function(rating) {
      scriptJquery('#rating_text_<?php echo $this->identity;?>').html("<?php echo $this->translate('Thanks for rating!');?>");
      for(var x=1; x<=5; x++) {
        scriptJquery('#rate_<?php echo $this->identity;?>_'+x).attr('onclick', '');
      }
      (scriptJquery.ajax({
        'dataType': 'json',
        'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'rate'), 'default', true) ?>',
        'data' : {
          'format' : 'json',
          'rating' : rating,
          'pro_id': pro_id
        },
        'beforeSend' : function(){
          rated = 1;
          total_votes = total_votes+1;
          pre_rate = (pre_rate+rating)/total_votes;
          set_rating();
        },
        'success' : function(responseJSON)
        { 
          if(responseJSON[0].total <= 1){
            scriptJquery('#rating_text_<?php echo $this->identity;?>').html(responseJSON[0].total+" Rating");
            new_text = responseJSON[0].total+" Rating";
          }else{
            scriptJquery('#rating_text_<?php echo $this->identity;?>').html(responseJSON[0].total+" Ratings");
            new_text = responseJSON[0].total+" Ratings";
          }
        }
      }));

    }
    
    set_rating();
  });
</script>

<div class="sitebooking_view sitebooking_view_container">
  <div id="sitebooking_rating" class="rating" onmouseout="rating_out_<?php echo $this->identity;?>();">
    <span id="rate_<?php echo $this->identity;?>_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(1);"></span>
    <span id="rate_<?php echo $this->identity;?>_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(2);"></span>
    <span id="rate_<?php echo $this->identity;?>_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(3);"></span>
    <span id="rate_<?php echo $this->identity;?>_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(4);"></span>
    <span id="rate_<?php echo $this->identity;?>_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(5);"></span>
    <span id="rating_text_<?php echo $this->identity;?>" class="rating_text"><?php echo $this->translate('click to rate');?></span>
  </div>
 </div>