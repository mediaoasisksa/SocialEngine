<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>

<?php if($this->item->approved == 0): ?>
  <div class="tip">
    <span><?php echo $this->translate("Admin has not yet approved this Service Provider.") ?></span>
  </div>
<?php elseif($this->item->enabled == 0): ?>
  <div class="tip">
    <span><?php echo $this->translate("This Service Provider has disabled its services.") ?></span>
  </div>        
<?php endif; ?>
    
<?php 
    
  $coreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.share");
  $shareArray = array();
  $shareArray = explode(",",$coreSettings);

?>

<div class="providercover">
	
	<div class="_top">
		
		<?php if(!empty($this->item->cover_id)) :?>

      <?php $url = Engine_Api::_()->storage()->get($this->item->cover_id)->getPhotoUrl();?>

    <?php else: ?>

      <?php $url = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/default_provider_cover.png" ?> 

    <?php endif; ?>

		<div class="_img" style="background-image:url( <?php echo $url;?> );"></div>

	</div>

	<div class="_bottom">
		<div class="_info">
			<div class="_left">
				<div class="_title_rating">
					<h3><?php echo $this->item->title ?></h3>
          	<span>
	            <span id="provider_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>_1" class="rating_star_big_generic"> </span>
	            <span id="provider_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>_2" class="rating_star_big_generic"> </span>
	            <span id="provider_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>_3" class="rating_star_big_generic"></span>
	            <span id="provider_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>_4" class="rating_star_big_generic" ></span>
	            <span id="provider_cover_rate<?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>_5" class="rating_star_big_generic" ></span>
            </span>

		        <script type="text/javascript">
		          en4.core.runonce.add( function() {
		            var rating = "<?php echo $this->item->rating;?>";
		          
		            for(var x=1; x<=parseInt(rating); x++) {
		                
	                var id = <?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
	                id = "provider_cover_rate"+id+"_"+x;
	                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
		            }

		            var remainder = Math.round(rating)-rating;

		            for(var x=parseInt(rating)+1; x<=5; x++) {
		                
	                var id = <?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
	                id = "provider_cover_rate"+id+"_"+x;
	                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
		            }

		            if (remainder <= 0.5 && remainder !=0){

	                var id = <?php echo $this->item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
	                var last = parseInt(rating)+1;
	                id = "provider_cover_rate"+id+"_"+last;
	                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
		            }
		          });
		        </script>
				</div>
				<p><?php echo $this->item->description; ?></p>
			</div>

			<div class="_right">

				<span class="_icons">

					<span class="_wishlist">
	        	<?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $this->item->pro_id),"favourite") ?>
	      	</span>

          <?php foreach( $shareArray as $value ) : ?>             

            <?php if( $value === "facebook") : ?> 
              <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $this->item->pro_id),"facebook") ?>
            <?php endif; ?>

            <?php if( $value === "twitter") : ?>            
              <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $this->item->pro_id),"twitter") ?>
            <?php endif; ?>

            <?php if( $value === "linkedin") : ?>
              <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $this->item->pro_id),"linkedin") ?>
            <?php endif; ?>

            <?php if( $value === "pinterest") : ?>
              <?php echo $this->sharelinkshelper(Engine_Api::_()->getItem('sitebooking_pro', $this->item->pro_id),"pinterest") ?>
            <?php endif; ?>

         		<?php if( $value === "share") : ?>
							<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'id' => $this->item->pro_id, 'type' => $this->item->getType(), 'format' => 'smoothbox'), '<i class="fa fa-share-alt"></i>',  array(
			        			'class' => 'smoothbox'
			      			)); 
							?>
						<?php endif; ?>

          <?php endforeach; ?>

				</span>
				
				<div class="_stats">
					<span class="_like"><i class="fa fa-thumbs-o-up"></i><?php echo $this->item->like_count;?></span>
					<span class="_comment"><i class="fa fa-comment-o"></i></i><?php echo $this->item->comment_count;?></span>
				</div>
			</div>
		</div>
	</div>
</div>