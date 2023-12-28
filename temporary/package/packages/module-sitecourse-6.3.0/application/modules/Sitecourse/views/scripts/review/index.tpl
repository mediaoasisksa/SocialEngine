
<?php if ($this->canRate): ?>
	<div>
		<div class="review">

			<form method="post" onsubmit="return validateMyForm();" class="global_form_box" id = "reviewForm" action="<?php echo $this->url(array('module' => 'sitecourse', 'controller' => 'review', 'action' => 'index', 'course_id' => $this->course_id, ),'sitecourse_review', true) ?>">

				<div>
					<label>
						<?php echo  $this->translate("Review Title") ?>
					</label>
					<?php if(empty($this->review_title)): ?>
						<input type="text" name="review_title" required />
					<?php else:?>
						<input type="text" name="review_title" value="<?php echo $this->review_title; ?>" required />
					<?php endif; ?>
				</div>
				<br>

				<?php $showCursor = 0; ?>
				<?php if (!empty($this->viewer_id) && (!empty($this->canRate))): ?>
				<?php $showCursor = 1; ?>
			<?php endif; ?>
			
			<div id="course_rating" class="rating" onmouseout="rating_out();">
				<span id="rate_1" class="seao_rating_star_generic" <?php if (!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
				<span id="rate_2" class="seao_rating_star_generic" <?php if (!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
				<span id="rate_3" class="seao_rating_star_generic" <?php if (!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
				<span id="rate_4" class="seao_rating_star_generic" <?php if (!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
				<span id="rate_5" class="seao_rating_star_generic" <?php if (!empty($this->viewer_id) && (empty($this->rated) || (!empty($this->rated) && ($this->update_permission)))) : ?> onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
			</div>
			<div id="rating_text" class="rating_text mright5"><?php echo $this->translate('click to rate'); ?></div>
			<ul class="form-errors" style="display: none;">
				<li>
					<ul class="errors">
						<li><?php echo $this->translate("Please complete this field - it is required."); ?></li>
					</ul>
				</li>
			</ul>
			<br>
			<div>
				<label>
					<?php echo  $this->translate("Review") ?>
				</label>
				<?php if(empty($this->review)): ?>
					<input type="text" name="review" required />
				<?php else:?>
					<input type="text" name="review" value="<?php echo $this->review; ?>" required/>
				<?php endif; ?>
			</div>
			<br>
			<input type="hidden" name="rating" />
			<div class="sitecourse_review_submit_button">
				<span><button type="submit" name="submit" onclick="submitForm()"><?php echo $this->translate("review") ?></button></span>
				<span><button type="submit" name="cancel" onclick="parent.Smoothbox.close()"><?php echo $this->translate("Cancel") ?></button></span>
			</div>
		</form>
	</div>
</div>
<br />
<?php else:

?><div class="course-rating-not-active tip"><span><?php if($this->isOwner){
	echo $this->translate("Course owner cannot rate on their courses");
}else {
	echo $this->translate("You cannot rate on this course");
} ?></span>

</div>

<?php
endif;
?>

<script type="text/javascript">
	var course_rate = 0;
	const validateMyForm = () => {
		if(form.elements['rating'].value == 0){
		const errorsEle = document.querySelector('.form-errors');
		errorsEle.style.display = 'block';
		}
		return course_rate > 0;
	}
	function submitForm(){
		if(document.getElementById('reviewForm')){
			form=document.getElementById('reviewForm');
			if(!isNaN(course_rate)){
				if(course_rate>5){
					course_rate=5;
				}
				form.elements['rating'].value = course_rate;
			}      
		}
	}

	

	en4.core.runonce.add(function () {
		let subject_pre_rate =  <?php echo $this->subject_pre_rate; ?>;
		let update_permission = <?php echo $this->update_permission; ?>;
		let subject_rated = '<?php echo $this->rated; ?>';
		let subject_id = '<?php echo $this->course_id; ?>';
		let subject_total_votes = <?php echo $this->rating_count; ?>;
		let viewer = '<?php echo $this->viewer_id; ?>';
		subject_new_text = '';
		course_rate = <?php echo $this->subject_pre_rate; ?>;
		let rating_over = window.rating_over = function (rating) {
			if (subject_rated == 1 && update_permission == 0) {
				document.getElementById('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript(($this->translate('you already rated'))) ?>";
                //set_rating();
            } else if (viewer == 0) {
            	document.getElementById('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('please login to rate')); ?>";
            } else {
            	document.getElementById('rating_text').innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('click to rate')); ?>";
            	for (var x = 1; x <= 5; x++) {
            		if (x <= rating) {
            			document.getElementById('rate_' + x).setAttribute('class', 'seao_rating_star_generic rating_star_y');
            		} else {
            			document.getElementById('rate_' + x).setAttribute('class', 'seao_rating_star_generic seao_rating_star_disabled');
            		}
            	}
            }
        };

        var rating_out = window.rating_out = function () {
              if (subject_pre_rate != 0) {
              	set_rating();
              }
              else {
              	for (var x = 1; x <= 5; x++) {
              		document.getElementById('rate_' + x).setAttribute('class', 'seao_rating_star_generic seao_rating_star_disabled');
              	}
              }
          };

          var set_rating = window.set_rating = function () {
          	var subject_rating = subject_pre_rate;
              for (var x = 1; x <= parseInt(subject_rating); x++) {
              	document.getElementById('rate_' + x).setAttribute('class', 'seao_rating_star_generic rating_star_y');
              }

              for (var x = parseInt(subject_rating) + 1; x <= 5; x++) {
              	document.getElementById('rate_' + x).setAttribute('class', 'seao_rating_star_generic seao_rating_star_disabled');
              }

              var remainder = Math.round(subject_rating) - subject_rating;
              if (remainder <= 0.5 && remainder != 0) {
              	var last = parseInt(subject_rating) + 1;
              	document.getElementById('rate_' + last).setAttribute('class', 'seao_rating_star_generic rating_star_half_y');
              }
          };

          var rate = window.rate = function (rating) {
          	course_rate=rating;
          	subject_pre_rate=rating;
          	set_rating();
          }

          set_rating();
      });
  </script>

  <style type="text/css">
  	<?php if ($showCursor == 0) { ?>
  		.layout_sitecourse_user_ratings .rating_star_big_generic{
  			cursor: default;
  		}
  	<?php } ?>
  </style>
