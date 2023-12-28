<?php ?>
<?php if($this->clients->getTotalItemCount() > 0) { ?>
  <?php $bgImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsbgimage', ''); ?>
  <div class="lp_client_section lp_section_box sesbasic_bxs">
    <section id="lp_client" class="lp_client_box_main sesbasic_clearfix" style="background-image: url(<?php echo $bgImage ?>);">
      <h2 class="wow animated fadeInUp"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsheading', 'Our Clients')); ?></h2>
      <div class="lp_client_row sesbasic_clearfix">
        <ul>
          <?php $i= 0; ?>
          <?php foreach($this->clients as $client) { ?>
            <?php $datadelay = 0.3 * $i; ?>
            <li class="wow animated fadeInUp" data-wow-delay="<?php echo $datadelay ?>s">
              <a href="<?php echo $client->client_link; ?>">
                <?php if($client->file_id) { ?>
                  <?php $item = Engine_Api::_()->getItem('sescompany_client', $client->client_id); ?>
                  <img src="<?php echo $item->getFilePath('file_id'); ?>" />
                <?php } ?>
              </a>
              <div class="lp_client_tooltip">
                <p><?php echo $client->client_name; ?></p>
              </div>
            </li>
          <?php $i++; } ?>
        </ul>
      </div>
    </section>
  </div>
<?php } ?>