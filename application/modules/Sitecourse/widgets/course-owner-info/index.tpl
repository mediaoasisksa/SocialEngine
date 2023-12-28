<?php 
$info = $this->ownerInfo;

if($info && !empty($info)): ?>
    <div class="course_browse_owner_info">
        <div class="course_browse_owner_info_main">
            <div class="course_browse_owner_profile">
                <span class="course_browse_owner_profile_image">
                    <img src="<?= $this->ownerImg; ?>" alt="Creator Image">
                </span>
                <span class="course_browse_owner_profile_name">
                    <h3>
                        <?= $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('target' => '_blank')); ?>
                    </h3>
                </span>
                <span class="course_browse_owner_profile_email">
                    <p><?=$info['email'];?></p>
                </span>
                <span class="course_browse_owner_profile_course">
                    <i class="fas fa-chalkboard"></i>
                    <p>
                        <?php  echo $this->translate(array('%s Course', '%s Courses', $this->ownerCourseNumber), $this->locale()->toNumber($this->ownerCourseNumber)) ?>
                    </p>
                    
                </span>
            </div>
        </div>
    </div>
<?php endif;?>   
