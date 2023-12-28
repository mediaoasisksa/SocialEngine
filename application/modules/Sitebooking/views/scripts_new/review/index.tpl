<div class = "global_form_popup">
  <h3 style="text-transform: capitalize;">
      <?php echo $this->title;?>
  </h3>
  <div style="margin-bottom: 2%;">
      <?php echo "Rating"; ?>
  </div>
<div>    
<script type="text/javascript">
  en4.core.runonce.add(function() {

    var pre_rate = "<?php echo $this->pre_rate;?>";
    var rated = '<?php echo $this->rated;?>';
    var id = <?php echo $this->id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';


    var rating_over = window.rating_over = function(rating) {
      if( rated == 1 ) {
        $('rating_text').innerHTML = "<?php echo $this->translate('you already rated');?>";
      } else if( viewer == 0 ) {
        $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate');?>";
      } else {
        $('rating_text').innerHTML = "<?php echo $this->translate('click to rate');?>";
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
          } else {
            $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
          }
        }
      }
    }
    
    var rating_out = window.rating_out = function() {

      if( rated == 1 ) {
          $('rating_text').innerHTML = "<?php echo $this->translate('you already rated');?>"; 
      }
            
        if (pre_rate != 0){
          set_rating();
        }
        else {
          for(var x=1; x<=5; x++) {
            $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
          }
        }
      }

      var set_rating = window.set_rating = function() {
      var rating = pre_rate;
      if( rated == 1 ) {
          $('rating_text').innerHTML = "<?php echo $this->translate('you already rated');?>"; 
      }

      for(var x=1; x<=parseInt(rating); x++) {
          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
          var last = parseInt(rating)+1;
          $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
      }
    }

    var rate = window.rate = function(rating) {
        document.getElementById("rating").value = rating
        pre_rate = rating;
        set_rating();
    }
    
    set_rating();
  });
</script>

<div class="sitebooking_view sitebooking_view_container">
  <div id="sitebooking_rating" class="rating" onmouseout="rating_out();">
    <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
    <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
    <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
    <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
    <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
  </div>
 </div>

<?php 

    $this->form->addElement('Hidden', 'rating', array(
      'required' => true,
      'order' => '994'
    ));
    $this->form->addElement('Hidden', 'subject', array(
      'value' => $this->item->getGuid(),
      'order' => '995'
    ));
    $this->form->addElement('Hidden', 'ser_id', array(
      'value' => $this->id,
      'order' => '996'
    ));
    $this->form->addElement('Hidden', 'pro_id', array(
      'value' => $this->id,
      'order' => '997'
    ));

?>

<?php echo $this->form->render($this);?>
