<?php $widgetIdentity = $this->identity;?>
<?php if(!$this->isAjax) :?>

<ul class="sitebooking_user_review_wrapper">
  
  <li class="_left">
    <div class="_top">
      <h3>Average Rating</h3>
      <div id="sitebooking_rating" class="rating">
        <span id="avg_rate_1" class="rating_star_big_generic"> </span>
        <span id="avg_rate_2" class="rating_star_big_generic"> </span>
        <span id="avg_rate_3" class="rating_star_big_generic"></span>
        <span id="avg_rate_4" class="rating_star_big_generic" ></span>
        <span id="avg_rate_5" class="rating_star_big_generic" ></span>
        <span id="rating_text" style="color: #aaa;">
          <?php 
          if($this->rating_count > 0)
            echo $this->translate("Based On ".$this->rating_count. " Rating");
          else
            echo $this->translate('No One Has Rated Yet');?>      
        </span> 
      </div>
      <div style="margin-top: 5px;">Based on <?php echo $this->review_count?> review</div>
    </div>

    <div class="_bottom">
      <h3>My Rating</h3>
      <div id="sitebooking_rating" class="rating">
        <span id="my_rate_1" class="rating_star_big_generic"> </span>
        <span id="my_rate_2" class="rating_star_big_generic"> </span>
        <span id="my_rate_3" class="rating_star_big_generic"></span>
        <span id="my_rate_4" class="rating_star_big_generic" ></span>
        <span id="my_rate_5" class="rating_star_big_generic" ></span>
      </div>
    </div>
  </li>

  <li class="_right">
    <div class=''>
      <h3>Ratings Breakdown</h3>
      <p class="_five">
        <span class="_counttime">5 <i class="fa fa-star"></i> </span>
        <span class="_strip"></span>
        <span class="_counts" ><?php echo $this->five_star?></span>
      </p>
      <p class="_four">
        <span class="_counttime">4 <i class="fa fa-star"></i> </span>
        <span class="_strip"></span>
        <span class="_counts" ><?php echo $this->four_star?></span>
      </p>
       <p class="_three">
        <span class="_counttime">3 <i class="fa fa-star"></i> </span>
        <span class="_strip"></span>
        <span class="_counts" ><?php echo $this->three_star?></span>
      </p>
       <p class="_two">
        <span class="_counttime">2 <i class="fa fa-star"></i> </span>
        <span class="_strip"></span>
        <span class="_counts" ><?php echo $this->two_star?></span>
      </p>
       <p class="_one">
        <span class="_counttime">1 <i class="fa fa-star"></i> </span>
        <span class="_strip"></span>
        <span class="_counts" ><?php echo $this->one_star?></span>
      </p>
    </div>
  </li>

</ul>

<script type="text/javascript">
  var rating = "<?php echo $this->myRating ?>";

  if( "<?php echo $this->myRating ?>" == ""){
    rating = 0;
  }
  
  for(var x=1; x<=parseInt(rating); x++) {
    document.getElementById('my_rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
  }

  var remainder = Math.round(rating)-rating;

  for(var x=parseInt(rating)+1; x<=5; x++) {
    document.getElementById('my_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
  }
  
  if (remainder <= 0.5 && remainder !=0){
    var last = parseInt(rating)+1;
    document.getElementById('my_rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
  }
</script>

<script type="text/javascript">

  window.addEvent('domready', function() {

    document.getElementById('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";

    var rating = "<?php echo $this->avgRating;?>";
    
    for(var x=1; x<=parseInt(rating); x++) {
      document.getElementById('avg_rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
    }

    var remainder = Math.round(rating)-rating;

    for(var x=parseInt(rating)+1; x<=5; x++) {
      document.getElementById('avg_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
    }
    
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      document.getElementById('avg_rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
    }

  });

</script>


<!-- USER REVIEW -->
<ul id = "user_reviews" class="">

  <div style = " margin-top: 2%; margin-bottom: 1%; color: green; " ><?php echo $this->review_count?> Review Found. </div>

<?php endif; ?>
  <?php foreach( $this->paginator as $item ) : ?>

    <li>

      <div>
        <span><?php echo $this->translate('Reviewed by');?></span>
        <span><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></span>
        <span>
            <?php echo $this->translate('about');?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>  
        </span>
      </div>

      <div><?php echo $item->review ?></div>

      <!-- USER REVIEW RATING -->
       <div class=''>
        <div id="sitebooking_rating" class="rating">
          <span id="user_rate_<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
          <span id="user_rate_<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
          <span id="user_rate_<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
          <span id="user_rate_<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
          <span id="user_rate_<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>
        </div>
      </div>

      <script type="text/javascript">
        en4.core.runonce.add( function() {
          var rating = "<?php echo $item->rating;?>";
          
          for(var x=1; x<=parseInt(rating); x++) {
            
            var id = <?php echo $item->getIdentity() ?>;
            id = "user_rate_"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
          }

          var remainder = Math.round(rating)-rating;

          for(var x=parseInt(rating)+1; x<=5; x++) {
            
            var id = <?php echo $item->getIdentity() ?>;
            id = "user_rate_"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
          }

          if (remainder <= 0.5 && remainder !=0){

            var id = <?php echo $item->getIdentity() ?>;
            var last = parseInt(rating)+1;
            id = "user_rate_"+id+"_"+last;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
          }
        });
      </script>

    </li>

  <?php endforeach; ?>

<?php if(!$this->isAjax): ?>
</ul>

<img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader-<?php echo $widgetIdentity ?>"> 

<div class="sitebooking_more" id="view_more_<?php echo $this->identity;?>" >View more</div>
<?php endif; ?>

<script type="text/javascript">
  document.getElementById('view_more_<?php echo $this->identity;?>').addEvent('click', function(){

    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data : {
      format : 'html',
      subject : en4.core.subject.guid,
      page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
      isAjax : 1
      },

      onRequest: function () {
        $('loader-<?php echo $widgetIdentity ?>').show();
      },   

      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
      $('loader-<?php echo $widgetIdentity ?>').hide();    
      document.getElementById('user_reviews').innerHTML = document.getElementById('user_reviews').innerHTML + responseHTML; 

      }     
    }));
  });
  var cpage_<?php echo $widgetIdentity ?> = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  
  var pages_<?php echo $widgetIdentity ?> = <?php echo $this->paginator->count() ?>;

  if(cpage_<?php echo $widgetIdentity ?> >= pages_<?php echo $widgetIdentity ?>) {
    document.getElementById("view_more_<?php echo $widgetIdentity ?>").style.display = "none";
  } else {
    document.getElementById("view_more_<?php echo $widgetIdentity ?>").style.display = "block";
  }  

</script>