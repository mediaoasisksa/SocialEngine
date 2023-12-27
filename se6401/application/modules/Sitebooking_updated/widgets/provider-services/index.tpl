<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
   
  <?php if(empty($this->isAjax)): ?>
  <div class="sitebooking_grid sb_common" id="provider_services_<?php echo $this->identity ?>">
  <?php endif; ?>
  
  <?php foreach( $this->paginator as $item ): ?>
  
  <div class="grid">
    
    <div class="_img"> <span><?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.main'));?></span> 
    </div>

    <div class="_profileinfo">
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
        scriptJquery(id).attr('class', 'rating_star_big_generic rating_star_big');
      }

      var remainder = Math.round(rating)-rating;

      for(var x=parseInt(rating)+1; x<=5; x++) {
        
        var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
        id = "provider_grid_tabs_rate"+id+"_"+x;
        scriptJquery(id).attr('class', 'rating_star_big_generic rating_star_big_disabled');
      }

      if (remainder <= 0.5 && remainder !=0){

        var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
        var last = parseInt(rating)+1;
        id = "provider_grid_tabs_rate"+id+"_"+last;
        scriptJquery(id).attr('class', 'rating_star_big_generic rating_star_big_half');
      }
      });
    </script> 
    <div class="_name">
      <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?> </h3>
    </div>
    <div class="_price"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></div>

    <div class="_description">
      <?php echo $item->description ?>
    </div>
    
    </div> 

  </div>

  <?php endforeach; ?>

  
  <?php if(empty($this->isAjax)): ?>
  </div>
  <img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader_<?php echo $this->identity ?>">
  <div class="sitebooking_more" onclick="viewfun()" id="services_view_more_<?php echo $this->identity ?>" >View more</div>
  <?php endif; ?>

<?php endif; ?>

<script type="text/javascript">

  function viewfun() {
  en4.core.request.send(scriptJquery.ajax({
    dataType:'html',
    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
    data : {
    format : 'html',
    subject : en4.core.subject.guid,
    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
    isAjax : 1
    },
    beforeSend: function () {
    scriptJquery('#loader_<?php echo $this->identity ?>').show();
    }, 
    success: function(responseHTML) {
    scriptJquery('#loader_<?php echo $this->identity ?>').hide();
    scriptJquery("provider_services_<?php echo $this->identity ?>").html(scriptJquery("provider_services_<?php echo $this->identity ?>").html() + responseHTML);
    }     
  }));
  var cpage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var pages = <?php echo $this->paginator->count() ?>;
  if(pages <= cpage+1) {
    var element = scriptJquery("services_view_more_<?php echo $this->identity ?>");
    element.parentNode.removeChild(element);
  }
  }  

  var cpage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var pages = <?php echo $this->paginator->count() ?>;
  if(pages <= cpage) {
  var element = scriptJquery("services_view_more_<?php echo $this->identity ?>");
  if(element != null){
    element.parentNode.removeChild(element);
  }
  } 
  
</script>


