<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Services');?>
  </h2>

  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php 
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      ?>
    </div>
  <?php endif; ?>
</div>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<div class="layout_middle">
<div class="sitebooking_list sb_common sitebooking_wishlist service_grid_list">
	<?php 
    // Render the sub menu
    echo $this->navigation()->menu()->setContainer($this->childNavigation)->render();
  ?>
  <?php if($this->paginator->getTotalItemCount() > 0) : ?>
  <?php foreach( $this->paginator as $item ) : ?>
  <div class="_list">
  <div class="_inner">
    <div class="_left">
    <div class="_img"> <span><?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?></span> </div>
    <div class="_info">
      <div class="_inner">
      <div class="_contact">
        <!-- CONTACT -->        
          <?php
            $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
            $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$item->ser_id));
            $viewer = Engine_Api::_()->user()->getViewer();
            $data = array();
            if($scheduleRow){
              $monday = json_decode($scheduleRow->monday, true);
              $tuesday = json_decode($scheduleRow->tuesday, true);
              $wednesday = json_decode($scheduleRow->wednesday, true);
              $thursday = json_decode($scheduleRow->thursday, true);
              $friday = json_decode($scheduleRow->friday, true);
              $saturday = json_decode($scheduleRow->saturday, true);
              $sunday = json_decode($scheduleRow->sunday, true);

              $data['demo'] = 'demo';
              if(!empty($monday))
                $data = array_merge($data,$monday);
              if(!empty($tuesday))
                $data = array_merge($data,$tuesday);
              if(!empty($wednesday))
                $data = array_merge($data,$wednesday);
              if(!empty($thursday))
                $data = array_merge($data,$thursday);
              if(!empty($friday))
                $data = array_merge($data,$friday);
              if(!empty($saturday))
                $data = array_merge($data,$saturday);
              if(!empty($sunday))
                $data = array_merge($data,$sunday);

              unset($data['demo']);
            }
          ?>
          <?php if(count($data) > 0 && $viewer->getIdentity() != $item->owner_id): ?>
            <button>
            <?php 
              echo $this->htmlLink(array(
                'action' => 'book-service',
                'ser_id' => $item->ser_id,
                'route' => 'sitebooking_booking_specific',
                'reset' => true,
              ), $this->translate('Book Me'), array(
                'class' => '',
              )) 
            ?>  
          </button>
          <?php else: ?>
            <button>
            <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'contact-us', 'pro_id' => $item->parent_id, 'format' => 'smoothbox'), $this->translate('Contact'), array(
                'class' => 'smoothbox'
              ));
            ?>
          </button>
        <?php endif; ?>
      </div>
      </div>
      <div class="_labels">

      <?php if($item->featured == 1) :?> 
        <span class="_featured">Featured</span> 
      <?php endif;?>

      <?php if($item->sponsored == 1) :?> 
        <span class="_sponsored">Sponsored</span> 
      <?php endif;?>

      <?php if($item->hot == 1) :?> 
        <span class="_hot">Trending</span> 
      <?php endif;?>

      <?php if($item->newlabel == 1) :?> 
        <span class="_verified">New</span> 
      <?php endif;?>

      </div>
      
      <?php 

      // SHOW CATEGORY
      if($item->category_id != 0) {
        $category = Engine_Api::_()->getItem('sitebooking_category',$item->category_id);
        $category_name = $category["category_name"];
      } 
        
      if($item->first_level_category_id != 0) {
        $category = Engine_Api::_()->getItem('sitebooking_category',$item->first_level_category_id);
        $first_level_category_name = $category["category_name"];
      } else {
        $first_level_category_name = 0;
      } 
        
      if($item->second_level_category_id != 0) {
        $category = Engine_Api::_()->getItem('sitebooking_category',$item->second_level_category_id);
        $second_level_category_name = $category["category_name"];
      } else {
        $second_level_category_name = 0;
      } 
      
      ?>
      
      <!-- category_name -->
      <?php if($category_name != '' && $first_level_category_name == '' && $second_level_category_name == ''): ?>
      <div class="_category">
        <?php
          echo $category_name; 
        ?>
      </div> 
      <?php endif;?>  

     <!-- first_level_category_name -->
      <?php if($first_level_category_name != '' && $second_level_category_name == ''): ?>
      <div class="_category">
        <?php
          echo $first_level_category_name; 
        ?>
      </div> 
      <?php endif;?> 

      <!-- second_level_category_name -->
      <?php if($second_level_category_name != ''): ?>
      <div class="_category">
        <?php
          echo $second_level_category_name;
        ?>
      </div>
      <?php endif;?> 

    </div>
    </div>
    <div class="_right">
      <div class="_rating_wishlist"> 
        <span class="_rating"> 
        <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
        <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
        <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
        <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
        <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>
        </span>

        <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_ser', $item->ser_id),"favourite") ?>
        
        <script type="text/javascript">
          en4.core.runonce.add( function() {
            var rating = "<?php echo $item->rating;?>";
          
            for(var x=1; x<=parseInt(rating); x++) {
              
            var id = <?php echo $item->getIdentity() ?>;
            id = "service_browse_rate_"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
            }

            var remainder = Math.round(rating)-rating;

            for(var x=parseInt(rating)+1; x<=5; x++) {
              
            var id = <?php echo $item->getIdentity() ?>;
            id = "service_browse_rate_"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
            }

            if (remainder <= 0.5 && remainder !=0){

            var id = <?php echo $item->getIdentity() ?>;
            var last = parseInt(rating)+1;
            id = "service_browse_rate_"+id+"_"+last;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
            }

          });
        </script>
      </div>
      
      <div class="_name">
        <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>
      </div>
      <div class="_providername">
        <span class="_logo">
        <?php if(!empty($item->provider_photo_id)) :?>

          <?php $url = Engine_Api::_()->storage()->get($item->provider_photo_id)->getPhotoUrl();?>
          
          <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug),"<img src = $url style='max-width:100%;'>")
          ?>

        <?php else: ?>

          <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/default_service_profile.png" ?> 

          <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug),"<img src = $src style='max-width:100%;'>")
          ?>

        <?php endif; ?>

        </span>
        By    <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug), $item->provider_title) ?>       </div>
        
        <div class="_price"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></div>    

        <div class="_description">
        <?php echo $item->description; ?> 
        </div>
        <div class="_respcontactbtn">
          <?php if(!empty($item->telephone_no)) :?>
          <button><a href="tel:<?php echo $item->telephone_no ?>" class="btn btn-default">Contact</a></button>
          <?php else: ?>
            <button>
              <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'contact-us', 'pro_id' => $item->parent_id, 'format' => 'smoothbox'), $this->translate('Contact'), array('class' => 'smoothbox' ));
              ?>
            </button>
          <?php endif;?>            
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  <?php else : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not any services in your wishlist.');?>
      </span>
    </div>
  <?php endif ?> 

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
)); ?>
</div>



