<!-- CONTACT -->
<?php if( !empty($this->providerItem->telephone_no) ): ?>
  <button class="">
    <?php if(Engine_API::_()->seaocore()->isMobile()): ?>
      <a href="tel:<?php echo $this->providerItem->telephone_no; ?>" class="btn btn-default"><?php echo $this->translate('Call Us') ?></a>
    <?php else:?>
      <?php
        echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'contact-us', 'pro_id' => $this->pro_id, 'format' => 'smoothbox'), $this->translate('Call Us'), array(
          'class' => 'buttonlink smoothbox icon_service_contact'
        ));
      ?>
    <?php endif;?>
  </button>
<?php endif;?>

<!-- REVIEW -->
<?php if( $this->reviewHide == '0'): ?>
  <?php if($this->flag == '0'): ?>
    <button ><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'review', 'action' => 'index', 'subject' => $this->subject()->getGuid(), 'format' => 'smoothbox'), $this->translate('Write a Review'), array('class' => 'smoothbox icon_service_edit buttonlink'));
  	?>
    </button>
  <?php elseif($this->flag == '1'): ?>
    <button ><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'review', 'action' => 'index', 'subject' => $this->subject()->getGuid(), 'format' => 'smoothbox'), $this->translate('Update Review'), array('class' => 'smoothbox icon_service_edit buttonlink'));?>
    </button>
  <?php endif;?>
<?php endif;?>