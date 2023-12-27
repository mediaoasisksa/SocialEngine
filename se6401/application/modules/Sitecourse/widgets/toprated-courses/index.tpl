<div id="toprated_courses">
</div>


<script>
    en4.core.runonce.add(function (){
        let courses = (<?php echo json_encode($this->courses);?>);
        document.getElementById('toprated_courses')
        .insertAdjacentHTML('beforeend',getCoursesHtml(
            courses, {'type':'toprated_courses','currency':'<?php echo $this->currency ?>'}));
        showSpecificFields(<?php echo json_encode($this->course_info); ?>, 'toprated_courses')
    });
</script>
