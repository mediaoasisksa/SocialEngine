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
        <?php foreach($this->teams as $team) { ?>
          <?php $modal = 'modal'.$i; ?>
          <div class="team_block">
            <div class="team_box wow slideInUp">
              <div class="member-img">
                <?php if($team->file_id) { ?>
                  <?php $item = Engine_Api::_()->getItem('sescompany_team', $team->team_id); ?>
                  <img src="<?php echo $item->getFilePath('file_id'); ?>" />
                <?php } ?>
                <button onclick="document.getElementById('<?php echo $modal ?>').style.display='block'" class="modal-button"><i class="fa fa-bars"></i></button>
              </div>
              <div class="member-content">
                <h2><?php echo $team->name; ?></h2>
                <?php if($team->designation) { ?>
                  <p><?php echo $team->designation; ?></p>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php $i++; } ?>
      </div>
    </section>
  </div>
  <?php $j = 1; ?>
  <?php foreach($this->teams as $team) { ?>
    <?php $modalj = 'modal'.$j; ?>
    <div id="<?php echo $modalj ?>" class="member-modal" style="display:none;">
      <div class="member-modal-content">
        <div class="modal-container"> <span onclick="document.getElementById('<?php echo $modalj ?>').style.display='none'" class="button-topright">&times;</span>
          <div class="modal-member-img"> 
            <?php if($team->file_id) { ?>
              <?php $item = Engine_Api::_()->getItem('sescompany_team', $team->team_id); ?>
              <img src="<?php echo $item->getFilePath('file_id'); ?>" />
            <?php } ?>
            <?php if($team->quote) { ?>
              <div class="member-quote">
                <p><i class="fa fa-quote-left"></i><?php echo $this->translate($team->quote); ?></p>
              </div>
            <?php } ?>
          </div>
          <div class="modal-member-content">
            <h2><?php echo $team->name; ?></h2>
            <?php if($team->designation) { ?>
              <h4><?php echo $team->designation; ?></h4>
            <?php } ?>
            <?php if($team->description) { ?>
              <p><?php echo $team->description; ?></p>
            <?php } ?>
            <?php if($team->phone) { ?>
              <p><?php echo $this->translate("Phone : %s", $team->phone); ?></p>
            <?php } ?>
            <?php if($team->email) { ?>
              <p><?php echo $this->translate("Email : %s", $team->email); ?></p>
            <?php } ?>
            <?php if($team->address) { ?>
              <p><?php echo $this->translate("Address: %s", $team->address); ?></p>
            <?php } ?>
            <ul class="social-links">
              <?php if($team->facebook) { ?>
                <?php $facebook = (preg_match("#https?://#", $team->facebook) === 0) ? 'http://'.$team->facebook : $team->facebook; ?>
                <li><a href="<?php $facebook; ?>"><i class="fa fa-facebook"></i></a></li>
              <?php } ?>
              <?php if($team->twitter) { ?>
                <?php $twitter = (preg_match("#https?://#", $team->twitter) === 0) ? 'http://'.$team->twitter : $team->twitter; ?>
                <li><a href="<?php echo $twitter; ?>"><i class="fa fa-twitter"></i></a></li>
              <?php } ?>
              <?php if($team->linkdin) { ?>
                <?php $linkdin = (preg_match("#https?://#", $team->linkdin) === 0) ? 'http://'.$team->linkdin : $team->linkdin; ?>
                <li><a href="<?php echo $linkdin ?>"><i class="fa fa-linkedin"></i></a></li>
              <?php } ?>
              <?php if($team->googleplus) { ?>
                <?php $googleplus = (preg_match("#https?://#", $team->googleplus) === 0) ? 'http://'.$team->googleplus : $team->googleplus; ?>
                <li><a href="<?php echo $googleplus ?>"><i class="fa fa-google-plus"></i></a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  <?php $j++; } ?>
<?php } ?>