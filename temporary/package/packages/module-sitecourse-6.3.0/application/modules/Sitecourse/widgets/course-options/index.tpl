<div class="course_browse_profile_option">
    <div class="course_browse_profile_option_main">

        <span class="course_browse_profile_option_media_icons">

            <?php if($this->is_owner): ?>
                <span class="course_browse_profile_option_edit" title="Edit">
                 <?php echo $this->htmlLink(array('route' => 'sitecourse_specific', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'edit','course_id'=>$this->course_id,),'Edit Course',array('class'=>'fas fa-edit')); ?>
             </span>
             <?php if($this->deletePermission): ?>
                <span class="course_browse_profile_option_edit" title="Delete">
                     <?php echo $this->htmlLink(array('route' => 'sitecourse_specific', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'course-delete','course_id'=>$this->course_id,),'Delete Course',array('class'=>'smoothbox fas fa-trash')); ?>
                 </span>
             <?php endif; ?>
         <?php endif; ?>

         <span class="course_browse_profile_option_share" title="Share">
            <a data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true' class="smoothbox" href="<?php echo $this->escape($this->url( array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $this->resource_type, 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>">
             <i class="fas fa-share-square"></i>
             <?php echo $this->translate('Share Course'); ?>
         </a>
     </span>

     <?php if(!$this->is_owner): ?>

        <span class="course_browse_profile_option_message" title="Message">
         <?php echo $this->htmlLink(array('route' => 'sitecourse_specific', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'messageowner','course_id'=>$this->course_id),'Message Owner', array('class' => 'smoothbox fas fa-envelope',)); ?>
     </span>

     <?php if($this->canReport): ?>
        <span class="course_browse_profile_option_report" title="Report">
            <?php echo $this->htmlLink(array('route' => 'sitecourse_specific', 'module' => 'sitecourse', 'controller' => 'index', 'action' => 'report','course_id'=>$this->course_id), 'Report Course', array('class' => 'smoothbox fas fa-bug',)); ?>                
        </span>
    <?php endif; ?>

<?php endif; ?>

</span>
</div>
</div>

