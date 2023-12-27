<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div id = "list-<?php echo $this->widgetIdentity ?>" class="sitebooking_list sb_common">
    <?php 
   $categoryTable = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();
    ?>
  <?php foreach( $this->paginator as $item ): ?>
    <div class="_list">
    <div class="_inner">
      <div class="_left">
        <div class="_img"> <span><?php echo $this->itemBackgroundPhoto($item, 'thumb.main') ?></span> </div>
        <div class="_info">
          <div class="_inner">
            <div class="_socialicons"> <a href="https://facebook.com/"><i class="fa fa-facebook"></i></a> <a href="https://twitter.com/"><i class="fa fa-twitter"></i></a> <a href="https://linkedin.com/"><i class="fa fa-linkedin"></i></a> <a href="https://pinterest.com/"><i class="fa fa-pinterest"></i></a>  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'id' => $item->pro_id, 'type' => $item->getType()), '<i class="fa fa-share-alt"></i>',  array(
              'class' => 'smoothbox'
            ));?> </div>  
        
            <div class="_stats"> <span class="_like"><i class="fa fa-thumbs-o-up"></i><?php echo $item->like_count;?></span> <span class="_comment"><i class="fa fa-comment-o"></i><?php echo $item->comment_count;?></span> </div>
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

            <?php if($item->verified == 1) :?> 
              <span class="_verified">Verified</span> 
            <?php endif;?>

            <?php if($item->newlabel == 1) :?> 
              <span class="_new">New</span> 
            <?php endif;?>

          </div>

        </div>
      </div>
      <div class='providers_browse_options'>
        <span id = "dashboardProvider_<?php echo $item->pro_id?>">
          <?php echo $this->htmlLink(array(
            'action' => 'edit',
            'pro_id' => $item->getIdentity(),
            'route' => 'sitebooking_provider_specific',
            'reset' => true,
          ), $this->translate('Dashboard'), array(
            'class' => 'buttonlink icon_service_dashboard',
          )) ?>           
        </span>
            <span class="site_<?php echo $item->pro_id?>">
              <?php if($item->enabled == "1"): ?>
                <?php
                  echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'disable', 'pro_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Disable Provider'), array(
                   'class' => 'buttonlink smoothbox icon_service_delete provider_enable'
                 ));
                ?>
              <?php else: ?>
                <?php
                  echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'enable', 'pro_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Enable Provider'), array(
                   'class' => 'buttonlink smoothbox icon_service_delete'
                 ));
                ?>
              <?php endif; ?>
            </span>
          </div>
      <div class="_right">
       <div class="_name">
          <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>
        </div>

        <!-- RATING -->
        <div class=' _rating_wishlist '>
          <div id="sitebooking_rating" class="_rating">
              <span id="provider_index<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
              <span id="provider_index<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
              <span id="provider_index<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
              <span id="provider_index<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
              <span id="provider_index<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>
          </div>
        </div>

        <script type="text/javascript">
          en4.core.runonce.add( function() {
            var rating = "<?php echo $item->rating;?>";
          
            for(var x=1; x<=parseInt(rating); x++) {
                
                var id = <?php echo $item->getIdentity() ?>;
                id = "provider_index"+id+"_"+x;

                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
            }

            var remainder = Math.round(rating)-rating;

            for(var x=parseInt(rating)+1; x<=5; x++) {
                
                var id = <?php echo $item->getIdentity() ?>;
                id = "provider_index"+id+"_"+x;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
            }

            if (remainder <= 0.5 && remainder !=0){

                var id = <?php echo $item->getIdentity() ?>;
                var last = parseInt(rating)+1;
                id = "provider_index"+id+"_"+last;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
            }
          });
        </script>
      <div class="_location"><i class="fa fa-map-marker"></i><?php echo $item->location; ?></div>

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
      
        <div class="_name">
          <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>
        </div>
      
        <div class="_description">
          <?php echo $item->description;?> 
        </div>
      </div>
    </div>
  </div>
      <?php endforeach; ?>
    </div>
  
    <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any service provider.');?>
          <?php echo $this->translate(' %1$sClick here%2$s to become a service provider.', '<a href="'.$this->url(array('action' => 'create'), 'sitebooking_provider_general').'">', '</a>'); ?>
      </span>
    </div>
    <?php endif; ?>



<?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    //'params' => $this->formValues,
)); ?>