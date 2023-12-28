<?php
$content_id = $this->identity;
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()
->appendFile($baseUrl . 'application/modules/Sitecourse/externals/scripts/jquery.min.js')
->appendFile($baseUrl . 'application/modules/Sitecourse/externals/scripts/owl.carousel.js');
?>
<div class="course_browse_reviews">
	<div class="course_browse_reviews_main">
		<span class="course_browse_reviews_total">
			<div id='add_review'>
				<?php if($this->rated){ echo $this->htmlLink(array('route' => 'sitecourse_review', 'module' => 'sitecourse', 'controller' => 'review', 'action' => 'index','course_id' => $this->course_id),'Update-Review', array( 'class' => 'smoothbox fas fa-edit',));
			} else {
				echo $this->htmlLink(array('route' => 'sitecourse_review', 'module' => 'sitecourse', 'controller' => 'review', 'action' => 'index','course_id' => $this->course_id),'Add-Review', array( 'class' => 'smoothbox fas fa-edit',));
			} ?>
			</div>
			<div class="course_browse_reviews_rating">
				<span class="course_browse_reviews_rates_number">
					<?php echo $this->course_rating; ?>
				</span>
				<span class="course_browse_reviews_rates">				
				</span>
				<span class="course_browse_reviews_rates_user">
					<?php if($this->rating_count > 1): ?>
					(<?php echo $this->rating_count; ?> ratings)
					<?php else: ?>
						(<?php echo $this->rating_count; ?> rating)
					<?php endif; ?>
				</span>
			</div>
	</span>
</div>
</div>
<br>
<div id='rev'></div>
<div class="loading-icon" style="display: none;" id="review-loading"><?php echo $this->translate("loading..."); ?> </div>
<?php if(!$this->reviews_count): ?>
	<div class="tip"><?php echo $this->translate("No review found.");?></div>
<?php else: ?>
	<script>
		let start = 0;
		let limit = 7;
		let j_q = jq.noConflict();
		let element = document.getElementById('rev');
		let course_id = <?php echo $this->course_id; ?>;
		let viewer_id = <?php echo $this->viewer_id; ?>;
		let levelId = <?php echo $this->levelId; ?>;
		let delPermission = <?php echo $this->delPermission; ?>;
		let loadingElement = document.getElementById('review-loading');
		let showLoading = () => loadingElement.style.display = 'block';
		let hideLoading = () => loadingElement.style.display = 'none';
		let toggleLikeBtn = (id,prop) => document.getElementById('like_'+id).disabled = prop;
		let toggleDislikeBtn = (id,prop) => document.getElementById('unlike_'+id).disabled = prop;
		let url = '<?php echo $this->url(array('action' => 'add-review','course_id'=>$this->course_id), 'sitecourse_review', true);?>';
		var isActive=true;
		let hasMoreReviews = () => {
			if(((start+1) * limit) >= parseInt(<?php echo $this->reviews_count ?>)){	
				window.onscroll = null;
			}
		}
		const reviewHtml = {
			checkDelete: function(viewer,owner,permission,id,url){
				html='';
				if((viewer === owner) && permission){					
					html += `<a id="delete_${id}" onclick = "Smoothbox.open('${url}')">Delete</a>`;
				}
				return html;
			},
			checkApproved: function(status){
				html='';
				if(status == 0){
					html += '<p class="review_approved" title="Disapproved"></p>'
					return html;
				} 
				return html;
			},
			rating: function(rating){
				let html = '';
				for(let i=1;i<= Math.min(Math.floor(rating), 5); ++i) {
					html += '<span class="course_browse_reviews_rates_full"><i class="fas fa-star"></i></span>';
				}
				let decimal = rating - Math.min(Math.floor(rating), 5);
				if(decimal > 0 && rating <= 5) {
					html += '<span class="course_browse_reviews_rates_half"><i class="fas fa-star-half"></i></span>';
				}
				let num = Math.floor(5 - rating);
				if(num < 0 ) num = 0;
				for(let i=1;i <= num; ++i){
					html+='<span class="no_ratings"></span>';
				}
				return html;
			}
		}
		let reviewRequestObject = {
			url : url,
			type : 'post',
			data : {
				format : 'json',
				course_id:course_id,
				start:start,
				limit:limit
			},
			success : function(responseJSON) {
				isActive=false;
				hideLoading();
				let likeArr = responseJSON.likeArr;
				console.log(likeArr);
				if(responseJSON.reviews!='false'){
					let reviews = responseJSON.reviews;
					hasMoreReviews();
					reviews.forEach( review => {
						if(review['status'] == 1 || (viewer_id === review['user_id']) || levelId === 1){

							let newDiv = document.createElement('div');
							newDiv.id = 'review_' + review['review_id'];
							newDiv.innerHTML = `
							<div class="course_browse_reviews_user_list">
							<div class="course_browse_reviews_user_block">
							<div class="course_browse_reviews_user_top">
							<div class="course_browse_reviews_user_profile">
							<span class="course_browse_reviews_rates_user_image">
							<img src="${review['owner_image'] ? review['owner_image'] :'application/modules/User/externals/images/nophoto_user_thumb_profile.png'}" alt="">
							</span>
							<span class="course_browse_reviews_rates_user_info">
							<h3>${review['owner']}</h3>
							</span>
							<div class="course_browse_reviews_user_ratings_time">
							<span class="course_browse_reviews_user_time">
							<p>(${review['diff']} ago)</p>
							</span>
							<span class="course_browse_reviews_rates">
							${reviewHtml.rating(review['rating'])}
							</span>
							</div>
							</div>							
							<div class="course_browse_reviews_user_reviews_likes">
							<span> <button class="course_reviews_like_count" id="like_${review['review_id']}" onclick="like(${review['review_id']},${1})" title="Like"> 
							<i class="fas fa-thumbs-up"  ></i>
							</button>
							<p id='like_count_${review['review_id']}'>					
							${review['like_count']}</p>
							</span>
							<span> <button class="course_reviews_dislikelike_count" id="unlike_${review['review_id']}" onclick="like(${review['review_id']},${2})" title="Dislike">
							<i class="fas fa-thumbs-down" ></i>
							</button>
							<p id='dislike_count_${review['review_id']}'>				
							${review['dislike_count']}</p>
							</span>
							<span class="course_browse_reviews_user_reviews_approval">${reviewHtml.checkApproved(review['status'])}
							</span>							
							</div>
							</div>
							<div class="course_browse_reviews_user_bottom">
							<h4>${review['review_title']}</h4>
							<div class="course_browse_reviews_user_desc">
							<p>${review['review']}</p>
							<span>${reviewHtml.checkDelete(viewer_id,review['user_id'],delPermission,review['review_id'],review['delUrl'])}
							</span>
							</div>
							</div>
							</div>
							</div>`;
							element.appendChild(newDiv);
							element.insertAdjacentHTML('beforeend','<br>');
							if( likeArr[review['review_id']] == 1){
								document.getElementById('like_'+review['review_id']).classList.add("liked");
								toggleLikeBtn(review['review_id'],true);
								toggleDislikeBtn(review['review_id'],false);
							}
							if(likeArr[review['review_id']] == 2){
								document.getElementById('unlike_'+review['review_id']).classList.add("liked");
								toggleLikeBtn(review['review_id'],false);
								toggleDislikeBtn(review['review_id'],true);
							}
						}});
					start+=7;
				}
			}
		}
		window.onscroll = scroll1;
		en4.core.runonce.add(function(){
			showLoading();
			let request = scriptJquery.ajax(reviewRequestObject);
			//request.send();
			let elem = document.querySelector('.course_browse_reviews_rates');
			//console.log(elem);
			elem.innerHTML = reviewHtml.rating('<?php echo $this->course_rating; ?>');
		});

		function scroll1(){
			course_id=<?php echo $this->course_id; ?>;
			let position = j_q(window).scrollTop();
			let bottom =j_q(document).height() - j_q(window).height();
			if( parseInt(position) == parseInt(bottom) ){
				if(!isActive){
					showLoading();
					addReview(course_id);
				}
			}
		}

		function addReview(course_id){
			let element = document.getElementById('rev');
			isActive=true;
			reviewRequestObject.data.start = start;
			let request = scriptJquery.ajax(reviewRequestObject);
			//request.send();
		}

		function like(id,value){
			let url = '<?php echo $this->url(array('action' => 'like','course_id'=>$this->course_id), 'sitecourse_review', true);?>'; 
			let likeElem = document.getElementById('like_'+id);
			let dislikeElem = document.getElementById('unlike_'+id);
			likeElem.disabled = true;
			dislikeElem.disabled = true;
			let request = scriptJquery.ajax({
				url : url,
				type : 'post',
				data : {
					format : 'json',
					review_id:id,
					value:value
				},
				success : function(responseJSON) {
				// user liked the review
				if(value == 1){
					document.getElementById('unlike_'+id).classList.remove("liked");
					document.getElementById('unlike_'+id).disabled = false;
					document.getElementById('like_'+id).classList.add("liked");
					document.getElementById('like_'+id).disabled = true;
				}
				// dislike review
				if(value == 2){
					document.getElementById('unlike_'+id).classList.add("liked");
					document.getElementById('unlike_'+id).disabled = true; 				
					document.getElementById('like_'+id).classList.remove("liked");
					document.getElementById('like_'+id).disabled = false;
				}
				const likeCnt = responseJSON.likeCnt;
				const dislikeCnt = responseJSON.dislikeCnt;
				document.getElementById('like_count_'+id).innerHTML = +likeCnt;
				document.getElementById('dislike_count_'+id).innerHTML = +dislikeCnt;				
			}
		});
		}
	</script>
<?php endif; ?>


