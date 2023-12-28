<div id="newest_courses">
</div>


<script>
	en4.core.runonce.add(function (){
		let courses = (<?php echo json_encode($this->courses);?>);
		document.getElementById('newest_courses')
		.insertAdjacentHTML('beforeend',getCoursesHtml(
			courses, {'type':'newest_courses','currency':'<?php echo $this->currency ?>'}));
		showSpecificFields(<?php echo json_encode($this->course_info); ?>, 'newest_courses')
	});
</script>
