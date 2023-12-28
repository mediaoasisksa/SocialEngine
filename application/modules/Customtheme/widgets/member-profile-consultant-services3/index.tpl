
<?php if($this->approved):?>
    <?php
    $date = $todayDate = date("Y-m-d");
    $day = strtolower(substr(date("l", strtotime($date)),0,3));
    $days[$date] = $day;
    $timeFrameValue = number_format(1)*1;
    for($i = 0;$i < $timeFrameValue-1; $i++){
    $date = date('Y-m-d', strtotime($date. ' + 1 day'));
    $day = strtolower(substr(date("l", strtotime($date)),0,3));
    $days[$date] = $day;
    }
    
    $this->duration = $duration = 1800;
    $date = date_create('2001-01-01');
    $time = array();
    date_time_set($date, 00, 00);
    $noOfTimeSlots = 86400/$duration;
    $timeFormateForUI = 'H:i';
    $timeFormate = 'H:i';
    for($i = 0;$i<$noOfTimeSlots;$i++)
    {
      $d1 = date_format($date,$timeFormate);
      date_add($date, date_interval_create_from_date_string($duration." seconds"));
      $time[] = $d1;
    }
    
    for($i = 0;$i<24;$i++)
    {
      $d1 = date_format($date,$timeFormate);
      date_add($date, date_interval_create_from_date_string("3600 seconds"));
      $timeOptions[] = $d1;
    }
    ?>
    <?php 
        
        if( !$this->dataConsulant): ?>
    <!-- Consultant Appointment-->
    <div class="sesbasic_bxs profile_mentor_consultant_appointment_main">
    	<div class="profile_tab_head">
    		<h2><?php echo $this->translate("Consultant Appointment");?></h2>
    	</div>
    	<div class="profile_mentor_consultant_appointment">
    
            <?php $subsribe=0; foreach( $this->paginator as $item ) : ?>
        		     <?php  $itemArray = $item->toArray(); $parent_id = $itemArray['parent_id'];
                        $params = array();
                        $params['ser_id'] = $item->getIdentity();
                        $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
                    ?>
                    <?php foreach($bookedItems as $bookingItem):?>
                        <?php $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);?>
                        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() == $bookingItem->user_id): $subsribe=$subsribe+1;?>
                        <div class="profile_mentor_consultant_appointment_item">
                            <article>
                                <div class="itemthumb">
                                        <span class="_userimg"><img src="<?php echo $item->getOwner()->getPhotoUrl() ? $item->getOwner()->getPhotoUrl() : '/upgrade/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></span>
    
                                        
                                        <?php
                                            $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($item->getOwner());
                                            //print_r($values->toArray());die;
                                            // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
                                            foreach($values->toArray() as $value) {
                                                if($value['field_id'] == 36) {
                                                    $fname = $value['value'];
                                                } else if($value['field_id'] == 47) {
                                                    $fname = $value['value'];
                                                } elseif($value['field_id'] ==37) {
                                                    $lname = $value['value'];
                                                } else if($value['field_id'] == 48) {
                                                    $lname = $value['value'];
                                                }
                                            }
                                        ?>
                        				<span class="_username"><a href="<?php echo $item->getOwner()->getHref();?>"><?php echo $this->htmlLink($item->getOwner()->getHref(), $fname . ' ' . $lname) ?></a></span>
                                </div>
                                <div class="iteminfo">
                                    <span class="category custom_tag"><a href="/bookings/services/home?category_id=<?php echo $item->category_id;?>"><?php echo $this->translate(Engine_Api::_()->getItemTable('sitebooking_category')->getCategoryName($item->category_id));?></a></span>
                                    <!--<div class="_date">-->
                                    <!--    <?php echo $bookingItem->servicing_date;?>-->
                                    <!--</div>-->
                                    <!--<div class="_time"><?php echo $this->translate("Meeting Time:");?> <?php  $dd = json_decode($bookingItem->duration, true);?> <?php echo $dd[$bookingItem->servicing_date];?></div>-->
                                	
                                	
                                	<?php
                          
                          $serviceDate = $bookingItem->servicing_date;
                          $showLink = false; 
                          $showMessage = false;
                          if($todayDate > $serviceDate) { 
                                $showLink = false; 
                                $showMessage = "Meeting Expired";
                          } else if($todayDate == $serviceDate) {
                                $showLink = true; 
                                $showMessage = "Join Meeting";
                          } else { 
                               $showLink = false; 
                               $showMessage = "Upcoming Meeting";
                          }
                          ?>
                                                
                          <?php if(!$showLink):?>
                            <div class="_right">
                                <div class="_rightcont">
                                    <div class="_subcription _txt tip">
                                    <!--<span class="_expired">-->
                                    <!--    <?php echo $this->translate($showMessage);?>-->
                                    <!--</span>-->
                                      <div class="_date">
                                            <b><?php echo $this->translate("Meeting Date:");?></b> <?php echo $serviceDate; ?>
                                        </div>
                                    <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary disabled" style="cursor: pointer;"><span><?php echo $this->translate($showMessage)?></span></a></div>

                                    </div>
                                </div>
                            </div>
                          <?php else:?>
                            <div class="_date">
                                <b><?php echo $this->translate("Meeting Date:");?></b> <?php echo $serviceDate; ?>
                            </div>
                            <div class="_time" style="display:none;"><b><?php echo $this->translate("Meeting Time:");?></b>
                                    <?php //echo $serviceDate; ?>
                            </div>
                            <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting2('<?php echo $bookingItem->servicebooking_id;?>', 'attendee')"><span><?php echo $this->translate($showMessage)?></span></a></div>
                          <?php endif;?>                
                        </div>
                        </article>
                        </div>
                               
            		    <?php endif;?>
                    <?php endforeach;?>
            <?php endforeach;?>
    		<?php if($subsribe == 0):?>
    		        <div class="tip"><span style="margin-left: 12px;"><?php echo($this->translate("You don't have any booking yet."));?></span></div>
    		     <?php endif;?>
    		
    	</div>
    </div>
    <?php endif;?>
    
    	<?php 
        
        if( $this->dataConsulant && Engine_Api::_()->user()->getViewer()->getIdentity() == $this->dataConsulant['owner_id']): ?>
    <!-- My Appointment-->
    <div class="sesbasic_bxs profile_mentor_consultant_appointment_main">
    	<div class="profile_tab_head">
    		<h2><?php echo $this->translate("My Appointment");?></h2>
    	</div>
    	<div class="profile_mentor_consultant_appointment">
    	    
            <?php foreach( $this->paginator as $item ) : ?>
    		     <?php  $itemArray = $item->toArray(); $parent_id = $itemArray['parent_id'];
                    $params = array();
                    $params['ser_id'] = $item->getIdentity();
                    $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
                ?>
                    <?php if($bookedItems->getTotalItemCount() > 0):?>
                    <?php foreach($bookedItems as $bookingItem):?>
                        
                        <?php $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);?>
                        <div class="profile_mentor_consultant_my_appointment_item">
                        <article>
                        <div class="itemthumb">
                        <a href="<?php echo $user->getHref();?>"><span class="_userimg"><img src="<?php echo $user->getPhotoUrl() ? $user->getPhotoUrl() : '/upgrade/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></span></a>
                        </div>
                        <div class="iteminfo">
                        <div class="_username">
                            <?php
                                            $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($user);
                                            //print_r($values->toArray());die;
                                            // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
                                            foreach($values->toArray() as $value) {
                                                if($value['field_id'] == 36) {
                                                    $fname = $value['value'];
                                                } else if($value['field_id'] == 47) {
                                                    $fname = $value['value'];
                                                } elseif($value['field_id'] ==37) {
                                                    $lname = $value['value'];
                                                } else if($value['field_id'] == 48) {
                                                    $lname = $value['value'];
                                                }
                                            }
                                        ?>
                        <a href="<?php echo $user->getHref();?>"><?php if($fname):?><?php echo $fname . ' ' . $lname;?><?php else:?><?php echo $user->getTitle();?><?php endif;?></a>
                        </div>
                        <!--<div class="_type sesbasic_text_light">-->
                        <!--<b>Trainee</b>-->
                        <!--</div>-->
                        <!--<div class="_time">-->
                        <!--	<span><?php echo $this->translate("Meeting Time:");?></span>-->
                        <!--	<span><?php echo $bookingItem->servicing_date;?></span>-->
                        <!--	<span><?php  $dd = json_decode($bookingItem->duration, true);?> <?php echo $dd[$bookingItem->servicing_date];?></span>-->
                        <!--</div>-->
            
                          <?php
                          
                          $serviceDate = $bookingItem->servicing_date;
                          $showLink = false; 
                          $showMessage = false;
                          if($todayDate > $serviceDate) { 
                                $showLink = false; 
                                $showMessage = "Meeting Expired";
                          } else if($todayDate == $serviceDate) {
                                $showLink = true; 
                                $showMessage = "Join Meeting";
                          } else { 
                               $showLink = false; 
                               $showMessage = "Upcoming Meeting";
                          }
                          ?>
                                                
                          <?php if(!$showLink):?>
                            <div class="_right">
                                <div class="_rightcont">
                                    <!--<div class="_subcription _txt tip">-->
                                    <!--<span class="_expired">-->
                                    <!--                <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting2('<?php echo $bookingItem->servicebooking_id;?>', 'moderator')"><span><?php echo $this->translate("Start Meeting")?></span></a></div>-->

                                    <!--</span>-->
                                    <!--</div>-->
                                      <div class="_date">
                                            <b><?php echo $this->translate("Meeting Date:");?></b> <?php echo $serviceDate; ?>
                                        </div>
                                <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary disabled" style="cursor: pointer;"><span><?php echo $this->translate($showMessage)?></span></a></div>

                                </div>
                            </div>
                          <?php else:?>
                            <div class="_date">
                                <b><?php echo $this->translate("Meeting Date:");?></b> <?php echo $serviceDate; ?>
                            </div>
                            <div class="_time" style="display:none;"><b><?php echo $this->translate("Meeting Time:");?></b>
                                    <?php //echo $serviceDate; ?>
                            </div>
                            <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting2('<?php echo $bookingItem->servicebooking_id;?>', 'moderator')"><span><?php echo $this->translate("Start Meeting")?></span></a></div>
                          <?php endif;?>
                                                 
                        </div>
                        </article>
                        </div>
                        
                        
                <?php endforeach;?>
                <?php else:?>
                    <div class="tip">
                    <span style="margin-left: 12px;">
                        <?php echo $this->translate("There is no meeting setup yet.");?></span>
                    </span>
                    </div>
                    <?php endif;?>
            <?php endforeach;?>
    	</div>
    </div>
    <?php endif;?>
    

    <?php else:?>
    
        <div class="tip">
            <span>
                 <?php echo $this->translate("Your zoom account yet to be approved. Please contact with Admin.");?>
            </span>
        </div>
    <?php endif;?>
    
        
      
    <script>
      
    function startMeeting(url){
      window.open(url,'_blank');
    }
    
    function startMeeting2(id, role){
       scriptJquery.ajax({
                url: '<?php echo $this->url(array('module' => 'customtheme', 'controller' => 'index', 'action' => 'start-meeting'), 'default', true) ?>',
                method: 'post',
                'data' : {
                    'servicebooking_id' : id,
                    'role': role,
                    'format' : 'json'
                },  
                success: function(responseJSON){
                    console.log(JSON.parse(responseJSON));
                    var r = JSON.parse(responseJSON);
                    if(r.success == true) {
                        window.open(r.message,'_blank');
                    } else {
                        alert(r.message);
                    }
                    
                }
            });
       
    }
    </script>
    
    <style>
        a.sesbasic_animation.custom_btn.custom_btn_primary.disabled {
    color: currentColor;
    cursor: not-allowed;
    opacity: 0.5;
    text-decoration: none;
}
    </style>