<?php ?>
<?php if(count($this->photos) > 0) { ?>
  <div class="lp_gallery_section lp_section_box sesbasic_bxs">
    <section id="lp_gallery" class="lp_gallery_box_main sesbasic_clearfix">
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosheading')) { ?>
        <h2 class="wow slideInUp"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosheading', 'Photo Gallery')); ?></h2>
      <?php } ?>
<!--       <div class="tab-buttons wow zoomIn">
           <button class="tab-button tabactive tabcurrent" onclick="openPics(event,'Meetings')">Meetings</button>
           <button class="tab-button tabactive" onclick="openPics(event,'Team')">Team Buildings</button>
           <button class="tab-button tabactive" onclick="openPics(event,'Activities')">Activities</button>
           <button class="tab-button tabactive" onclick="openPics(event,'Architecture')">Architecture</button>
       </div>-->
      <div id="Meetings" class="pics">
        <div class="gallery">
          <?php foreach($this->photos as $photo) { ?>
            <a href="<?php echo $photo->getPhotoUrl(); ?>"><img src="<?php echo $photo->getPhotoUrl(); ?>" class="wow zoomIn" /></a>
          <?php } ?>
        </div>
<!--        <div id="Team" class="pics" style="display:none"></div>
        <div id="Activities" class="pics" style="display:none"></div>
        <div id="Architecture" class="pics" style="display:none"></div>-->
      </div>
    </section>
  </div>
<?php } ?>