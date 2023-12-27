<?php 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>
<?php if(empty($this->isAjax)): ?>
<ul class="services_browse _wrapper" id="similar_providers<?php echo $this->identity ?>">
<?php endif; ?>

  <?php foreach ($this->paginator as $key => $item) : ?>
    <li>

      <div class='services_browse_photo'>
         <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->pro_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->slug), $this->itemBackgroundPhoto($item, 'thumb.normal')); ?>
      </div>

      <div class='services_browse_info'>
        <span class='services_browse_info_title'>
          <h3><?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->pro_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->slug), $item->getTitle()); ?>
          </h3>
        </span>

        <!-- RATING -->
        <div class='  '>
          <div id="sitebooking_rating" class="rating">
            <span id="provider_browse_rate_<?php echo $item->pro_id; ?>_1" class="rating_star_big_generic"> </span>
            <span id="provider_browse_rate_<?php echo $item->pro_id; ?>_2" class="rating_star_big_generic"> </span>
            <span id="provider_browse_rate_<?php echo $item->pro_id; ?>_3" class="rating_star_big_generic"></span>
            <span id="provider_browse_rate_<?php echo $item->pro_id; ?>_4" class="rating_star_big_generic" ></span>
            <span id="provider_browse_rate_<?php echo $item->pro_id; ?>_5" class="rating_star_big_generic" ></span>
          </div>
        </div>
		  
      </div>

      <script type="text/javascript">
        en4.core.runonce.add( function() {
          var rating = "<?php echo $item->rating;?>";
          
          for(var x=1; x<=parseInt(rating); x++) {
            
            var id = <?php echo $item->pro_id; ?>;

            id = "provider_browse_rate_"+id+"_"+x;

            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
          }

          var remainder = Math.round(rating)-rating;

          for(var x=parseInt(rating)+1; x<=5; x++) {
            
            var id = <?php echo $item->pro_id; ?>;
            id = "provider_browse_rate_"+id+"_"+x;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
          }

          if (remainder <= 0.5 && remainder !=0) {

            var id = <?php echo $item->pro_id; ?>;
            var last = parseInt(rating)+1;
            id = "provider_browse_rate_"+id+"_"+last;
            document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
          }

        });
      </script>
    </li>
  <?php endforeach; ?>
  <?php if(empty($this->isAjax)): ?>

</ul> 
<img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader_<?php echo $this->identity ?>">
<div class="sitebooking_more" onclick="viewmore<?php echo $this->identity ?>()" id="view_more_<?php echo $this->identity ?>" >View more</div>
<?php endif; ?>


<script type="text/javascript">

  function viewmore<?php echo $this->identity ?>() {
    en4.core.request.send(scriptJquery.ajax({
      dataType:'html',
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data : {
        format : 'html',
        subject : en4.core.subject.guid,
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
        isAjax : 1,
        limit : '<?php echo $this->limit ?>'
      },
      beforeSend: function () {
        scriptJquery('#loader_<?php echo $this->identity ?>').show();
      }, 
      success: function(responseHTML) {
        scriptJquery('#loader_<?php echo $this->identity ?>').hide();
      document.getElementById("similar_providers<?php echo $this->identity ?>").html(document.getElementById("similar_providers<?php echo $this->identity ?>").html() + responseHTML);
      }     
    }));
    var cpage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
    var pages = <?php echo $this->paginator->count() ?>;
    if(pages <= cpage+1) {
      var element = document.getElementById("view_more_<?php echo $this->identity ?>");
      element.parentNode.removeChild(element);
    }
  }  

  var cpage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  var pages = <?php echo $this->paginator->count() ?>;
  if(pages <= cpage) {
    var element = document.getElementById("view_more_<?php echo $this->identity ?>");
    if(element != null){
      element.parentNode.removeChild(element);
    }
  } 
    
</script>