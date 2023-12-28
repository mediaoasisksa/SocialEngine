<?php $difficulty_levels = array(0=>'Beginner',1=>'Intermediate',2=>'Expert');
$height = 380;
if($this->height){
	$height = $this->height;
}
?>

<div class="slider-container" style="height:<?= $height;?>px">
	<div class="left-slide" >
		<?php foreach($this->courses as $index => $course): ?>
			<div class="slider-course-item">
				<?php 
				$storage_file = Engine_Api::_()->getItem('storage_file', $course['photo_id']);
				$src='';
				if(!empty($storage_file)){
					$src=$storage_file->map(); 
				}
				?>				
				<div class="course_browse_info">
					<span class="courses_browse_info_desc_block">
						<span class='courses_browse_info_title'>
							<h3>
								<?php  echo $course['link'];?>
							</h3>
						</span>
						<span class="courses_browse_info_desc">
							<ul class="courses_browse_info_desc_list">
								<?php if($this->enrolled_count): ?>
									<li>
										<i class="fas fa-users"></i>
										<span class="sitecourse_owl_info_desc_bold" title="Enrolled Members">
											<?php 
									if( intval($course['buyers_count']) >1){
										echo $course['buyers_count'];
										echo " Members";
									} else {
										echo $course['buyers_count'];
										echo " Member";
									} ?>
										</span>
										
									</li>
								<?php endif; ?>

								<?php if($this->course_category): ?>
									<li>
										<i class="far fa-list-alt"></i>
										<span class="sitecourse_owl_info_desc_bold" title="Category">
											<?php $item = Engine_Api::_()->getItem('sitecourse_category', $course['category_id']);
											echo $item['category_name'];?>
										</span>
										
									</li>
								<?php endif; ?>

								<?php if($this->course_difficulty): ?>
									<li>
										<i class="fas fa-level-up-alt"></i>
										<span class="sitecourse_owl_info_desc_bold" title="Difficulty Level">
											<?php echo $difficulty_levels[$course['difficulty_level']]; ?>
										</span>
										
									</li>
								<?php endif; ?>

								<?php if($this->owner_name): ?>
									<li>
										<i class="fas fa-user"></i>
										<span class="sitecourse_owl_info_desc_bold" title="Owner">
											<?php $item = Engine_Api::_()->getItem('user', $course['owner_id']);
										echo $item['displayname'];?>
										</span>
										
									</li>
								<?php endif; ?>
								
							</ul>
							
						</span>
					</span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="right-slide">
		<?php  
		$course = $this->courses;
		for($i = count($course) - 1; $i >= 0; --$i):
			$storage_file = Engine_Api::_()->getItem('storage_file', $course[$i]['photo_id']);
			$src='';
			if(!empty($storage_file)){
				$src=$storage_file->map(); 
			}
			?>
			<div style="background-image: url('<?= $src; ?>');">
			</div>
		<?php endfor; ?>
	</div>
	<div class="action-buttons">
		<button class="down-button" title="down">
			<i class="fas fa-arrow-down" ></i>
		</button>
		<button class="up-button" title="up">
			<i class="fas fa-arrow-up" ></i>
		</button>
	</div>
</div>

<script type="text/javascript">
	const sliderContainer = document.querySelector('.slider-container')
	const slideRight = document.querySelector('.right-slide')
	const slideLeft = document.querySelector('.left-slide')
	const upButton = document.querySelector('.up-button')
	const downButton = document.querySelector('.down-button')
	const slidesLength = slideRight.querySelectorAll('div').length
	const slideCourseItems = document.querySelectorAll('.slider-course-item');

	let activeSlideIndex = 0
	const height = '<?= $height;?>'
	slideLeft.style.top = `-${(slidesLength - 1) * height}px`

	upButton.addEventListener('click', () => changeSlide('up'))
	downButton.addEventListener('click', () => changeSlide('down'))

	const changeSlide = (direction) => {
		const sliderHeight = sliderContainer.clientHeight
		if(direction === 'up') {
			activeSlideIndex++
			if(activeSlideIndex > slidesLength - 1) {
				activeSlideIndex = 0
			}
		} else if(direction === 'down') {
			activeSlideIndex--
			if(activeSlideIndex < 0) {
				activeSlideIndex = slidesLength - 1
			}
		}

		slideRight.style.transform = `translateY(-${activeSlideIndex * sliderHeight}px)`
		slideLeft.style.transform = `translateY(${activeSlideIndex * sliderHeight}px)`
		// slideCourseItems[activeSlideIndex].style.backgroundColor = colorCodes[Math.floor(Math.random() * (colorCodes.length-1))]
	}

	window.addEventListener('load', () => {
		slideCourseItems.forEach((item, index) => {	
			// item.style.backgroundColor = colorCodes[Math.floor(Math.random() * index)];
		})		
	})

</script>
