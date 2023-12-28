<?php 
$id = $this->course_id;
$blockId = 2;
$liId = 'enrolled-members';
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
        </div>
        <div class="generic_layout_container">
         <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <div>    
                <h3>
                    <?php echo $this->translate("Enrolled Members Info "); ?>
                </h3>

            </div>
            <table class='admin_table' width="100%">
                <thead>
                    <tr><th><?php echo $this->translate("ID") ?></th>
                        <th><?php echo $this->translate("Username") ?></th>
                        <th><?php echo $this->translate("Transaction Id"); ?></th>
                        <th><?php echo $this->translate("Date"); ?></th>
                        <th><?php echo $this->translate("Email") ?></th>
                    </tr>
                </thead>
                <tbody>
                 <?php foreach( $this->paginator as $details ): ?>
                    <tr>

                        <td><?=$details['buyerdetail_id'];?></td>
                        <td>
                            <?php $user = Engine_Api::_()->user()->getUser($details['user_id']); 
                            echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank'));?>
                            
                        </td>
                        <td>
                            <?php 
                            echo $this->htmlLink(array('route' => 'admin_default','module' => 'sitecourse','controller' => 'manage','action' => 'detail-transaction','transaction_id' => $details['transaction_id'],), $details['gateway_transaction_id'], array('target' => '_blank')) ?>

                        </td>
                        <td><?= date('d-M-Y',strtotime($details['date'])); ?></td>
                        <td><?=$details['email'];?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("No one enrolled in the course yet."); ?>
            </span>
        </div>
    <?php endif; ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true,)); ?>
</div>
</div>
</div>
</div>

