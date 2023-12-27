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

<?php 
$categoryTable = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();
$coreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.share");
$shareArray = array();
$shareArray = explode(",",$coreSettings);

?>
<div class="layout_middle">

<div class="sitebooking_list sb_common sitebooking_wishlist">
  <?php 
    // Render the sub menu
    echo $this->navigation()->menu()->setContainer($this->childNavigation)->render();
  ?>
  <?php if($this->paginator->getTotalItemCount() > 0) : ?>
  <?php foreach( $this->paginator as $item ) : ?>

    <div class="_list">
    <div class="_inner">
      <div class="_left">
      <div class="_img"> <span><?php echo $this->itemBackgroundPhoto($item, 'thumb.main') ?></span> </div>
      <div class="_info">
        <div class="_inner">
        <div class="_socialicons"> 

          <?php foreach( $shareArray as $value ) : ?>             

          <?php if( $value === "facebook") : ?> 
            <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $item->pro_id),"facebook") ?>
          <?php endif; ?>

          <?php if( $value === "twitter") : ?>            
            <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $item->pro_id),"twitter") ?>
          <?php endif; ?>

          <?php if( $value === "linkedin") : ?>
            <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $item->pro_id),"linkedin") ?>
          <?php endif; ?>

          <?php if( $value === "pinterest") : ?>
            <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $item->pro_id),"pinterest") ?>
          <?php endif; ?>

          <?php endforeach; ?>

        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'id' => $item->pro_id, 'type' => $item->getType()), '<i class="fa fa-share-alt"></i>',  array(
          'class' => 'smoothbox'
        ));?> </div>  
      
        <div class="_stats"> <span class="_like"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count;?></span> <span class="_comment"><i class="fa fa-comment"></i><?php echo $item->comment_count;?></span> </div>
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
      <div class="_right">
       <div class="_name">
        <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>
      </div>

      <!-- RATING -->
      <div class=' _rating_wishlist '>
        <div id="sitebooking_rating" class="_rating">
          <span id="provider_wishlist<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
          <span id="provider_wishlist<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
          <span id="provider_wishlist<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
          <span id="provider_wishlist<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
          <span id="provider_wishlist<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>
        </div>
        <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $item->pro_id),"favourite") ?>
      </div>
      

      <script type="text/javascript">
        en4.core.runonce.add( function() {
        var rating = "<?php echo $item->rating;?>";
        
        for(var x=1; x<=parseInt(rating); x++) {
          
          var id = <?php echo $item->getIdentity() ?>;
          id = "provider_wishlist"+id+"_"+x;

          document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
        }

        var remainder = Math.round(rating)-rating;

        for(var x=parseInt(rating)+1; x<=5; x++) {
          
          var id = <?php echo $item->getIdentity() ?>;
          id = "provider_wishlist"+id+"_"+x;
          document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
        }

        if (remainder <= 0.5 && remainder !=0){

          var id = <?php echo $item->getIdentity() ?>;
          var last = parseInt(rating)+1;
          id = "provider_wishlist"+id+"_"+last;
          document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
        }

        });
      </script>
      <?php if(!empty($item->location)): ?>
          <div class="_location"><i class="fa fa-map-marker"></i><?php echo $item->location; ?></div>
      <?php else: ?>
        <div class="_location"><i class="fa fa-map-marker"></i><?php echo "No Location Mentioned"; ?></div>
      <?php endif;?>

        <div class="_categoryblock">
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
        $check = 0;

        foreach ($service as $serviceCategoryValue) {

          $count = $count + 1;

          foreach ($categoryTable as $categoryValue) {

          if($serviceCategoryValue['category_id'] == $categoryValue['category_id']) {
            ?>
              <span class="_category"><?php echo $categoryValue['category_name']; ?></span>
            <?php
            $check = $check + 1;

            if(count($service) != $count)
            echo ", ";
          }

          }

        }
        ?>
      </div>
        <?php if($check == 0): ?>
          <div class="_nocategory"><?php echo "Provider hasn't yet started any service."; ?></div>
        <?php endif;?> 
      
      <div class="_name">
        <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </h3>
      </div>
      
      <div class="_description">
        <?php echo $item->description;?> 
      </div>
      <div class="_respcontactbtn">
        <!-- CONTACT -->
        <?php if(!empty($item->telephone_no) ): ?>
              <button><a href="tel:<?php echo $item->telephone_no ?>" class="btn btn-default">Contact</a></button>
            <?php else:?>
            <button>
              <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'contact-us', 'pro_id' => $item->pro_id, 'format' => 'smoothbox'), $this->translate('Contact'), array(
                  'class' => 'smoothbox'
                ));
              ?>
            </button>
          <?php endif; ?>
      </div>
      </div>
    </div>
    </div>
  
  <?php endforeach;?> 
  <?php else : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not any providers in your wishlist.');?>
      </span>
    </div>
  <?php endif ?>
</div>
<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  // 'query' => $this->formValues,
  // 'params' => $this->formValues,
)); ?>
    </div>
