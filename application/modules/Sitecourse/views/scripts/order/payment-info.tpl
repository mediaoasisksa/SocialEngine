<?php 
$id = $this->course_id;
$blockId = 2;
$liId='paymet-method';
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>

<div class="course_builder_dashboard">
	<div class="course_builder_dashboard_container">

		<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_menu.tpl'; ?>

		<div class="course_builder_dashboard_sections">
			<div class="course_builder_dashboard_sections_list">
				<div class="layout_middle">
					<div class="course_builder_dashboard_sections_header">
						<div class="course_builder_dashboard_sections_header_title">
							<img src="<?php echo $this->images['image_icon'];?>" alt="" />
							<h3><?php echo $this->translate('Course Dashboard'); ?></h3>
						</div>
						<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_dashboardNavigation.tpl'; ?>
					</div>
				</div>
				<div class="generic_layout_container">
					<?php if(!empty($this->enabledgateway)): ?>
						<!-- paypal is enabled -->
						<?php if(!empty($this->enabledgateway['paypal']) && $this->enabledgateway['paypal']): ?>
							<?php echo $this->form->render($this); ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
</div>




