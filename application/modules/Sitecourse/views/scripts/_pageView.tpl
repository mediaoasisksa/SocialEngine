<?php if(count($this->courses) > 0): ?>
    <div id="carousel_courses" class="owl-carousel owl-theme <?php echo $this->carouselClass ?>">
    </div>
<?php else: ?>
    <p><?php echo $this->translate('Cannot find a new course');?></p>
<?php endif; ?>


<script type="text/javascript">
    en4.core.runonce.add(function (){
        let courses = (<?php echo json_encode($this->courses);?>);
        document.getElementById('carousel_courses')
        .insertAdjacentHTML('beforeend',getCoursesHtml(
            courses, {'type':'carousel_courses','currency':'<?php echo $this->currency ?>'}));
        showSpecificFields(<?php echo json_encode($this->course_info); ?>, 'carousel_courses')
    });
</script>
