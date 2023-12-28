<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>

<?php $widgetIdentity = $this->identity; ?>
<div class="servicecover">
  <div class="_inner">
    <div class="_thumb">
      <div class="_img"> 
        <span><?php echo $this->itemBackgroundPhoto($this->item, 'thumb.normal'); ?></span> 
      </div>

      <div class="_labels">

        <?php if($this->item->featured == 1) :?> 
          <span class="_featured">Featured</span> 
        <?php endif;?>

        <?php if($this->item->sponsored == 1) :?> 
          <span class="_sponsored">Sponsored</span> 
        <?php endif;?>

        <?php if($this->item->hot == 1) :?> 
          <span class="_hot">Trending</span> 
        <?php endif;?>

        <?php if($this->item->newlabel == 1) :?> 
          <span class="_new">New</span> 
        <?php endif;?>

        </div>
      </div>
  
      <div class="_info">
        <div class="_respname">
          <h3> <?php echo $this->item->title;?> </h3>
        </div>
        <div class="_rating_wishlist">
          <!-- RATING -->
          <div id="sitebooking_rating" class="_rating">
            <span id="service_top_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>_1" class="rating_star_big_generic"> </span>
            <span id="service_top_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>_2" class="rating_star_big_generic"> </span>
            <span id="service_top_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>_3" class="rating_star_big_generic"></span>
            <span id="service_top_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>_4" class="rating_star_big_generic" ></span>
            <span id="service_top_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>_5" class="rating_star_big_generic" ></span>
          </div>

          <script type="text/javascript">
            en4.core.runonce.add( function() {
              var rating = "<?php echo $this->item->rating;?>";
            
              for(var x=1; x<=parseInt(rating); x++) {
                  
                var id = <?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>;
                id = "service_top_cover_rate"+id+"_"+x;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
              }

              var remainder = Math.round(rating)-rating;

              for(var x=parseInt(rating)+1; x<=5; x++) {
                  
                var id = <?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>;
                id = "service_top_cover_rate"+id+"_"+x;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
              }

              if (remainder <= 0.5 && remainder !=0){

                var id = <?php echo $this->item->getIdentity() ?><?php echo $widgetIdentity ?>;
                var last = parseInt(rating)+1;
                id = "service_top_cover_rate"+id+"_"+last;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
              }
            });
          </script>

          <span class="_wishlist">
            <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_ser', $this->item->ser_id),"favourite") ?>
          </span>
        </div>

        <div class="_price"><?php echo $this->locale()->toCurrency($this->item->price,Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($this->item->duration); ?></div>
        <span></span>   
        <div class="_name">
          <h3> <?php echo $this->item->title;?> </h3>
        </div>
    
        <!-- category_name -->
        <?php if($this->category_name != '' && $this->first_level_category_name == '' && $this->second_level_category_name == ''): ?>
          <div class="_category">
            <?php
                  echo $this->category_name; 
            ?>
          </div> 
        <?php endif;?>  

        <!-- first_level_category_name -->
        <?php if($this->first_level_category_name != '' && $this->second_level_category_name == ''): ?>
          <div class="_category">
            <?php
                  echo $this->first_level_category_name; 
            ?>
          </div> 
        <?php endif;?> 

        <!-- second_level_category_name -->
        <?php if($this->second_level_category_name != ''): ?>
          <div class="_category">
            <?php
                  echo $this->second_level_category_name;
            ?>
          </div>
        <?php endif;?> 

        <div class="_description">
          <?php echo $this->item->description; ?>  
        </div> 

        <div class="_actionbtns">
          <div class="_leftbtn">
            <?php if( $this->reviewHide == '0'): ?>  
              <?php if($this->flag == '1'): ?>
                <button><i class="fa fa-pencil"></i><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'review', 'action' => 'index', 'subject' => $this->subject()->getGuid(), 'format' => 'smoothbox'), $this->translate('Write a Review'), array(
                                'class' => 'smoothbox'
                      ));?>   
                  </button>
              <?php endif;?>
              <?php if($this->flag == '2'): ?>
                  <button><i class="fa fa-pencil"></i><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'review', 'action' => 'index', 'subject' => $this->subject()->getGuid(), 'format' => 'smoothbox'), $this->translate('Update Review'), array(
                                'class' => 'smoothbox'
                      ));?>   
                  </button>
              <?php endif;?>
            <?php endif;?>  
            <?php if($this->item->approved == 0): ?>
              <div class="tip">
                <span><?php echo $this->translate("Admin has not yet approved this service.") ?></span>
              </div>

            <!-- service will not show if not enabled -->  
            <?php elseif($this->item->enabled == 0 || $this->providerTable->enabled == 0): ?>
              <div class="tip">
                <span>Provider has disabled this service.<span>
              </div>       
            <?php else: ?>     
            
              <?php if($this->viewer->getIdentity() != $this->item->owner_id) : ?>
                <?php if($this->item->enabled == 1 && $this->providerTable->enabled == 1 && count($this->availability) > 0): ?>
                  <button>
                    <?php 
                      echo $this->htmlLink(array(
                        'action' => 'book-service',
                        'ser_id' => $this->item->getIdentity(),
                        'route' => 'sitebooking_booking_specific',
                        'reset' => true,
                      ), $this->translate('Book Me'), array(
                        'class' => '',
                      )) 
                    ?>  
                  </button>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>
          <div class="_rightbtn">
            <span class="_like"><i class="fa fa-thumbs-o-up"></i><?php echo $this->item->like_count;?></span>
            <span class="_comment"><i class="fa fa-comment-o"></i></i><?php echo $this->item->comment_count;?></span>
          </div>
        </div>
      </div>
  </div>
</div>