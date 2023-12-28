<?php 
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>
<div class="course_builder_dashboard">
    <div class="course_builder_dashboard_container">
        <?php $id=$this->course_id;
        $blockId = 1;?>
        <?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_menu.tpl'; ?>

        <div class="course_builder_dashboard_sections">
            <div class="course_builder_dashboard_sections_header">
                <div class="course_builder_dashboard_sections_header_title">
                    <img src="<?php echo $this->images['image_icon'];?>" alt="" />
                    <h3><?php echo $this->translate('Course Dashboard'); ?></h3>
                </div>
                <?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_dashboardNavigation.tpl'; ?>
            </div>
            <br>
            <div class="generic_layout_container">
                <?php if(!empty($this->video_url)): ?>
                    <div class="video_preview">
                        <?php if($this->video_type == 'upload'): ?>
                        <div class="video-course">
                            <video id="video" width="100%" height="340px" controls autoplay>
                                <source src="<?= $this->video_url; ?>" type="video/mp4" />
                                    Please try after some time
                                </video>
                            </div>
                        <?php else: ?>
                            <div class="video-course">
                                <iframe src="<?= $this->video_url; ?>" title="Video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen autoplay>
                                </iframe>
                            </div>  
                        <?php endif;?>   
                    </div>
                <?php endif; ?>

                <div class="target_your_student_video_upload">
                    <?php echo $this->htmlLink(array('route' => 'sitecourse_video_general', 'module' => 'sitecourse', 'controller' => 'video', 'action' => 'create','parent_type' =>'course','parent_id' => $this->course_id), $this->translate('Upload New Intro Video'), array(
                        'class' => 'smoothbox fas fa-video',
                    )); ?>

                    <?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'intro-video','course_id' => $this->course_id), $this->translate('Upload Existing Intro Video'), array(
                        'class' => 'smoothbox fas fa-video',
                    ));?>
                </div>

            </div>
        </div>
    </div>
</div>




