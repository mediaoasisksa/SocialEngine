<section class="Section-Constants-All">
      <div class="container">
        <div class="enwan-constants">
          <h2><?php echo $this->translate("selection of mentors");?></h2>
          <div class="khat-cons"></div>
          <p>
            <?php echo $this->translate("Attending monthly consulting meetings with a selection of mentors");?>
          </p>
        </div>
        <div class="Box-Constants-All">
          <?php foreach( $this->paginator as $item ) : ?>
        
            <div class="Box-Constants">
              <!-- <img src="application/modules/Customtheme/externals/images/img1.1-Constants.png" alt="" /> -->
              <?php $url = $item->getPhotoUrl(); $url = $url ? "." . $url : '/application/themes/sescompany/images/nophoto_user_thumb_profile.png'?>
              <div class="img-contantss-1 img-contantss" style="background-image: url('<?php echo $url;?>')">
                <div class="link-in-bg"> 
                  
                </div>
                </div>
                 <div class="Text-Contants-All">
                <div class="small-image">
                  <img src="<?php echo $item->getPhotoUrl('thumb.icon');?>" alt=""/>
                  <?php //echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
                </div>
                <div class="text-Box-Contants">
                  <br />
                  <ul>
                      <li><h5><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h5></li>
                      <?php if($item->jobtitle):?>
                     <li> <b><?php echo $this->translate("JobTitle: ");?></b> <?php echo $item->jobtitle;?></li>   <br />
                     <?php endif;?>
                     <?php if($item->qualifications):?>
                    <li><b><?php echo $this->translate("Qualifications: ");?></b> <?php echo $item->qualifications;?> </li>  <br />
                    <?php endif;?>
                    <?php if($item->history):?>
                    <li><b><?php echo $this->translate("History: ");?></b> <?php echo $item->history;?></li>  <br />
                    <?php endif;?>
                    <?php if($item->file_id):?>
                    <?php $storage = Engine_Api::_()->getItem('storage_file', $item->file_id);?>
                    <li><b><?php echo $this->translate("CV: ");?></b> <a target="_blank" style="
    color: #c7002b;
" href="/<?php echo Engine_Api::_()->storage()->get($item->file_id, '')->storage_path;?>">Preview</a> | <a target="_blank" style="
    color: #c7002b;
" download href="/<?php echo Engine_Api::_()->storage()->get($item->file_id, '')->storage_path;?>">Download</a></li> 
                    <?php endif;?>
                    </ul>
                    
                    <?php if($this->viewer()->getIdentity() != $item->user_id): ?>
                  <button onclick="window.location.href='/payment/settings'">Subscribe Me <i class=""></i></button>
                  <?php else:?>
                  <button>
                    Contact Me <i class=""></i>
                  </button>
                  <?php endif;?>
                </div>
              </div>
              </div>
              
          <?php endforeach;?>
        </div>
        <!--<div class="View-More">-->
        <!--  <button>View More</button>-->
        <!--</div>-->
        
    </section>
    <!-- end Selection Of Consulatants -->
