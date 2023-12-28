<div id = "grid-<?php echo $this->widgetIdentity ?>" class="sitebooking_grid sb_common">
  <?php   
    $categoryTable = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();
  ?>
  <?php $item = $this->provider; ?>
  <?php $classroom = $category = Engine_Api::_()->getItem('classroom',$item->parent_id);?>
  <div class="_grid">
    <div class="_inner">
      <div class="_top">
        <div class="_img"> <span><?php echo $this->itemBackgroundPhoto($classroom, 'thumb.profile') ?></span> </div>
      </div>
      <div class="_bottom">

      <!-- RATING -->
      <div class=' _rating_wishlist '>
        <div id="sitebooking_rating" class="_rating">
          <span id="provider_grid_tabs_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_1" class="rating_star_big_generic"> </span>
          <span id="provider_grid_tabs_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_2" class="rating_star_big_generic"> </span>
          <span id="provider_grid_tabs_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_3" class="rating_star_big_generic"></span>
          <span id="provider_grid_tabs_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_4" class="rating_star_big_generic" ></span>
          <span id="provider_grid_tabs_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_5" class="rating_star_big_generic" ></span>
        </div>
      </div>

      <script type="text/javascript">
        en4.core.runonce.add( function() {
          var rating = "<?php echo $item->rating;?>";
        
          for(var x=1; x<=parseInt(rating); x++) {
              
            var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
            id = "provider_grid_tabs_rate"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
          }

          var remainder = Math.round(rating)-rating;

          for(var x=parseInt(rating)+1; x<=5; x++) {
              
            var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
            id = "provider_grid_tabs_rate"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
          }

          if (remainder <= 0.5 && remainder !=0){

            var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
            var last = parseInt(rating)+1;
            id = "provider_grid_tabs_rate"+id+"_"+last;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
          }
        });
      </script> 
        <div class="_name">
          <h3> <?php echo $this->htmlLink($classroom->getHref(), $classroom->getTitle()) ?> </h3>
        </div>
        <div class="_category"> 
          <!-- ALL CATEGORIES RELATED TO PROVIDER -->
          <?php 

            $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
            $serviceTableName = $serviceTable->info('name');

            $select = $serviceTable->select();

            $sql = $serviceTableName.".parent_id = ".$item['pro_id']." AND ".$serviceTableName.".category_id != '0'";
            $select->where($sql);
            $select->group($serviceTableName.'.category_id');

            $service = $serviceTable->fetchAll($select)->toArray();

            $count = 0;

            foreach ($service as $serviceCategoryValue) {

              $count = $count + 1;

              foreach ($categoryTable as $categoryValue) {

                if($serviceCategoryValue['category_id'] == $categoryValue['category_id']) {
                  echo $categoryValue['category_name'];

                  if(count($service) != $count)
                    echo ", ";
                }

              }

            }

          ?>

        </div>
      </div>
        <div class="_contact">
          <!-- CONTACT -->
          <button>
            <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'contact-us', 'pro_id' => $item->pro_id, 'format' => 'smoothbox'), $this->translate('Contact'), array(
                'class' => 'smoothbox'
              ));
            ?>
          </button>
        </div>
    </div>
  </div>
</div>