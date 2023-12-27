<?php if($this->approved):?>
<?php
$todayDate = $date = date("Y-m-d");
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
<!-- Mentor Subscriptions-->
    <?php 
    
    if(!$this->dataMentor): ?>
<div class="sesbasic_bxs profile_mentor_subscriptions">
	<div class="profile_tab_head">
		<h2><?php echo $this->translate("Mentor Subscriptions");?></h2>
	</div>
	<div class="profile_mentor_subscriptions_listing">
		        <?php $subsribe=0; foreach( $this->paginator as $item ) : ?>
    		     <?php  $itemArray = $item->toArray(); $parent_id = $itemArray['parent_id'];
                    $params = array();
                    $params['ser_id'] = $item->getIdentity(); $params['groupby'] = 1;
                    $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
                ?>
                <?php foreach($bookedItems as $bookingItem):?>
                    <?php $user = Engine_Api::_()->getItem('user', $bookingItem->user_id);?>
                    
                    <?php if($user->getIdentity() == Engine_Api::_()->user()->getViewer()->getIdentity()):?>
                   
                    <?php $subsribe = $subsribe+1;?>
                        <div class="profile_mentor_subscriptions_item">
                        <article>
                            <div class="_left">
                                <div class="itemthumb">
                                    <span class="_userimg"><img src="<?php echo $item->getOwner()->getPhotoUrl() ?  $item->getOwner()->getPhotoUrl() : '/upgrade/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></span>
                                    
                                    
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
                                    <span class="category custom_tag"><a href="/pages/mentor-service?category_id=<?php echo $item->category_id;?>"><?php echo $this->translate(Engine_Api::_()->getItemTable('sitebooking_category')->getCategoryName($item->category_id));?></a></span>
                                    <?php if(strtotime($bookingItem->servicing_end_date) > strtotime(date("Y-m-d"))):?>
                                    <div class="_subcription">
                                        <span><?php echo $this->translate("Subscription:");?></span>
                                        <span class="_active"><?php echo $this->translate("Active");?></span>
                                    </div>
                                    <div class="_valid">
                                        <span class="sesbasic_text_light"><?php echo $this->translate("Valid till:");?></span>
                                        <span class="_date"><?php echo $bookingItem->servicing_end_date;?></span>
                                    </div>
                                    <?php else:?>
                                     <div class="_subcription">
                                        <span><?php echo $this->translate("Subscription:");?></span>
                                        <span class="_expired"><?php echo $this->translate("Expired");?></span>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </div>
                            <div class="_right">
                                <div class="_rightcont">
                                    <div class="_icon">
                                        <i class="_zoom"></i>
                                    </div>
                                    <!--<div class="_rightinfo">-->
                                    <!--    <div class="_status"><b><?php echo $this->translate("Today:")?></b> <span class="_online"><?php echo $this->translate("Online")?></span></div>-->
                                    <!--    <div class="_time"><b><?php echo $this->translate("Meeting Time:")?></b> <span><?php  $dd = json_decode($bookingItem->duration, true);?> <?php echo $dd[$bookingItem->servicing_date];?></span></div>-->
                                    <!--</div>-->
                                    
                                        <?php
                          
                          $serviceDate = $bookingItem->servicing_date;
                          $serviceEndDate = $bookingItem->servicing_end_date;
                          $showLink = false; 
                          $showMessage = false;
                          if(strtotime($todayDate) > strtotime($serviceEndDate)) { 
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
                                    <!--<span class="_expired">-->
                                    <!--    <?php echo $this->translate($showMessage);?>-->
                                    <!--</span>-->
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
                            <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting2('<?php echo $bookingItem->servicebooking_id;?>', 'attendee')"><span><?php echo $this->translate($showMessage)?></span></a></div>
                          <?php endif;?>  
                                         
                                         
                                         <!--<a href="javascript:void(0);" class="sesbasic_animation custom_btn custom_btn_primary"><span>Join Meeting</span></a>-->
    `                                       
                                              
                                </div>
                            </div>
                        </article>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
		     <?php endforeach;?>
		     <?php if($subsribe == 0):?>
		        <div class="tip"><span style="margin-left: 12px;"><?php echo($this->translate("You don't have any subscription yet."));?></span></div>
		     <?php endif;?>
	</div>
</div>
<?php endif;?>

<!--Mentor Subscribers-->


<div class="sesbasic_bxs profile_mentor_subscribers">

	<?php 
    
    if($this->dataMentor && Engine_Api::_()->user()->getViewer()->getIdentity() == $this->dataMentor['owner_id']): ?>
	<div class="profile_tab_head">
		<h2><?php echo $this->translate("Mentor Subscribers");?></h2>
	</div>
	<div class="profile_mentor_subscribers_listing">
	    
	    <?php foreach( $this->paginator as $item ) : ?>
	    
                <?php
                    $itemArray = $item->toArray();
                    $parent_id = $itemArray['parent_id']; $viewer = Engine_Api::_()->user()->getViewer();

                    
                ?>
                    <?php 
                                        $params = array();
                                        $params['ser_id'] = $item->getIdentity();
                                        
                                        $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator($params);
                                        
                                    ?>
                          <?php if($bookedItems->getTotalItemCount() > 0 ):?>            
                          <?php foreach($bookedItems as $itemBook): ?>
                          
                          <div class="profile_mentor_subscribers_item">
                            <article>
                                <div class="_left">
                                    <div class="_leftcont">
                                         <?php
                          $serviceDate = $itemBook->servicing_date;
                          $serviceEndDate = $itemBook->servicing_end_date;
                          $duration = $itemBook->duration;
                          $dd = json_decode($itemBook->duration, true); 
                          $timeD  = $dd[$itemBook->servicing_date];
                          $showLink = false; 
                          $showMessage = false;
                         
                          if(strtotime($todayDate) > strtotime($serviceEndDate)) {
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
                            <div class="_btn"><a class="sesbasic_animation custom_btn custom_btn_primary" style="cursor: pointer;" onclick="startMeeting2('<?php echo $itemBook->servicebooking_id;?>', 'moderator')"><span><?php echo $this->translate("Start Meeting")?></span></a></div>
                          <?php endif;?>	
                                      
                                    </div>
                                </div>
                                <div class="_right">
                                    <div class="_subscriberslisting">
                                        
                                        <?php 
                                        $params = array();
                                        $params['ser_id'] = $item->getIdentity();
                                        
                                        $bookedItems = Engine_Api::_()->getItemTable('sitebooking_servicebooking')->getBookingsPaginator1($params);
                                        
                                    ?>
                                        <?php foreach($bookedItems as $itemBook): ?>
                                        <?php
                                        
                                         $duration = $itemBook->duration;
                                          $dd = json_decode($itemBook->duration, true); 
                                          $timeDD  = $dd[$itemBook->servicing_date];
                                          ?>
                                          <?php if( $timeDD == $timeD):?>
                                        <?php $user = Engine_Api::_()->getItem('user', $itemBook->user_id); ?>
                                        <div class="_subscriberitem <?php echo $itemBook->servicebooking_id;?>">
                            							 <div class="_thumb">
                                                            <a href="<?php echo $user->getHref()?>"><img src="<?php echo $user->getPhotoUrl() ? $user->getPhotoUrl() : '/upgrade/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></a>
                                                        </div>
                            						    <div class="_info">
                                                            <?php $fname='';$lname='';
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
                                                                
                                                                if(!$fname && !$lname) {
                                                                 $name = $user->getTitle();
                                                                } else {
                                                                $name = $fname . ' ' . $lname; 
                                                                }
                                                            ?>
                                                            <div class="_name"><a href="<?php echo $user->getHref();?>"><?php echo $name;?></a></div>
                                                            <?php
                                                            if($itemBook->servicing_date > date("Y-m-d")) {
                                                                $date1=date_create($itemBook->servicing_date);
                                                            } else {
                                                                $date1=date_create(date("Y-m-d"));
                                                            }
                                                            
                                                            $date2=date_create($itemBook->servicing_end_date);
                                                            $diff=date_diff($date2,$date1);
                                                            $daysLeft = $diff->format("%R%a days");
                                                            $daysLeft = str_replace('-', ' ', $daysLeft);
                                                            $daysLeft = str_replace('+', ' ', $daysLeft);
                                                            ?>
                                                            <div class="_days"><?php echo str_replace('-', ' ', $daysLeft);?> left</div>
                                                        </div>
                            						</div>
                            						<?php endif;?>
                                        <?php endforeach;?>
                                        
                                        
                                        
                                        
                                        <?php 
                                            foreach(array_reverse($zoomMeetingUrl) as $key => $value){
                                                if( $value->servicebooking_id == $itemBook->servicebooking_id ){ ?>
                                                
                                                <?php foreach(json_decode($value->user_ids, true) as $key => $id1):?>
                                                <?php $user = Engine_Api::_()->getItem('user', $id1); ?>
                                                    <div class="_subscriberitem <?php echo $itemBook->servicebooking_id;?>">
                            							 <div class="_thumb">
                                                            <a href="<?php echo $user->getHref()?>"><img src="<?php echo $user->getPhotoUrl() ? $user->getPhotoUrl() : '/upgrade/application/themes/sescompany/images/nophoto_user_thumb_profile.png';?>" alt=""></a>
                                                        </div>
                            						    <div class="_info">
                                                            <?php $fname='';$lname='';
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
                                                                
                                                                if(!$fname && !$lname) {
                                                                 $name = $user->getTitle();
                                                                } else {
                                                                $name = $fname . ' ' . $lname; 
                                                                }
                                                            ?>
                                                            <div class="_name"><a href="<?php echo $user->getHref();?>"><?php echo $name;?></a></div>
                                                            <?php
                                                            if($itemBook->servicing_date > date("Y-m-d")) {
                                                                $date1=date_create($itemBook->servicing_date);
                                                            } else {
                                                                $date1=date_create(date("Y-m-d"));
                                                            }
                                                            
                                                            $date2=date_create($itemBook->servicing_end_date);
                                                            $diff=date_diff($date2,$date1);
                                                            $daysLeft = $diff->format("%R%a days");
                                                            $daysLeft = str_replace('-', ' ', $daysLeft);
                                                            $daysLeft = str_replace('+', ' ', $daysLeft);
                                                            ?>
                                                            <div class="_days"><?php echo str_replace('-', ' ', $daysLeft);?> left</div>
                                                        </div>
                            						</div>
                                                <?php endforeach;?>
                                        <?php
                                        }
                                        }
                                         ?>
                                         
                                         
                                    </div>
                                </div>
                            </article>
                           </div>
                         <?php endforeach;?>
                         <?php else:?>
                         <div class="tip">
                                <span>
                                    <?php echo $this->translate("You don't have booking requests yet.");?>
                                </span>
                            </div>
                         <?php endif;?>
        <?php endforeach;?>
	    
</div>
<?php endif;?>
</div>
<?php else:?>
   <div class="tip">
            <span>
                 <?php echo $this->translate("Your zoom account yet to be approved. Please contact with Admin.");?>
            </span>
        </div>

<?php endif;?>

  
