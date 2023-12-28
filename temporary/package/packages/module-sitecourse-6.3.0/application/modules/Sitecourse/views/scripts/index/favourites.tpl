<?php if(count($this->favouriteCourses)): ?>

    <?php foreach($this->favouriteCourses as $course): ?>
        <div class="item">
            <p><?=$course['title'];?></p>
        </div>

    <?php endforeach; ?>
<?php else: ?>
    <div class="tip">
        <?php echo $this->translate("Please add a course into favourites."); ?>
    </div>
<?php endif; ?>
