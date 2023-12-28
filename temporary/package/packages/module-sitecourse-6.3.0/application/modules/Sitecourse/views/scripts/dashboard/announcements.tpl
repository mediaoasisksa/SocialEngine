<?php 
$id = $this->course_id;
$blockId = 1;
$liId = 'announcement';
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

            <div class="generic_layout_container">
                <h2><?php echo $this->translate("Manage Announcements");?></h2>
                <p><?php echo $this->translate(" Post Announcements for your Courses. Announcements will appear on the Course View Page."); ?></p>

                <div class="course_builder_dashboard_announcement">
                    <span>
                        <i class="fas fa-bullhorn"></i>
                        <?php echo $this->htmlLink(array('route' => 'sitecourse_dashboard', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'create-announcement','course_id' => $this->course_id), $this->translate('Post New Announcement'), array(
                            'class' => 'smoothbox',
                            ));?></span>
                        </div>

                        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
                         <?php foreach( $this->paginator as $announcement ): ?>
                            <div class="item-announcement">
                                <h4><?php echo $announcement['title'];?></h4>
                                <p><?php echo $announcement['body'];?></p>
                                <span class="item-announcement-check"><input onclick="toggleAnnouncement(this,<?php echo $announcement['announcement_id'];?>)"type="checkbox" name="enable" <?php if($announcement['enable'])echo "checked"; ?> > <?php echo $this->translate("Enable Announcement") ?>
                                </span>

                                <div class="item-options">
                                    <span><?php echo $this->htmlLink(array('route' => 'sitecourse_announcement', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'edit-announcement','announcement_id' => $announcement['announcement_id']), $this->translate('edit'), array(
                                        'class' => 'smoothbox',
                                        ));?></span>
                                    <span><?php echo $this->htmlLink(array('route' => 'sitecourse_announcement', 'module' => 'sitecourse', 'controller' => 'dashboard', 'action' => 'delete-announcement','announcement_id' => $announcement['announcement_id']), $this->translate('delete'), array(
                                        'class' => 'smoothbox',
                                        ));?></span>
                                    </div>
                                </div>



                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="tip">
                                <span>
                                    <?php echo $this->translate("No Announcements have been posted for this course yet."); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true,)); ?>
                  </div>

                  <form id="announcement-create" method="post">
                    <input type="hidden" name="enable" value="0">
                    <input type="hidden" name="id">
                </form>

            </div>
        </div>
    </div>
</div>
</div>


<script type="text/javascript">
    function toggleAnnouncement(elem,id){
        const form = document.getElementById('announcement-create');
        form.elements['enable'].value = elem.checked;
        form.elements['id'].value = id;
        form.submit();
    }
</script>
