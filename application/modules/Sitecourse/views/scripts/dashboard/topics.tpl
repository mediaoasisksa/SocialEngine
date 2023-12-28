<?php $id = $this->course_id; 
$liId='builder'; ?>

<?php 

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>


<div class="course_builder_dashboard">
	<div class="course_builder_dashboard_container">

		<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_menu.tpl'; ?>

		<div class="course_builder_dashboard_sections">
			<div class="course_builder_dashboard_sections_list">
				<!-- Section_3 -->
				<div class="course_builder_dashboard_block">
					<div class="course_builder_dashboard_sections_header">
						<div class="course_builder_dashboard_sections_header_title">
							<img src="<?php echo $this->images['image_icon'];?>" alt="" />
							<h3><?php echo $this->translate('Course Dashboard'); ?></h3>
						</div>
						<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_dashboardNavigation.tpl'; ?>
					</div>
					<div class="course_builder_dashboard_sections_header_right">
						<div class="course_builder_dashboard_add_button">
							<div class="course_builder_dashboard_add_block">
								<div class="course_builder_dashboard_add_button_main">
									<?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'add-topic','course_id'=>$this->course_id), $this->translate('Add New Topic'), array(
										'class' => 'smoothbox buttonlink fas fa-plus',
									))?>
								</div>
							</div>
						</div>
						<span class="course_builder_dashboard_sections_header_collapse">
							<a href="javascript:void(0)" onclick="expandAll()" style="display: block;" class="item-expand">
								Expand All
								<i class="fas fa-chevron-up"></i>
							</a>
							<a href="javascript:void(0)" onclick="collapseAll()" style="display: none;" class="item-collapse">
								Collapse All
								<i class="fas fa-chevron-down"></i>
							</a>
						</span>
					</div>

					<div class="course_builder_dashboard_mains">
						<div class="course_builder_dashboard_main_block">
							<?php if(count($this->topics) > 0): ?>
								<!-- <form method="POST"> -->
									<div class="topics">
										<?php foreach ($this->topics as $index => $value): ?>

											<div class="course_builder_dashboard_list dropzone" draggable="true" id="<?php echo $value['topic_id'] ?>" name="order[]" ondragstart="drag(event)" ondragover="allowDrop(event)" ondrop="drop(event)">

												<span class="course_builder_dashboard_list_accordion">
													<p>
														<i class="fas fa-arrows-alt" ></i>
														<?php  echo $value['title']; ?>
													</p>
													<span class="course_builder_dashboard_list_icons">
														<span class="course_builder_dashboard_icons_list">
															<!--  <i class="far fa-edit"> -->

															</span>


															<span class="course_builder_dashboard_icons_list">
																<!--  <i class="far fa-edit"> -->
																	<?php echo $this->htmlLink(array('route' => 'sitecourse_doc_specific', 'module' => 'sitecourse', 'controller' => 'doc', 'action' => 'add-doclesson', 'topic_id' =>$value['topic_id']),'Document', array(
																		'class' => 'smoothbox',
																	)); ?>
																</span>
																
																
																<span class="course_builder_dashboard_icons_list">
																<!--  <i class="far fa-edit"> -->
																	<?php echo $this->htmlLink(array('route' => 'sitecourse_doc_specific', 'module' => 'sitecourse', 'controller' => 'doc', 'action' => 'add-lesson-video-invite', 'topic_id' =>$value['topic_id']),'Video Invite', array(
																		'class' => 'smoothbox',
																	)); ?>
																</span>
																<span class="course_builder_dashboard_icons_list">
																	<!--  <i class="far fa-edit"> -->
																		<?php echo $this->htmlLink(array('route' => 'sitecourse_topic_specific', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'add-lesson', 'topic_id' =>$value['topic_id']),'Text', array(
																			'class' => 'smoothbox',

																		)); ?>
																	</span>

																	<span class="course_builder_dashboard_icons_list">
																		<?php echo $this->htmlLink(array('route' => 'sitecourse_video_general', 'module' => 'sitecourse', 'controller' => 'video', 'action' => 'create', 'topic_id' =>$value['topic_id'],'parent_type'=>'lesson','course_id'=>$id), $this->translate('Video'), array(
																			'class' => 'smoothbox',
																		)); ?>
																	</span>


																	<?php if($this->buyersCount == 0): ?>
																		<span class="course_builder_dashboard_icons_list">
																			<!--  <i class="far fa-edit"> -->
																				<?php echo $this->htmlLink(array('route' => 'sitecourse_topic_specific', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'edit-topic', 'topic_id' =>$value['topic_id']),'', array(
																					'class' => 'smoothbox far fa-edit',

																				)); ?>
																				<!-- </i> -->
																			</span>
																			<span class="course_builder_dashboard_icons_list">
																				<!-- <i class="far fa-trash-alt"> -->
																					<?php echo $this->htmlLink(array('route' => 'sitecourse_topic_specific', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'delete-topic', 'topic_id' =>$value['topic_id'],'course_id'=>$id),'', array(
																						'class' => 'smoothbox far fa-trash-alt',
																					)); ?>
																					<!-- </i> -->
																				</span>
																			<?php endif; ?>
																			<span
																			class="course_builder_dashboard_icons_list dropdown_icon"
																			>
																			<i class="fas fa-chevron-down"></i>
																		</span>
																	</span>
																</span>
																<div class="course_builder_dashboard_list_accordion_panel">

																	<?php  //print_r($this->lessons); ?>
																	<?php foreach ($this->lessons as $index1 => $value1): ?>
																		<?php if($value['topic_id'] == $value1['topic_id']): ?>
																			<div class="course_builder_topic_lessons">
																				<?php  echo $value1['title']; ?>
																				<?php if($this->buyersCount == 0): ?>
																					<?php echo $this->htmlLink(array('route' => 'sitecourse_lesson_specific', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'delete-lesson', 'lesson_id' =>$value1['lesson_id']),'', array(
																						'class' => 'smoothbox far fa-trash-alt',
																					)); ?>
																				<?php endif; ?>
																			</div>
																		<?php endif; ?>
																	<?php endforeach; ?>            
																</div>
																<!-- </form> -->
															</div>
														<?php endforeach; ?>
													</div>
												<?php else: ?>
												<?php endif; ?>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">
	var acc = document.getElementsByClassName(
		"course_builder_dashboard_list_accordion"
		);
	var i;

	for (i = 0; i < acc.length; i++) {
		acc[i].addEventListener("click", function () {
			this.classList.toggle("course_builder_dashboard_list_accordion_active");
			var panel = this.nextElementSibling;
			if (panel.style.display === "block") {
				panel.style.display = "none";
			} else {
				panel.style.display = "block";
			}
		});
	}
</script>

<script type="text/javascript">
	function allowDrop(ev) {
		ev.preventDefault();
	}

	function drag(ev) {
		ev.dataTransfer.setData("text", ev.target.id);
	}

	function drop(ev) {
		ev.preventDefault();
		let data = ev.dataTransfer.getData("text");
		let source = scriptJquery('div#'+data+'.course_builder_dashboard_list')[0];
		let count = 0;
		let swapElem = scriptJquery(ev.target).parents('div.course_builder_dashboard_list')[0];
		let pickedElem = source;
		while(!swapElem.classList.contains('dropzone') && ++count <= 6) {
			swapElem = swapElem.parentNode;
		}
		if(swapElem.classList.contains('dropzone') && 
			source.classList.contains('dropzone')
			) {
		let contentDiv = document.querySelector('.topics');

		let swapNextSibling = swapElem.nextSibling;
		let pickedNextSibling = pickedElem.nextSibling;

		contentDiv.insertBefore(pickedElem, swapNextSibling);
		contentDiv.insertBefore(swapElem, pickedNextSibling);

		orderChangeAjax(pickedElem.id, swapElem.id);
	}

}

function orderChangeAjax(src_id,dest_id){
	let url = '<?php echo $this->url(array('action' => 'orderchange','course_id'=>$this->course_id), 'sitecourse_dashboard', true);?>';
	en4.core.request.send(scriptJquery.ajax({
		url : url,
		data : {
			format : 'json',
			src:src_id,
			dest:dest_id
		},
		success : function(responseJSON) {
			if(responseJSON.changed){
			}
		}
	}));
}


function expandAll(){
	const expandElem = document.querySelector('.item-expand');
	const collapseElem = document.querySelector('.item-collapse');
	expandElem.style.display = 'none';
	collapseElem.style.display = 'block';
	const lessonsList = document.querySelectorAll('.course_builder_dashboard_list_accordion_panel');
	const topicsList = document.querySelectorAll('.course_builder_dashboard_list_accordion')

	lessonsList.forEach(element => {
		element.style.display = "block";
	})
	const className = 'course_builder_dashboard_list_accordion_active';
	topicsList.forEach(element => {
		if(!element.classList.contains(className))
			element.classList.add(className)
	})
}

function collapseAll(){
	const expandElem = document.querySelector('.item-expand');
	const collapseElem = document.querySelector('.item-collapse');
	expandElem.style.display = 'block';
	collapseElem.style.display = 'none';
	const lessonsList = document.querySelectorAll('.course_builder_dashboard_list_accordion_panel');
	const topicsList = document.querySelectorAll('.course_builder_dashboard_list_accordion')

	lessonsList.forEach(element => {
		element.style.display = "none";
	})
	const className = 'course_builder_dashboard_list_accordion_active';
	topicsList.forEach(element => {
		if(element.classList.contains(className))
			element.classList.remove(className)
	})
}
</script>
