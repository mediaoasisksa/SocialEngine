<?php if($this->message == 1): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You are not authorized to access this page.');return;?>
    </span>
  </div>
<?php endif;?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="services_browse">
    <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class='services_browse_photo'>
          <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
        </div>
        <div class='services_browse_options'>
          <?php echo $this->htmlLink(array(
            'action' => 'edit',
            'pro_id' => $item->parent_id,
            'ser_id' => $item->getIdentity(),
            'route' => 'sitebooking_service_specific',
            'reset' => true,
          ), $this->translate('Edit Service'), array(
            'class' => 'buttonlink icon_service_edit',
          )) ?>
          
          <?php
            echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service', 'action' => 'delete', 'pro_id' => $item->parent_id,'ser_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Service'), array(
             'class' => 'buttonlink smoothbox icon_service_remove'
           ));
          ?>

          <span class="service_site_<?php echo $item->ser_id?>">
            <?php if($item->enabled == "1"): ?>
              <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service', 'action' => 'disable', 'pro_id' => $item->parent_id,'id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Disable Service'), array(
                 'class' => 'buttonlink smoothbox icon_service_delete service_enable'
               ));
              ?>
            <?php else: ?>
              <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'sitebooking', 'controller' => 'service', 'action' => 'enable', 'pro_id' => $item->parent_id,'id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Enable Service'), array(
                 'class' => 'buttonlink smoothbox icon_service_delete'
               ));
              ?>
            <?php endif; ?>
          </span>

          <script type="text/javascript">

            en4.core.runonce.add(function(){
              $$('.sitebooking_main_provider_manage').getParent().addClass('active');

            });

            function statusChange(pro_id, id, status) {

              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'bookings/providers/'+pro_id+'/services/service-manage',
                data : {
                  format : 'json',
                  status : status,
                  id : id,
                  pro_id : pro_id
                },  

                onSuccess: function(responseJSON, responseText) {

                  if(responseJSON[0].status == "enabled") {
                    var status = "disabled";
                    var p_id = responseJSON[0].id;
                    var pro_id = responseJSON[0].pro_id

                    document.getElementsByClassName('service_site_'+p_id)[0].innerHTML = "<button id = disabled_"+p_id+" onclick = \"statusChange("+pro_id+","+p_id+",'"+ status+"')\">Disable</button>";
                  }

                  if(responseJSON[0].status == "disabled") {
                    var status = "enabled";
                    var p_id = responseJSON[0].id;
                    var pro_id = responseJSON[0].pro_id

                    document.getElementsByClassName('service_site_'+p_id)[0].innerHTML = "<button id = enabled"+p_id+" onclick = \"statusChange("+pro_id+","+p_id+",'"+ status+"')\">Enable</button>";
                  }
                }     
              }));
            }

          </script>    
          <!-- Dashboard -->
        </div>
        <div class='services_browse_info'>
          <span class='services_browse_info_title'>
              <h3 style="padding-top: 0; padding-bottom: 5px;"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
          </span>
          <p class='services_browse_info_date'>
              <?php echo $this->translate('Created');?>
              <?php echo $this->translate('on');?>
              <?php 
              $date1 = date_create($item->creation_date, timezone_open('UTC'));
              $date1 = date_timezone_set($date1, timezone_open($this->timezone));
              echo date_format($date1, 'F j \, Y');              
              ?>
          </p>
          <div class="stat_info">
          <?php if( $item->comment_count > 0 || $item->like_count > 0 ) :?>
            <i class="fa fa-bar-chart"></i>
          <?php endif; ?>
            <?php if( $item->comment_count > 0 ) :?>
              <span>
                <?php echo $this->translate(array('%s Comment', '%s Comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
              </span>
            <?php endif; ?>
            <?php if( $item->like_count > 0 ) :?>
              <span>
                <?php echo $this->translate(array('%s Like', '%s Likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
              </span>
            <?php endif; ?>
            <?php if( $item->view_count > 0 ) :?>
              <span>
                <?php echo $this->translate(array('%s View', '%s Views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              </span>
            <?php endif; ?>
            <?php if($item->type == 1):?>
          <div class="_price"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></div>
          </div>
          <?php else:?>
                    <div class="_price"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo $this->translate('Monthly');?></div>
          </div>
          <?php endif;?>
            <p class='services_browse_info_blurb'>
              <?php $readMore = ' ' . $this->translate('Read More') . '...';?>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 180, $this->htmlLink($item->getHref(), $readMore) ) ?>
            </p>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any Service entries that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not created any service yet.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>

