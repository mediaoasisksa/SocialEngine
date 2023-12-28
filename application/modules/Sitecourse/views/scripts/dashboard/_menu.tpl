<?php $course= Engine_Api::_()->getItem('sitecourse_course',$id);
?>
<div class="course_builder_dashboard_menus">
	<ul class="course_builder_dashboard_menus_list">
		<div class="course_builder_dashboard_profile_card">
			<div class="course_builder_dashboard_profile_cards">
				<div class="course_builder_dashboard_profile_card_block">
					<span class="course_builder_dashboard_profile_image">
						<img src="<?php echo $this->images['image']; ?>" alt="" />
					</span>
					<h2 class="course_builder_dashboard_profile_title">
						<?php echo $this->translate('Course Dashboard'); ?>
					</h2>
				</div>
			</div>
		</div>

		<section class="course_builder_dashboard_menus_accordions">
			<div class="course_builder_dashboard_menus_accordion">
				<div class="course_builder_dashboard_menus_accordion-heading">
					<h3><?=$this->translate('Content')?></h3>
					<span
					class="course_builder_dashboard_menus_state-indication plus"
					></span>
				</div>
				<div id='1' class="course_builder_dashboard_menus_accordion-body">
					<ul class="course_builder_dashboard_menus_accordion-body_list">
						<li id ='edit' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_specific', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'edit', 'course_id' =>$id,), $this->translate('Edit Info'), array(
							)); ?></li>
						<li id ='target' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'targetstudents', 'course_id' =>$id,), $this->translate('Target Your Students')); ?></li>
						<li id ='builder' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'topics', 'course_id' =>$id,), $this->translate('Course Builder')); ?></li>
						<li id='overview' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'course-overview', 'course_id' =>$id,), $this->translate('Course Overview')); ?></li>
						<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.announcements',1)): ?>
						<li id='announcement'class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'announcements', 'course_id' =>$id,), $this->translate('Announcements')); ?></li>
					<?php endif; ?>
					<li id='picture'class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'course-picture', 'course_id' =>$id,), $this->translate('Add Photo and Signature')); ?></li>
					<li id='intro-video' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'course-intro-video', 'course_id' =>$id), $this->translate('Add Intro Video')); ?></li>
					

					</ul>
				</div>
			</div>

			<div class="course_builder_dashboard_menus_accordion">
				<div class="course_builder_dashboard_menus_accordion-heading">
					<h3><?=$this->translate('Payment Info')?></h3>
					<span
					class="course_builder_dashboard_menus_state-indication plus"
					></span>
				</div>
				<div id='2' class="course_builder_dashboard_menus_accordion-body">
					<ul class="course_builder_dashboard_menus_accordion-body_list">
					<li id='enrolled-members' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'enrolled-members', 'course_id' =>$id,), $this->translate('Enrolled Members')); ?></li>

					<li id='paymet-method' class="course_builder_dashboard_menus_accordion-body_list-element"><?php echo $this->htmlLink(array('route' => 'sitecourse_order', 'module' => 'sitecourse', 'controller' => 'order', 'action' => 'payment-info', 'course_id' =>$id,), $this->translate('Payment Methods')); ?></li>
					<li id='transaction'class="course_builder_dashboard_menus_accordion-body_list-element">
						<?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'transactions', 'course_id' =>$id,), $this->translate('Transactions / Payments')); ?>
						
					</li>
				</ul>
			</div>
		</div>

		
	</section>
</ul>
</div>



<script type="text/javascript">
	const accordions = document.getElementsByClassName(
		"course_builder_dashboard_menus_accordion-heading"
		);

	for (const acc of accordions) {
		acc.addEventListener("click", function () {
			const body = this.nextElementSibling;
			body.classList.toggle("open");
			const indication = this.querySelector(
				".course_builder_dashboard_menus_state-indication"
				);
			if (indication.classList.contains("plus")) {
				indication.classList.remove("plus");
				indication.classList.add("minus");
			} else if (indication.classList.contains("minus")) {
				indication.classList.remove("minus");
				indication.classList.add("plus");
			}
		});
	}

	const blockId = '<?php echo $blockId ?? '1'; ?>'; 
	const elementAcc = document.querySelectorAll(".course_builder_dashboard_menus_accordion-body");
	

	elementAcc.forEach(element => {
		if(element.id == blockId){
			element.classList.add('open');
			let indicator = element.previousElementSibling.querySelector('.course_builder_dashboard_menus_state-indication');
			indicator.classList.add("minus");
			indicator.classList.remove("plus");
		}
	});

	const liId = '<?php echo $liId ?? 'edit'; ?>';
	const liElementAcc = document.querySelectorAll(".course_builder_dashboard_menus_accordion-body_list-element");
	liElementAcc.forEach(element => {
		if(element.id == liId){
			element.classList.add('active');
		} else {
			element.classList.remove('active');
		}
	});

</script>
