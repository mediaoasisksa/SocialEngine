<?php $video = $this->video;
$this->headScript()->appendFile($this->layout()->staticBaseUrl . '/application/modules/Sitecourse/externals/scripts/togglefavourite.js');
?>
<div class="course_profile_view_container">
	<div class="course_profile_view_main">
		<div class="course_profile_view_video_preview">
			<div class="course_profile_view_video_preview_left">
				<div class="course_profile_view_video_preview_desc">
					<span class="course_profile_view_video_preview_title">
						<h1>
							<?php echo $this->course->title; ?>
						</h1>
						<span class="course_profile_view_video_preview_taging">
							<?php if($this->isTopRated): ?>
								<a href="javascript:void(0)" class="browse_course_tooltip_toprated"><?php echo $this->translate("Top-Rated"); ?></a>
							<?php endif; ?>
							<?php if($this->isNewest): ?>
								<a href="javascript:void(0)" class="browse_course_tooltip_sposored"><?php echo $this->translate("Newest"); ?></a>
							<?php endif; ?>
							<?php if($this->course->bestseller): ?>
								<a href="javascript:void(0)" class="browse_course_tooltip_bestseller"><?php echo $this->translate("Best-Seller"); ?></a>
							<?php endif; ?>
						</span>
					</span>
					
					<span class="course_profile_view_video_preview_creater">
						<div class="course_profile_view_video_preview_creater_main">
							<img src="<?php echo $this->ownerImg; ?>" alt="Creator Image">
							<h3>
								<?php echo $this->translate('Owner'); ?> : <?= $this->htmlLink($this->course->getOwner()->getHref(), $this->course->getOwner()->getTitle(), array('target' => '_blank')); ?>
							</h3>
						</div>

						<span class="course_profile_view_video_preview_ratings_tags">
							<div class="course_profile_view_video_preview_ratings_block">
								<span class="course_profile_view_video_preview_ratings">
									<span class="course_profile_view_video_preview_rate full_rating"></span>
									<span class="course_profile_view_video_preview_rate full_rating"></span>
									<span class="course_profile_view_video_preview_rate full_rating"></span>
									<span class="course_profile_view_video_preview_rate full_rating"></span>
									<span class="course_profile_view_video_preview_rate half_rating"></span>
								</span>
								<span class="course_profile_view_video_preview_rating_point">
									<?php echo $this->translate("Average Rating:"); ?> <?php echo $this->course->rating; ?>                      
								</span>
							</div>
						</span>

					</span>

					<span class="course_profile_view_video_preview_list_feature">
						<span class="course_profile_view_video_preview_list_feature_block">
							<span class="course_profile_view_video_preview_feature_list" title="Difficulty Level">
								<i class="fas fa-level-up-alt"></i>
								<p>
									<?php echo $this->difficultyLevel; ?>
								</p>
							</span>
							<span class="course_profile_view_video_preview_feature_list" title="Buyer Count">
								<i class="fas fa-user"></i>
								<p>
									<?php echo $this->buyersCount; ?>
								
								<?php if($this->buyersCount > 1){
											echo ' Members';
										} else{
											echo ' Member';
										} 
								?>
								</p>
							</span>
							<span class="course_profile_view_video_preview_feature_list" title="Category">
								<i class="fas fa-list-ul"></i>
								<p>
									<?php echo $this->category_name; ?>
								</p>
							</span>
						</span>
						<span class="course_profile_view_video_preview_feature_price">
							<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
							echo $this->translate("Price: "); ?>
							<?= $currency; ?>							
							<?php echo $this->course['price']; ?>
							
						</span>
					</span>

					<div class="course_profile_view_time">
						<span><?php echo $this->translate("Last Modified "); ?></span>
						<span><?php $date = strtotime($this->course->modified_date);
						echo $this->timestamp(strtotime($this->course->modified_date)); ?></span>
					</div>

					<span class="course_profile_view_tags">
						<form id='filter_form_tag' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitecourse_general', true) ?>' style='display: none;'>
							<input type="hidden" id="tag" name="tag"  value=""/>
						</form>
						<ul class="sitecourse_sidebar_list">
							<li>
								<?php foreach ($this->tag_array as $key => $frequency): ?>
									<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
									<?php ?>
									<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>);' style="float:none;font-size:<?php echo $step ?>px;" title=''><?php echo '#'.$key ?></a>
								<?php endforeach; ?>
							</li>
						</ul>
					</span>

					<span class="course_profile_view_video_preview_info">
						<p>
							<?php echo $this->course->overview; ?>
						</p>
					</span>
					<span class="course_profile_view_video_preview_buttons">
						<?php if($this->canBuy): ?>
							<!-- purchase now form -->
							<form id='purchase_course_form' class='global_form_box' method='post' action='<?php echo $this->url(array('action' => 'buyer-details','course_id' => $this->course_id), 'sitecourse_order', true) ?>'>
								<input type="hidden" value="<?php echo $this->course_id ?>" name="course" />
								<input type="submit" value="<?php echo $this->translate("BUY NOW");?>">
							</form>
						<?php elseif($this->isPurchased): ?>
							<!-- course is purchased by user -->
							<a href='<?php echo $this->url(array('action' => 'index','course_id'=>$this->course->course_id), 'sitecourse_learning', true) ?>' ><?php echo $this->translate("Go to course"); ?></a>
						<?php endif; ?>

						<?php if($this->favText): ?>
							<button class="browse__course__widhlist__added" title="Remove From Favourite" onclick="toggleFavourite(<?=$this->course->course_id; ?>)" id="fav_btn_<?=$this->course->course_id; ?>">
								<i class="far fa-heart" ></i></button>
							<?php else: ?>
								<button class="browse__course__widhlist" title = "Make Favourite" onclick="toggleFavourite(<?=$this->course->course_id; ?>)" id="fav_btn_<?=$this->course->course_id; ?>">
									<i class="far fa-heart" ></i></button>
								<?php endif; ?>
							</span>
						</div>
					</div>
					<div class="course_profile_view_video_preview_right">
						<div class="course_profile_view_video_preview_video">

							<?php if($video['type'] == 'upload'): ?>
								<video id="video" width="100%" height="400px" controls>
									<source src="<?php echo $this->video_url; ?>" type="video/mp4" />
										<?php echo $this->translate("Please try after some time");?>
									</video>
								<?php else: ?>
									<iframe width="100%" height="400px" src="<?php echo $this->video_url; ?>" frameborder="0" allowfullscreen>
									</iframe>
								<?php endif; ?>

							</div>
						</div>
					</div>
				</div>
			</div>


			<script type="text/javascript">
				const favUrl = '<?= $this->url(array('action' => 'togglefavourite', 'course_id' => $this->course->course_id), 'sitecourse_specific', null);?>';
    // display ratings
    window.addEventListener('load', function(event) {
    	let rating = Number('<?php echo $this->course->rating; ?>') || 0; 
    	rating = (rating <= 5) ? rating : 5;
    	let ratingHTML = '';
    	for(let i = 0; i < Math.floor(rating); ++i) {
    		ratingHTML += `<span class="course_profile_view_video_preview_rate full_rating"></span>`;
    	}
    	if((Math.round(rating) - rating) > 0) {
    		ratingHTML += `<span class="course_profile_view_video_preview_rate half_rating"></span>`;
    	}
    	let num = Math.floor(5 - rating);
    	if(num < 0 ) num = 0;
    	for(let i=1;i <= num; ++i){
    		ratingHTML +=	`<span class="no_ratings"></span>`;
    	}

    	document.querySelector('.course_profile_view_video_preview_ratings').innerHTML = ratingHTML;
    })


    function toggleFavourite(id) {
    	scriptJquery.ajax({
    		url : favUrl,
    		data : {
    			format : 'json'
    		},
    		success : function(responseJSON) {    		
    			const likeButton = document.getElementById('fav_btn_' + id);    		
    			if(responseJSON.favourite){
    				likeButton.classList.add('browse__course__widhlist__added');
    				likeButton.classList.remove('browse__course__widhlist');
    				likeButton.title = 'Remove From Favourite';
    			} else {    				
    				likeButton.classList.remove('browse__course__widhlist__added');
    				likeButton.classList.add('browse__course__widhlist');
    				likeButton.title = 'Make Favourite';
    			}
    		}
    	});
    }
</script>
<script type="text/javascript">

	var tagAction = function(tag){
		form=scriptJquery('#filter_form_tag')[0];  
		form.elements['tag'].value = tag;
		scriptJquery('#filter_form_tag').submit();

	}
</script>
