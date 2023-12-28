<div id="bestseller_courses">
</div>


<script>
    en4.core.runonce.add(function (){
        let courses = (<?php echo json_encode($this->courses);?>);
        document.getElementById('bestseller_courses')
        .insertAdjacentHTML('beforeend',getCoursesHtml(
            courses, {'type':'bestseller_courses','currency':'<?php echo $this->currency ?>'}));
    });
</script>
