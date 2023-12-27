<?php
//SORT STATISTICS:
// 1) SHOW MY REFFERALS:
?>
<script type="text/javascript">
  var InviteMemberPage = Number(<?php echo sprintf('%d', $this->invite_Info->getCurrentPageNumber()) ?>);
  
</script>  
<?php
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/css.php?request=/application/modules/Seaocore/externals/styles/style_invite.css')?>
<?php if (!$this->isajax) : ?>


<div class='seaocore_invite_left'>
	<div class="seaocore_invite_left_title"><?php echo $this->translate('Search Friends');?></div>
  <?php echo $this->form->setAttrib('class', 'seaocore_invite_search_form')->render($this) ?>
</div>

  <div id="show_successmsg">

  </div>

  <?php // SHOW DATA IN TABLE FORMATE:    ?>

  <div class="seaocore_invite_right">
  	<div class="seaocore_invite_statistics_header">
		  <select class="invite_types" id="invite_types">
		    <option value="1"><?php echo $this->translate('My Referrals');?></option> 
		    <option value="0"><?php echo $this->translate('Pending Invites');?></option>
		  </select>
		
		  <select class="invite_types" id="service_types">
		    <option value="all"><?php echo $this->translate('All');?></option>
		    <option value="google"><?php echo $this->translate('Gmail');?></option>
		    <option value="yahoo"><?php echo $this->translate('Yahoo');?></option>
		    <option value="windowlive"><?php echo $this->translate('Windowlive');?></option>
		    <option value="facebook"><?php echo $this->translate('Facebook');?></option>
		    <option value="twitter"><?php echo $this->translate('Twitter');?></option>
		    <option value="linkedin"><?php echo $this->translate('Linkedin');?></option>
<!--		    <option value="aol"><?php echo $this->translate('Aol');?></option>-->
		  </select>
		  
		 	
	  </div>
	  <div id="statistics_info" class="seaocore_invite_statistics_list">
  <?php endif; ?>
  <?php if ($this->invite_Info->count() > 0) : ?>
    <?php if ($this->invite_statistics == 1) : ?>

      <table>
        <thead>
          <tr>
            <th>
              <?php echo $this->translate("User Photo"); ?>
            </th>
            <th>
              <?php echo $this->translate("Display Name"); ?>
            </th>
            <th>
              <?php echo $this->translate("Join Date"); ?>
            </th>
          </tr>
        </thead>
        <?php $viewer = Engine_Api::_()->user()->getViewer();
              $refferal_count = 0; 
        ?>
        <?php foreach ($this->invite_Info as $item): ?>

          <?php $user = Engine_Api::_()->user()->getUser($item['new_user_id']);
                $user_id = $user->getIdentity();
             if (empty($user_id)) continue;
           $refferal_count++;
           ?>	
          <tr>
            <td class="userid"><?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')); ?></td>

            <td class="username">	<?php 
      echo $this->htmlLink(
              $user->getHref(), $user->displayname, array('class' => 'users_browse_photo')
      );
          ?></td>
            <td> <?php echo $this->timestamp(strtotime($user->creation_date)); ?></td>
            <td><?php
        $table = Engine_Api::_()->getDbtable('block', 'user');
        $select = $table->select()
                ->where('user_id = ?', $user->getIdentity())
                ->where('blocked_user_id = ?', $viewer->getIdentity())
                ->limit(1);
        $row = $table->fetchRow($select);
          ?>
              <?php if ($row == NULL): ?>
                <?php if ($this->viewer()->getIdentity()): ?>
                  <div class='browsemembers_results_links'>
                    <?php echo $this->userFriendship($user) ?>
                  </div>
                <?php endif; ?>
              <?php endif; ?></td>
            <?php if ($this->viewer()->membership()->isMember($user)): ?><td>

                <a style="background-image: url(application/modules/Messages/externals/images/send.png);" class="buttonlink" href="<?php echo $this->baseUrl(); ?>/messages/compose/to/<?php echo $user->getIdentity() ?>"> <?php echo $this->translate('Send Message'); ?> </a></td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>  

      </table>

    <?php else : ?>

      <table class='admin_table seaocore_admin_table' width= "100%" >
        <thead>
          <tr>
            <th class='admin_table_short' align="center">
              <?php echo $this->translate("User ID / Email"); ?>
            </th>
            <th class='admin_table_short' align="center">
              <?php echo $this->translate("User Displayname"); ?>
            </th>
            <th align="left">
              <?php echo $this->translate("Provider"); ?>
            </th>
            <th align="left">
              <?php echo $this->translate("Date"); ?>
            </th>

            <th align="left">
              <?php echo $this->translate("Resend"); ?>
            </th>

          </tr>
        </thead>

        <?php foreach ($this->invite_Info as $item): ?>      	
          <tr>
            <td><?php echo!empty($item['social_profileid']) ? $item['social_profileid'] : $item['recipient']; ?></td>
            <td><?php
      if (!empty($item['displayname'])) {
        echo $item['displayname'];
      } else if (!empty($item['social_profileid'])) {
        echo $item['social_profileid'];
      } else {
        echo $item['recipient'];
      }
          ?></td>


            <td> <?php
        if (!empty($item['service']))
          echo ucfirst ($item['service']);
        else
          echo "-----"
            ?>

            </td>
            <td> <?php echo $this->timestamp(strtotime($item['timestamp'])); ?></td>
            <td><a href="javascript:void(0);" onclick="reSendInvite('<?php echo $item['service']; ?>', '<?php echo !empty($item['social_profileid'])?$item['social_profileid']:$item['recipient'] ; ?>')"> <?php echo $this->translate('Resend'); ?></a></td>

          </tr>
        <?php endforeach; ?>  

      </table>





    <?php endif; ?>  
    <?php
  else :
    $msg1 = $this->translate('There are no refferal which you have invited via your this invite service.');
    $msg2 = $this->translate('There are no pending invitations via your this invite service.');?>
    <div class="tip">
    <span>      
      <?php echo $this->invite_statistics == 1 ? $msg1 : $msg2;?> </span>
  </div>
    <?php
  endif;
  
  if (isset($refferal_count) && $refferal_count == 0) {?>
		<div class="tip">
    <span>      
      <?php echo $this->translate('There are no refferal which you have invited via your this invite service.');?> </span>
  </div>
		
		
 <?php }
  ?>
    
    



  <?php if (!$this->isajax): ?>
  </div>

  </div>


  <script type="text/javascript">
    var seao_dateFormat = '<?php echo Engine_Api::_()->getApi('Invite', 'Seaocore')->useDateLocaleFormat();?>';
    var reSendInvite = function (service, profileid) {
      
      var URL = '<?php echo ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->url(array(), 'seaocore_resend_invite', true);?>' + '?socialtype=' + service + '&social_profileid=' + profileid;
      
      Smoothbox.open(URL);
      
      
    }
    en4.core.runonce.add(function()
  {
    en4.core.runonce.add(function init()
    {
      monthList = [];
      myCal = new Calendar({ 'start_cal[date]': 'M d Y', 'end_cal[date]' : 'M d Y' }, {
        classes: ['event_calendar'],
        pad: 0,
        direction: 0
      });
    });
  });
  
  en4.core.runonce.add(function() {

    // check end date and make it the same date if it's too
    cal_starttime.calendars[0].start = new Date( document.getElementById('starttime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);

    cal_starttime_onHideStart();
    // cal_endtime_onHideStart();
  });

  var cal_starttime_onHideStart = function() {  
    var cal_bound_start = scriptJquery('#starttime-date').value
    if(seao_dateFormat == 'dmy') {
      var cal_bound_start = en4.seaocore.covertdateDmyToMdy(scriptJquery('#starttime-date').value);
    }
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( cal_bound_start );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  
  var cal_endtime_onHideStart = function() {
    var cal_bound_end = scriptJquery('#endtime-date').value;
    if(seao_dateFormat == 'dmy') {
      var cal_bound_end = en4.seaocore.covertdateDmyToMdy(scriptJquery('#endtime-date').value);
    }
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( cal_bound_end );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
   
    if(scriptJquery('#endtime-minute').length) {
      scriptJquery('#endtime-minute').css('display', 'none');
    }
    
    if(scriptJquery('#endtime-ampm').length) {
      scriptJquery('#endtime-ampm').css('display', 'none');
    }
    
    if(scriptJquery('#endtime-hour').length) {
      scriptJquery('#endtime-hour').css('display', 'none');
    }
    
    if(scriptJquery('#starttime-minute').length) {
      scriptJquery('#starttime-minute').css('display', 'none');
    }
    
    if(scriptJquery('#starttime-ampm').length) {
      scriptJquery('#starttime-ampm').css('display', 'none');
    }
    
    if(scriptJquery('#starttime-hour').length) {
      scriptJquery('#starttime-hour').css('display', 'none');
    } 
    
    var requestActive = false;
    var browseContainer, formElement, page, totalUsers, userCount, currentSearchParams;

    formElement = scriptJquery('#search_friends');
   
    //browseContainer = scriptJquery('#browsemembers_results');

    // On search
    formElement.on('submit', function(event) { 
      event.stop();
      searchMembers();
    });

    var searchMembers = window.searchMembers = function() { 
       scriptJquery('#statistics_info').html("<div class='seaocore_content_loader'></div>");
      if( requestActive ) return;
      requestActive = true;

      currentSearchParams = formElement.serialize();

      var param = (currentSearchParams ? currentSearchParams + '&' : '') + 'isajax=1&format=html&search=1&invite_statistics=' + scriptJquery('#invite_types').value  + '&invite_service=' + scriptJquery('#service_types').value;
      
      var request = scriptJquery.ajax({
        url: en4.core.baseUrl + 'widget/index/mod/seaocore/name/seaocores-invitestatistics',
        success : function(responseHTML) { 
         scriptJquery('#statistics_info').html(responseHTML)  ;    
         requestActive = false;
         Smoothbox.bind();
        }
      });
      request.send(param);
    }
    
      
    //SHOW EITHER ACCEPTED INVITATIONS OR PENDING INVITATIONS.
    scriptJquery('#invite_types').on('change', function () { 
      
      scriptJquery('#statistics_info').html("<div class='seaocore_content_loader'></div>");     
    	currentSearchParams = formElement.serialize();      
      var postData = {
        'invite_statistics' : this.value,
        'invite_service': scriptJquery('#service_types').val(),
        'isajax': true,      
        'format': 'html'
      };
    	
      en4.core.request.send(scriptJquery.ajax( {
        url : en4.core.baseUrl + 'widget/index/mod/seaocore/name/seaocores-invitestatistics?' + currentSearchParams,      
        data : postData,
        success : function(responseHTML)
        { 
          scriptJquery('#statistics_info').html(responseHTML);
        }
      }), {
        'force':true
      });
      
      
    });
      
    //SHOW THE INVITATIONS ACCRODING TO SERVICES:
      
    scriptJquery('#service_types').on('change', function () { 
      
      scriptJquery('#statistics_info').html("<div class='seaocore_content_loader'></div>");
      currentSearchParams = formElement.serialize();

      var postData = {
        'invite_statistics' : scriptJquery('#invite_types').val(),
        'invite_service': this.value,
        'isajax': true,      
        'format': 'html'
      };
    	
      en4.core.request.send(scriptJquery.ajax( {
        url : en4.core.baseUrl + 'widget/index/mod/seaocore/name/seaocores-invitestatistics?' + currentSearchParams,      
        data : postData,
        success : function(responseHTML)
        { 
          scriptJquery('#statistics_info').html(responseHTML);
        }
      }), {
        'force':true
      });
      
      
    });


  </script>
  
  
  <script type="text/javascript">
  var InviteMemberPage = Number(<?php echo sprintf('%d', $this->invite_Info->getCurrentPageNumber()) ?>);
  
  en4.core.runonce.add(function() {
    var url = en4.core.baseUrl + 'widget/index/mod/seaocore/name/seaocores-invitestatistics';
    scriptJquery('#group_members_search_input').on('keypress', function(e) {
      if( e.key != 'enter' ) return;

      en4.core.request.send(scriptJquery.ajax({
        'url' : url,
        'data' : {
          'format' : 'html',
          'subject' : en4.core.subject.guid,
          'search' : this.value
        }
      }), {
        'element' : scriptJquery('#group_profile_members_anchor').parent()
      });
    });
});
  var paginateInviteMembers = function(page) { 
    scriptJquery('#statistics_info').html("<div class='seaocore_content_loader'></div>");
    var url = en4.core.baseUrl + 'widget/index/mod/seaocore/name/seaocores-invitestatistics?' + currentSearchParams;
    currentSearchParams = formElement.serialize();
    en4.core.request.send(scriptJquery.ajax({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'invite_statistics' : scriptJquery('#invite_types').val(),
        'invite_service': scriptJquery('#service_types').val(),
        'isajax': true, 
        //'search' : groupMemberSearch,
        'page' : page
        //'waiting' : waiting
      }
    }), {
      'element' : scriptJquery('#statistics_info')
    });
  }
</script>

<?php endif; ?>

 <?php if( $this->invite_Info->count() > 1 ): ?>
    <div>
      <?php if( $this->invite_Info->getCurrentPageNumber() > 1 ): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginateInviteMembers(InviteMemberPage - 1)',
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->invite_Info->getCurrentPageNumber() < $this->invite_Info->count() ): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginateInviteMembers(InviteMemberPage + 1)',
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
