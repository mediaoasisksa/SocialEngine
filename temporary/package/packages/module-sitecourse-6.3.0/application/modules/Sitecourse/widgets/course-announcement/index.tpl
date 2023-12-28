<div class="course_annoncement_main_block">
        <span class="course_annoncement_main_icon">
            <i class="fas fa-bullhorn"></i>
            <p><?php echo $this->translate("Announcement"); ?></p>
        </span>
        <div class="course_annoncement_main">                
            <marquee width="100%" behavior="scroll" class="post-announcements-slider">
                <?php foreach($this->announcements as $index => $announcements): ?>
                    <p>
                        <?php echo $announcements['body']; ?>
                    </p>
                <?php endforeach; ?>
            </marquee>
        </div>
</div>

    
