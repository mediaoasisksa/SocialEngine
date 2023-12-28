<?php ?>
<?php if(count($this->teams) > 0) { ?>
  <?php $bgImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsbgimage', ''); ?>
  <?php $heading = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsheading', 'Our Teams'); ?>
  <div class="lp_team_section lp_section_box sesbasic_bxs">
    <section id="lp_team" class="lp_about_main_section sesbasic_clearfix" style='background-image:url("<?php echo $bgImage ?>");'>
      <?php if($heading) { ?>
        <div class="lg_team_banner wow animated fadeInUp animated" data-animate="fadeInUp-parent">
          <h2><?php echo $this->translate($heading); ?></h2>
        </div>
      <?php } ?>
      <div class="lp_team_main">
        <?php $i=1; ?>
        <?php foreach($this->popularMembers as $team) { ?>
        <?php
        // Member type
    $subject = $team;
    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
    $memberType = '';
    if( !empty($fieldsByAlias['profile_type']) )
    {
      $optionId = $fieldsByAlias['profile_type']->getValue($subject);
      if( $optionId ) {
        $optionObj = Engine_Api::_()->fields()
          ->getFieldsOptions($subject)
          ->getRowMatching('option_id', $optionId->value);
        if( $optionObj ) {
          $memberType = $optionObj->label;
        }
      }
    }
    ?>
          <?php $modal = 'modal'.$i; ?>
          <div class="team_block">
            <div class="team_box wow slideInUp">
              <div class="member-img">
                <?php if($team->photo_id) : ?>
                  <?php $item = Engine_Api::_()->getItem('user', $team->user_id); 
                  //$this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'))
                  ?>
                  <img src="<?php echo $item->getPhotoUrl(); ?>" />
               
                <?php else:?>
                <?php $baseUrl = $this->baseUrl();?>
                    <?php $url = "$baseUrl/application/themes/sescompany/images/nophoto_user_thumb_profile.png";?>
                    <img src="<?php echo $url; ?>" />
                <?php endif; ?>
                <button onclick="document.getElementById('<?php echo $modal ?>').style.display='block'" class="modal-button"><i class="fa fa-bars"></i></button>
              </div>
              <div class="member-content">
                <h2><a href="<?php echo $team->getHref();?>"><?php echo $team->getTitle(); ?></a></h2>
                <p><?php echo $memberType;?></p>
              </div>
            </div>
          </div>
        <?php $i++; } ?>
      </div>
    </section>
  </div>
  <?php $j = 1; ?>
  <?php foreach($this->popularMembers as $team) { ?>
    <?php $modalj = 'modal'.$j; ?>
    <?php
        // Member type
    $subject = $team;
    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
$memberType = '';
    if( !empty($fieldsByAlias['profile_type']) )
    {
      $optionId = $fieldsByAlias['profile_type']->getValue($subject);
      if( $optionId ) {
        $optionObj = Engine_Api::_()->fields()
          ->getFieldsOptions($subject)
          ->getRowMatching('option_id', $optionId->value);
        if( $optionObj ) {
          $memberType = $optionObj->label;
        }
      }
    }
    ?>
    <div id="<?php echo $modalj ?>" class="member-modal" style="display:none;">
      <div class="member-modal-content">
        <div class="modal-container"> <span onclick="document.getElementById('<?php echo $modalj ?>').style.display='none'" class="button-topright">&times;</span>
          <div class="modal-member-img"> 
            <?php if($team->photo_id) { ?>
                  <?php $item = Engine_Api::_()->getItem('user', $team->user_id); 
                  //$this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'))
                  ?>
                  <img src="<?php echo $item->getPhotoUrl(); ?>" />
                <?php } ?>
           
          </div>
          <div class="modal-member-content">
            <h2><a href="<?php echo $team->getHref();?>"><?php echo $team->getTitle(); ?></a></h2>
            <p><?php echo $memberType;?></p>
            
          </div>
        </div>
      </div>
    </div>
  <?php $j++; } ?>
<?php } ?>