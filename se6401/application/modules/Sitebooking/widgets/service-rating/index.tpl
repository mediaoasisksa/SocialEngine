<script>
  jQuery.noConflict();
</script>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var pre_rate = <?php echo $this->item->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var ser_id = <?php echo $this->item->ser_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';

    var rating_over_<?php echo $this->identity;?> = window.rating_over_<?php echo $this->identity;?> = function(rating) {
      if( rated == 1 ) {
        $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate('you already rated');?>";
        //set_rating_<?php echo $this->identity;?>();
      } else if( viewer == 0 ) {
        $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate('please login to rate');?>";
      } else {
        $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate('click to rate');?>";
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_<?php echo $this->identity;?>_'+x).set('class', 'rating_star_big_generic rating_star_big');
          } else {
            $('rate_<?php echo $this->identity;?>_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
          }
        }
      }
    }
    
    var rating_out_<?php echo $this->identity;?> = window.rating_out_<?php echo $this->identity;?> = function() {
      if (new_text != ''){
        $('rating_text_<?php echo $this->identity;?>').innerHTML = new_text;
      }
      else{
        if(<?php echo $this->rating_count ?> <= 0){
          $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate('0 Rating') ?>";
        }else{
          $('rating_text_<?php echo $this->identity;?>').innerHTML = " <?php echo $this->translate(array('%s Rating', '%s Ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
        }        
      }
      if (pre_rate != 0){
        set_rating_<?php echo $this->identity;?>();
      }
      else {
        for(var x=1; x<=5; x++) {
          $('rate_<?php echo $this->identity;?>_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }

    var set_rating_<?php echo $this->identity;?> = window.set_rating_<?php echo $this->identity;?> = function() {
      var rating = pre_rate;
      if (new_text != ''){
        $('rating_text_<?php echo $this->identity;?>').innerHTML = new_text;
      }
      else{
        if(<?php echo $this->rating_count ?> <= 0){
          $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate('0 Rating') ?>";
        }else{
          $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate(array('%s Rating', '%s Ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
        }
      }
      for(var x=1; x<=parseInt(rating); x++) {
        $('rate_<?php echo $this->identity;?>_'+x).set('class', 'rating_star_big_generic rating_star_big');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
        $('rate_<?php echo $this->identity;?>_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating)+1;
        $('rate_<?php echo $this->identity;?>_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
      }
    }

    var rate_<?php echo $this->identity;?> = window.rate_<?php echo $this->identity;?> = function(rating) {
      $('rating_text_<?php echo $this->identity;?>').innerHTML = "<?php echo $this->translate('Thanks for rating!');?>";
      for(var x=1; x<=5; x++) {
        $('rate_<?php echo $this->identity;?>_'+x).set('onclick', '');
      }
      (new Request.JSON({
        'format': 'json',
        'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'rate'), 'default', true) ?>',
        'data' : {
          'format' : 'json',
          'rating' : rating,
          'ser_id': ser_id
        },
        'onRequest' : function(){
          rated = 1;
          total_votes = total_votes+1;
          pre_rate = (pre_rate+rating)/total_votes;
          set_rating_<?php echo $this->identity;?>();
        },
        'onSuccess' : function(responseJSON, responseText)
        {
          if(responseJSON[0].total <= 1){
            $('rating_text_<?php echo $this->identity;?>').innerHTML = responseJSON[0].total+" Rating";
            new_text = responseJSON[0].total+" Rating";
          }
          else{
            $('rating_text_<?php echo $this->identity;?>').innerHTML = responseJSON[0].total+" Ratings";
            new_text = responseJSON[0].total+" Ratings";  
          }
        }
      })).send();

    }
    
    set_rating_<?php echo $this->identity;?>();
  });
</script>

<div class="sitebooking_view sitebooking_view_container">
  <div id="sitebooking_rating" class="rating" onmouseout="rating_out_<?php echo $this->identity;?>();">
    <span id="rate_<?php echo $this->identity;?>_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate_<?php echo $this->identity;?>(1);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(1);"></span>
    <span id="rate_<?php echo $this->identity;?>_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate_<?php echo $this->identity;?>(2);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(2);"></span>
    <span id="rate_<?php echo $this->identity;?>_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate_<?php echo $this->identity;?>(3);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(3);"></span>
    <span id="rate_<?php echo $this->identity;?>_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate_<?php echo $this->identity;?>(4);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(4);"></span>
    <span id="rate_<?php echo $this->identity;?>_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate_<?php echo $this->identity;?>(5);"<?php endif; ?> onmouseover="rating_over_<?php echo $this->identity;?>(5);"></span>
    <span id="rating_text_<?php echo $this->identity;?>" class="rating_text"><?php echo $this->translate('click to rate');?></span>
  </div>
 </div>